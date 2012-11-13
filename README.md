simple-mysql-database-class
===========================

This is a simple database class for use with PHP and MySQL. It is meant to be as simple to use as possible.

Under the hood, is is set up for many future improvements. For example, it automatically detects the query type so it can send the request to a master or a slave as needed, yet there is currently no support for multiple database connections. The error reporting is also a bit bare bones, but could be improved in the future.

Usage:

To use, create a new instance of the class with the correct connection parameters:

`$db = new Database($host, $user, $password, $database);`

Query the database like so:

`$results = $db->query("SELECT * FROM table LIMIT 10");`

`Database::query` returns an array of results, so it can be iterated through like any other array:

```php
foreach ($results as $row) {
	// hello world
}
```

If the query ends with `LIMIT 1`, only one row is to be expected in the results, so instead of returning an array of results, each item containing an individual row, the row itself is returned as an array:

`$row = $db->query("SELECT * FROM table LIMIT 1")`

If no results are returned from MySQL, Database::query returns `false`.

If the query type was an insert, Database::query returns the last inserted ID or true.

To get the last inserted ID from MySQL, use `Database::insertId()`, which will return the last inserted ID or false.

`$id = $db->insertId();`