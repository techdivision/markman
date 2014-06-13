# Technical Background

The technical foundation was given by the introduction of PHP userland threads in the form of Joe Watkins' [phtreads](https://github.com/krakjoe/pthreads) library.
Using this library we are able to utilize real [POSIX](<http://en.wikipedia.org/wiki/Posix>) compatible threads which allows us to build up complex structures and non-blocking connection handlers within only one PHP process.
It also allows for communication in between these threads.