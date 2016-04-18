<?php 
$dsn = 'mysql:dbname=fireworks_over_america;host=mysqlv112';
$username = 'foa7356';
$password = 'CdLUW3#*DC~';

try {
$conn = new PDO($dsn, $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
	echo 'Connection failed: ' . $e->getMessage();
}
?>