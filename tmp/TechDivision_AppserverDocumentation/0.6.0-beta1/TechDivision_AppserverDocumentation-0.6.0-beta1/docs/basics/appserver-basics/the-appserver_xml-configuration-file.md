# The appserver.xml Configuration File

We believe that the appserver should be highly configurable, so anyone interested can fiddle around with it.
Therefor we provide a central configuration file located at `/opt/appserver/etc/appserver.xml`.
This file contains the complete [architecture](<>) as an XML structure.
So if you want to change used components, introduce new services or scale the system by adding additional servers you can do so with some lines of XML.
You might have a look at our basic `appserver.xml` [here](<https://github.com/techdivision/TechDivision_Runtime/blob/master/src/etc/appserver.xml>)

## The Architecture

In this example we have a shortened piece of the `apserver.xml` file to understand how the architecture is driven by the configuration.

```xml
<container
   	name="webserver"
    type="TechDivision\WebContainer\Container">
    <description>
     	<![CDATA[This is an example of a webserver container that handles http requests in common way]]>
    </description>
    <deployment type="TechDivision\WebContainer\WebContainerDeployment" />
    <host
    	name="localhost"
        appBase="/webapps"
        serverAdmin="info@appserver.io"
        serverSoftware="appserver/${appserver.version} (${os.family}) PHP/${appserver.php.version}" />
    <servers>
       	<server
         	type="\TechDivision\WebSocketServer\Servers\AsyncServer"
            socket="\TechDivision\WebSocketServer\Sockets\AsyncSocket"
            serverContext="\TechDivision\Server\ServerContext"
            loggerName="System">
            <params>
               	<param name="transport" type="string">tcp</param>
               	<param name="address" type="string">0.0.0.0</param>
               	<param name="port" type="integer">8589</param>
               	<param name="workerNumber" type="integer">64</param>
                        
               	<!-- configure the server as you would like -->
                        
           	</params>

           	<connectionHandlers>
            	<connectionHandler type="\TechDivision\WebSocketProtocol\WebSocketConnectionHandler" />
           	</connectionHandlers>
     	</server>

		<!-- Here, additional servers might be added -->
	
	</servers>
</container>
``` 

In the above example you can see three important components of the appserver architecture being used. The [*container*](<>), [*server*](<>) and a [*protocol*](<>) (if you did not read about our basic [architecture](<>) you should now).
We are basically building up a container which holds a server using the websocket protocol to handle incomming requests.

### Container configuration

A *container* is created by using the `container` element within the `containers` collection of the `appserver` document element.
Two things make this element in a specific container being built up by the system on startup:

* The `type` attribute states a class extending our `AbstractContainerThread` which makes a container into a certain kind of container.

* The `deployment` element states a class containing preparations for starting up the container. It can be considered a hook which will be invoked before the container will be available.

That is basically everything there is to do to create a new container. To make use of it, it has to contain at least one *server* within its `servers` collection.

### Server configuration

The *servers* contained by our *container* can also be losely drafted by the XML configuration and will be instantiated on container bootup.
To enable a *server* you have to mention three basic attributes of the element:

* The `type` specifies a class implementing the `ServerInterface` which implements the basic behaviour of the server on receiving a connection and how it will handle it.

* The `socket` attribute specifies the type of socket the server should open. E.g. a stream or asynchonious socket.

* The `serverContext` specifies the server's soure of configuration and container for runtime information e.g. ServerVariables like `DOCUMENT_ROOT`.

So we have our specific server which will open a certain port and operate in a defined context.
But to make the server handle a certain type of requests it needs to know which *protocol* to speak.
This can be done using the `connectionHandler` element.
Certain server wrappers can handle certain protocols. Therefor we can use the protocols which a server wrapper, e.g. `WebServer` supports in form of connection handlers. [WebServer](<https://github.com/techdivision/TechDivision_WebServer>) offers a `HttpConnectionHandler` class. By using it, the server is able to understand the HTTP protocol. 


