<?php
include_once("tempVariables.php");
include_once("databaseConnection.php");
include_once("session.php");

if(loggedIn())
{
echo 'user ' . $_SESSION['user_id'] . ' visited ' . $_POST['restaurant'];
}
?>