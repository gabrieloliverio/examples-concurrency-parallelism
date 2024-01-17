# Examples - concurrency and parallelism article

## To execute the examples

1. Install the dependencies

`composer install`

As I write the article, some of the AMPHP packages are on version 3, whereas some are on version 2, such as the MySQL one.
The MySQL package is being updated to version 3, but it is still in Beta. For this reason, the minimum stability was set to "dev".

2. Get the containers running

`docker compose up -d`

A container with php and apache will be launched to serve the ´slow.php´ and ´fast.php´, as well as a MySQL for the MySQL examples.

3. Then execute a script on CLI, e.g. 

`php src/hello_world.php`
