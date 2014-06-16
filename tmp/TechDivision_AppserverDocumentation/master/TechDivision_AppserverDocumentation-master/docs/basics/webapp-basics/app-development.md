# App Development
This is a "getting started" tutorial for everyone who wants to learn about the appserver speciality: servlets.
It will guide you through setting up your first webapp, which serves HTTP requests. All necessary steps are explained in
detail. It is assumed that you already installed the appserver as described [here](<../../getting-started/installation.md>).

## Let's get started
We have a webapp stub which you can use for this tutorial. You can get it using composer by executing the following command:

```bash
composer.phar create-project techdivision/techdivision_applicationserverproject myfirstapp dev-master
```
Alternatively you can download it from GitHub directly [here](<https://github.com/techdivision/TechDivision_ApplicationServerProject>).
You should copy the `src` directory of the app to `/opt/appserver/webapps` and run `composer update` in it.
Or more elegantly, use ant to deploy it to your appserver installation via the following command within the app directory:

```bash
ant deploy
```

*myfirstapp* is the name of the webapp, it is necessary to call it by url. If you haven't already started the appserver
do it now by typing using the appropriate restart commands as described [here](<../../getting-started/basic-usage.md#start-and-stop-scripts>).

By default the appserver is running on port 9080. Therefore head over to ``127.0.0.1:9080/myfirstapp/demo.do``. Notice the webapp
name in the url, if you have chosen something else as the name, use it instead of *myfirstapp*.
The basic app stub should be visible.

![myfirstapp landing page](../../images/myfirstapp.png)

Let's look into some source code to get to know where the `.do` comes from. Open up your webapps folder ``webapps/myfirstapp``
in your favourite editor. You will see that the structure of the web application is similar to webapp structures of [Tomcat](<http://en.wikipedia.org/wiki/Apache_Tomcat>) or WildFly. 
Open ``WEB-INF/web.xml``. This is the configuration file for your webapp's routes which contains servlets and their mapping to URIs.
A servlet can be defined as follows:

```xml
<servlet>
	<description><![CDATA[A demo servlet]]></description>
    <display-name>DemoServlet</display-name>
    <servlet-name>DemoServlet</servlet-name>
    <servlet-class>\TechDivision\ApplicationServerProject\Servlets\DemoServlet</servlet-class>
</servlet>
```

There you define the servlet name and map it to a servlet class defined by a namespace. If you open ``WEB-INF/classes``
you will find the defined servlet. This servlet can now be used in a route mapping like the following.


```xml
<servlet-mapping>
   	<servlet-name>DemoServlet</servlet-name>
    <url-pattern>/*</url-pattern>
</servlet-mapping>
```

This means that the servlet `DemoServlet` is mapped to every URI (or PATH_INFO if your app is not your document root) and will therefor handle every request to your app.
So why the `.do`? As you will learn [later](<../appserver-basics/architecture.md>) the appserver's work-horse is the *Server* component. And simply put: the server responsible for http requests needs to know when it has to handle servlets.
So consider the `.do` a, in this case imaginary, file extension like .php or .html.

So try some other URI patterns here e.g. `/demo.do*`, restart the appserver and test them in the browser's url bar. It will always call the
same servlet which delivers the same content. Let's inspect the corresponding servlet class by opening
``WEB-INF/webapps/classes/TechDivisioon/ApplicationServerProject/Servlets/DemoServlet.php``. The servlet inherits from ``HttpServlet`` as
it conforms to the HTTP 1.1 protocol. For every method of this protocol a method is provided by this class which can
be overridden. Most of the time you will use ``doGet()`` or ``doPost()`` for GET and POST methods. Let's inspect the
``doGet()`` in detail.

```php
public function doGet(HttpServletRequest $req, HttpServletResponse $res)
{
  	// build path to template
    $pathToTemplate = $this->getServletConfig()->getWebappPath() .
      	DIRECTORY_SEPARATOR . 'static' .
       	DIRECTORY_SEPARATOR . 'templates' .
       	DIRECTORY_SEPARATOR . 'layout.phtml';

    // init template
    $template = new DemoTemplate($pathToTemplate);

   	$baseUrl = '/';
    // if the application has NOT been called over a vhost
    // configuration append application folder naem
    if (!$this->getServletConfig()->getApplication()->isVhostOf($req->getServerName())) {
     	$baseUrl .= $this->getServletConfig()->getApplication()->getName() . '/';
    }

    // set vars in template
    $template->setBaseUrl($baseUrl);
    $template->setRequestUri($req->getUri());
    $template->setUserAgent($req->getHeader("User-Agent"));
    $template->setWebappName($this->getServletConfig()->getApplication()->getName());

    // set response content by render template
   	$res->appendBodyStream($template->render());
}
```

First the path to the template is built, afterwards the template is constructed. The template needs some data to display,
which is set by several methods. The last line of the method sets the response content, which is sent back to the
client.
You can of course use your own template functionalities, engines or build a webservice on this base if you want. This template class is just a simple approach for demonstration purposes.

Please note that you only have to handle requests against servlets this way! Every other file, like images or other static content, will be delivered by the server automatically.

## Let's build something CRUDish

You already learned how to configure and create a servlet, which is conform to the HTTP protocol and can deliver content
to the client. Now it is time to dive deep into the structure of the appserver. As a first webapp we will build something
CRUDish, which involves data handling. You would normally do this with a database. But why implement a database layer in your app when the server can do that for you? As we use the appserver we have much more advantages. 
The appserver comes along with a persistence container. With this architecture, your webapp is scalable, as containers, which contain servers, can scale. You will learn by this tutorial how this works in detail.

So let's build a little system which can save customers. After creating a new customer, we can view them again. The first
step is to build a little form which takes the users input data and send it to the server. Therefore, we head over to
``WEB-INF/web.xml`` and add our route for this form.

```xml
<servlet>
  	<description><![CDATA[A customer servlet]]></description>
   	<display-name>CustomerServlet</display-name>
   	<servlet-name>CustomerServlet</servlet-name>
   	<servlet-class>\TechDivision\ApplicationServerProject\Servlets\CustomerServlet</servlet-class>
</servlet>

<servlet-mapping>
  	<servlet-name>CustomerServlet</servlet-name>
   	<url-pattern>/customer.do*</url-pattern>
</servlet-mapping>
```

The customer servlet is now callable via the route ``/customer.do``. But before we do so, let's create the servlet. It is a
class in the path ``WEB-INF/classes/TechDivision/ApplicationServerProject/Servlets`` (if you did not change it within the ``web.xml``).

```php
namespace TechDivision\ApplicationServerProject\Servlets;
   
use TechDivision\Servlet\Http\HttpServlet;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;

class CustomerServlet extends HttpServlet
{
    public function doGet(HttpServletRequest $req, HttpServletResponse $res)
    {
        $webappPath = $this->getServletConfig()->getWebappPath();

        // check if the template is available
        if (!file_exists(
                $pathToTemplate = $webappPath .
                DIRECTORY_SEPARATOR . 'static/templates/customer.phtml'
        )) {
            throw new \Exception(
                "Requested template '$pathToTemplate' is not available"
            );
        }

        // render template
        ob_start();
        require $pathToTemplate;
        $html = ob_get_clean();

        $res->appendBodyStream($html);
    }  
    // ... 
```

A template containing a form can now be built and delivered as already seen above.
The templates are in the directory ``static/templates`` of the webapp root directory. If it exists it gets rendered and
its output is set as the response's content. The only thing to do is to fill the template with life. Create the file
``static/templates/customer.phtml`` and insert the following (you might also add some style if you like).

```html
<!DOCTYPE html>
<html lang="en">
	<head>
  		<meta charset="utf-8">
	</head>
	<body>
 		<form action="customer.do" method="post">
       		<input type="hidden" name="action" value="persist" />
      		<input type="hidden" name="customerId" value="<?php echo $customerId ?>" />
       		<input name="name" type="text" placeholder="Enter customer name" />
      		<button type="submit" class="btn">Submit</button>
  		</form>
	</body>
</html>
```

As you can see the form uses the POST method to post its data. As we only support GET in ``CustomerServlet`` we have to
implement a corresponding method which can handle POST.

```php
public function doPost(HttpServletRequest $req, HttpServletResponse $res)
{
  	// load the params with the entity data
   	$parameterMap = $req->getParameterMap();

   	// check if the necessary params has been specified and are valid
   	if (!array_key_exists('customerId', $parameterMap)) {
      	throw new \Exception();
   	} else {
       	$customerId = filter_var($parameterMap['customerId'], FILTER_VALIDATE_INT);
   	}
   	if (!array_key_exists('name', $parameterMap)) {
       	throw new \Exception();
   	} else {
       	$name = filter_var($parameterMap['name'], FILTER_SANITIZE_STRING);
   	}

  	$res->appendBodyStream('Hello ' . $name);
}
```

So far so good, but we want to persist the customer to the database. Therefore we have to take a look on the persistence
container. Open ``META-INF/appserver-ds.xml``. This is a dummy configuration file for the persistence container. It reads like the following:
```xml
<datasources>
  	<datasource name="myfirstapp"
 	    type="TechDivision\PersistenceContainer\Application">    
 	        <database>
            <driver>pdo_sqlite</driver>
            <user>appserver</user>
            <password>appserver</password>
            <path>META-INF/data/customers.sqlite</path>
            <memory>false</memory>
            </database>
   	</datasource>
</datasources>
```

This configuration defines a data source connection to a database. In this case we use a sqlite database for demonstration
purposes. We defined the path to the database as well as the path to the entities which get persisted to it. 
You will find that the database already exists. This is done via a mechanism called provisioning, where we are able to execute
important steps an app needs at deploy- or time of first execution.
You might want to have a look at the `META_INF/provision.xml` to see what happened here in combination with the needed datasource.

Now let's create persistent entities. Create the class ``META-INF/classes/TechDivision/ApplicationServerProject/Entities/Customer.php`` with the following content.

```php
namespace TechDivision\ApplicationServerProject\Entities;

/**
 * @Entity @Table(name="customer")
 */
class Customer {
        
   	/**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    public $customerId;

   	/**
     * @Column(type="string", length=255)
     */
    public $name;

    public function setCustomerId($customerId) {
   		$this->customerId = $customerId;
   	}

    public function getCustomerId() {
 	 	return $this->customerId;
    }

    public function setName($name) {
    	$this->name = $name;
    }

    public function getName() {
    	return $this->name;
    }
}
```

This is an entity which we can parse and persist using Doctrine (as you might have guessed). The entity gets persisted by an entity processor which takes care of the entities' states. At this point there is the tricky but cool part of appserver. As we want to persist
the customer we have to talk to the persistence container which is all part of the ``META-INF`` folder. This is done by
sockets in order to achieve scalability. Right now only the persistence container knows the customer class and the servlet
engine does not know anything about it. We have to copy the customer class into the `WEB-INF` directory as
``WEB-INF/classes/TechDivision/ApplicationServerProject/Entities/Customer.php`` (or make a symlink). As such we can now use it in our servlet. Insert the
following lines right before the method ``doPost()`` appends its changes to the `$res` response object:

```php
use TechDivision\ApplicationServerProject\Entities\Customer;

// ...

// create a new entity and persist it
$entity = new Customer();
$entity->setCustomerId((integer) $customerId);
$entity->setName($name);

$initialContext = $this->session->createInitialContext();
$proxy = $initialContext->lookup(
  	'TechDivision\ApplicationServerProject\Services\CustomerProcessor'
);
$proxy->persist($entity);
```

As you can already see we do not have a session attribute. But this session is needed as a context session which can handle our current context. The initial context provides us with a proxy class for a class of the persistence container. In this example
we want to connect to the ``CustomerProcessor`` class as it handles our CRUD actions for our entity. We can communicate
to the processor via a socket which is represented by the proxy class whereas the proxy class is just a general proxy
implementation and not dependent on the ``CustomerProcessor`` class. The method call of ``persist()`` is actually done
by remote method invocation via sockets. In order to make the code lines work we have to add the following lines to
our customer servlet.

```php
use TechDivision\PersistenceContainerClient\ConnectionFactory;

// ...

protected $connection;
protected $session;

public function __construct() {
  	$this->connection = ConnectionFactory::createContextConnection('myfirstapp');
    $this->session = $this->connection->createContextSession();
}
```

Now we are ready to start the implementation of ``CustomerProcessor``. As we don't want to overwhelm the documentation
with lines of code we prepared a parent class to inherit some functionality from. It is located at
``META-INF/classes/TechDivision/ApplicationServerProject/Services/AbstractProcessor.php``. In the same
folder we create the ``CustomerProcessor.php`` class as follows.

```php
namespace TechDivision\ApplicationServerProject\Services;

use TechDivision\ApplicationServerProject\Entities\Customer;

/**
 * @Singleton
 */
class CustomerProcessor extends AbstractProcessor
{

   	public function persist(Customer $entity)
    {
       	// load the entity manager
       	$entityManager = $this->getEntityManager();
        // check if a detached entity has been passed
        if ($entity->getCustomerId()) {
        	$merged = $entityManager->merge($entity);
            $entityManager->persist($merged);
       	} else {
           	$entityManager->persist($entity);
       	}
        // flush the entity manager
       	$entityManager->flush();
        // and return the entity itself
        return $entity;
  	}
}
```

You may have noticed the ``@Singleton`` above the class name. This exhibits that
the customer processor is a singleton bean. It means that only one instance of it is created which is necessary as there could be conflicts while persisting otherwise. There are also stateless and stateful beans which are for other purposes, as they
either know the state between two requests of the same user or not. The ``persist()`` method gets the doctrine entity
manager in order to persist entities. After successful persistence we want to display all entities in the frontend. We
therefore implement the following method into our customer processor.

```php
public function findAll()
{
  	$entityManager = $this->getEntityManager();
    $repository = $entityManager->getRepository(
      	'TechDivision\ApplicationServerProject\Entities\Customer'
    );
    return $repository->findAll();
}
```

This works again with the doctrine entity manager as it does all the work for us. In order to display all customers
in the frontend we add the following lines of code at the start of the ``doGet()`` method in our customer servlet.

```php
// member variable
protected $customers;

// ...

// doGet() method
$initialContext = $this->session->createInitialContext();
$proxy = $initialContext->lookup('TechDivision\ApplicationServerProject\Services\CustomerProcessor');
$this->customers = $proxy->findAll();
```

It is the same principle. We request again a proxy class which takes all the communication for us via the socket. We just
call the ``findAll()`` method we already implemented in our customer processor. Next step is now to customize the
template like this:

```php
<?php foreach ($this->customers as $customer): ?>
  	<div><?php echo $customer->getName() ?></div>
<?php endforeach; ?>
```

We iterate over all customers and echo their names. The final step is now to change the ``doPost()`` method as we still
return the Hello World example. Change the last line of the method to this line:

```php
$this->doGet($req, $res);
```

It is now time to restart the appserver again and go to ``127.0.0.1:9080/myfirstapp/customer.do`` to see what we have built.
You can now type in new customer names and view them in the frontend. Please note that this is just a very simple example
to demonstrate how appserver works. It is a beginner tutorial. The demonstrated code should clearly be refactored, but
for this tutorial it is good to go. Feel free to try out more functionality like updating and deleting entities.
Also have a look at our example app which contains an action-based wrapper around the HTTP method.
