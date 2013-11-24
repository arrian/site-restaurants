<?php
include_once("tempVariables.php");
include_once("databaseConnection.php");
include_once("session.php");

if(loggedIn()) echo $_SESSION['name'] . " reviewed restaurant " . $_POST['restaurant_id'] . " saying: " . $_POST['description'];
else echo "A guest reviewed restaurant " . $_POST['restaurant_id'] . " saying: " . $_POST['description'];

?>