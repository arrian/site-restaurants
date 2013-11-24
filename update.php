<?php 
include_once("tempVariables.php");
include_once("databaseConnection.php");

function displayAllRestaurants()
{
  global $servername, $userRead, $userReadPass;
  //connecting
  $connection = new DatabaseConnection($servername, $userRead, $userReadPass);//connecting to database

  $result = $connection->allRestaurants();
  while($row = mysql_fetch_array($result))
  {?>
  <a href="restaurant.php?<?echo $row['restaurant_id'];?>" style="background-image:url(images/<?echo $row['image'];?>)"><h4><?echo $row['name'];?></h4></a>
  <?
  }
}
?>