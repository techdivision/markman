# Uninstall

Before uninstalling you should stop all services which are still running, otherwise there might be problems with existing pid-files on Linux and Mac for the next time you install it.
You can have a look how to do so [here](<basic-usage.md#start-and-stop-scripts>)

To uninstall the appserver on Linux you might rely on your package management system. 
On Windows you can use the normal uninstall process provided by the operating system.

Under Mac OS X you can simply delete the `/opt/appserver` folder and delete the configuration
files for the launch daemons. These are files are located in folder `/Library/LaunchDaemons` and named `io.appserver.<DAEMON>.plist`.