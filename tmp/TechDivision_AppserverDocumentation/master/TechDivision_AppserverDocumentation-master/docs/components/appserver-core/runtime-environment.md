# Runtime Environment

The runtime environment appserver.io is using is delivered by the package [TechDivision_Runtime](<https://github.com/techdivision/TechDivision_Runtime>).
This package  provides the appserver runtime which is system independent and encloses a thread-safe compiled PHP environment.
Besides the most recent PHP 5.5.x version the package came with installed extensions:

* [pthreads](http://pecl.php.net/package/pthreads)
* [appserver](https://github.com/techdivision/php-ext-appserver) (contains some replacement functions which behave badly in a multithreaded environment)

Additionally the PECL extensions [XDebug](http://pecl.php.net/package/xdebug) and [ev](http://pecl.php.net/package/ev) are compiled as a shared modules. XDebug is necessary to
render detailed code coverage reports when running unit and intergration tests. ev will be used to
integrate a timer service in one of the future versions.

