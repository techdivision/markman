# Installation 
Besides supporting several operating systems and their specific ways of installing software, we also support several ways of getting this software.
So to get your appserver.io package you might do any of the following:

* Download one of our [**releases**](<https://github.com/techdivision/TechDivision_ApplicationServer/releases>) right from this repository which provide tested install packages

* Grab any of our [**nightlies**](<http://builds.appserver.io/>) from our project page to get bleeding edge install packages which still might have some bugs

* Build your own package using [ant](<http://ant.apache.org/>)! To do so clone [TechDivision_Runtime](<https://github.com/techdivision/TechDivision_Runtime>) first. Then update at least the `os.family` and `os.distribution` build properties according to your environment and build the appserver with the ant target appropriate for your installer (e.g. `create-pkg` for Mac or `create-deb` for Debian based systems).

The package will install with these basic default characteristics:

* Install dir: `/opt/appserver`
* Autostart after installation, no autostart on reboot
* Reachable under pre-configured ports as described [here](<#basic-usage>) 

For OS specific steps and characteristics see below for tested environments.

## Mac OS X

* Tested versions: 10.8.x +
* Ant build: 
	- `os.family` = mac 
	- target `create-pkg`


## Windows
* Tested versions: 7 +
* Ant build: 
	- `os.family` = win
	- target `WIN-create-jar`


As we deliver the Windows appserver as a .jar file, a installed Java Runtime Environment (or JDK that is) is a vital requirement for using it.
If the JRE/JDK is not installed you have to do so first. You might get it from [Oracle's download page](<http://www.oracle.com/technetwork/java/javase/downloads/jre7-downloads-1880261.html>).
If this requirement is met you can start the installation by simply double-clicking the .jar archive.

## Debian

* Tested versions: Squeeze +
* Ant build: 
	- `os.family` = linux
	- `os.distribution` = debian 
	- target `create-deb`

If you're on a Debian system you might also try our .deb repository:

```
root@debian:~# echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list
root@debian:~# wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -
root@debian:~# aptitude update
root@debian:~# aptitude install appserver
```

## Fedora
* Tested versions: 20
* Ant build: 
	- `os.family` = linux
	- `os.distribution` = fedora 
	- target `create-rpm`
	

## CentOS
* Tested versions: 6.5
* Ant build: 
	- `os.family` = linux
	- `os.distribution` = centos 
	- target `create-rpm`

Installation and basic usage is the same as on Fedora **but** CentOS requires additional repositories like [remi](<http://rpms.famillecollet.com/>) or
[EPEL](<http://fedoraproject.org/wiki/EPEL>) to satisfy additional dependencies.

## Raspbian
As an experiment we offer Raspbian and brought the appserver to an ARM environment. What should we say, it worked! :D
With `os.distribution` = raspbian you might give it a try to build it yourself (plan at least 5 hours) as we currently do not offer prepared install packages.