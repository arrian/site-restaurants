<?php 
include_once("tempVariables.php");
include_once("databaseConnection.php");
include_once("session.php");

$name = $_POST['username'];
$password = $_POST['password'];

$connection = new DatabaseConnection($servername, $userRead, $userReadPass);//connecting to database

if((strlen($name) < 2) || ($password == "")) header("Location: /restaurants/");

if(strlen($name) > 50) die("Username too long.");
if(strlen($password) > 50) die("Password too long.");

$hash = hash('sha256', $password);

//checking if user exists
if($connection->isUsernamePasswordCorrect(new SafeString($name), new SafeString($password))) validateUser();
else die("Incorrect username or password.");

$users = $connection->getUserInfo(new SafeString($name));
$user = mysql_fetch_array($users);


$_SESSION['name'] = $name;
$_SESSION['admin'] = $user['admin'];
$_SESSION['user_id'] = $user['user_id'];

header("Location: /restaurants/");
?>
