<?php
$Secret = array(
    'clientid' => getenv('CLIENTID'),
    'mysql.server' => getenv('MYSQL_SERVER'),
    'mysql.username' => getenv('MYSQL_USERNAME'),
    'mysql.password' => getenv('MYSQL_PASSWORD'),
    'mysql.dbname' => getenv('MYSQL_DBNAME'),
    'mongodb.server' => getenv('MONGODB_SERVER'),
    'mongodb.username' => getenv('MONGODB_USERNAME'),
    'mongodb.password' => getenv('MONGODB_PASSWORD'),
);

var_dump($Secret);
var_dump($_ENV);
?>