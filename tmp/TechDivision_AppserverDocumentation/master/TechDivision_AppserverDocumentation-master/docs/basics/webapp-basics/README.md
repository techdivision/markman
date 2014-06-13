# Webapp Basics
Below you will find instructions and further information about our concept of webapps, php applications running within the appserver.
To understand why we coined the term "webapps" for them we need to have a look on the two concepts we offer as a base for such apps.
The first one is well known in the PHP community: **PHP scripts**. These cover basically every PHP application right now which get bootstrapped by a script and mostly consists of basic object oriented structures PHP offers.
The second is a concept derived from the Java world we would like to introduce to the PHP world: [**servlets**](<http://en.wikipedia.org/wiki/Servlet>).
Servlets, and therefor the apps using them, are not bootstrapped by scripts but rather by the appserver itself. That in combination with the multithreaded [architecture](<#technical-background-and-architecture>) allows for a very unique use of PHP classes which are implemented as servlets.
To further get to know the concept you might check for a more [practical example](<#app-development>).
