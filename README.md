# GreenMT
Simple and naïve library for php pthreads extension. 

GreenMT classes are not designed to be used as is, but as a base for greater developement.
It currently provide two approch :

- **Synchronized** : data is fetch from inside the created threads, and result is fetch in the main thread as it becomes available
- **OnDemand** : data comes from the main thread and is dispatched among the threads which process them when they need it


This library is design to work with the PHP Pthread extension `pthreads`, which can be find here : https://github.com/krakjoe/pthreads
