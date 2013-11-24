<?php
header('Content-type: application/json');

include_once("tempVariables.php");
require_once("databaseConnection.php");



try
{
  $restaurantArray = new Restaurants();

  $connection = new DatabaseConnection($servername, $userRead, $userReadPass);//connecting to database
  
  $tags = array();
  
  foreach($_GET as $name => $value) 
  {
    if($name != "type" && $name != "keywords" && $name != "order") 
    {
      $safeString = new SafeString($value);
      $tags[] = $safeString->value;
    }
  }
  
  if(isset($_GET['keywords']))
  {
  $restaurants = $connection->searchRestaurants(new SafeString($_GET['keywords']), $tags);
  }
  else
  {
  $restaurants = $connection->searchRestaurants(new SafeString(""), $tags);
  }
  
  while($restaurant = mysql_fetch_array($restaurants))
  {
    $restaurantArray->addRestaurant(new Restaurant($restaurant['restaurant_id'],$restaurant['name'],$restaurant['image'],$restaurant['description']));
  }
  
  $restaurantArray->toJson();
}
catch (Exception $e)
{
  echo $e->getMessage();
}

class Restaurants
{
  public $restaurants = array();

  function addRestaurant(Restaurant $restaurant)
  {
    $this->restaurants[] = $restaurant;
  }
   
  function toJson()
  {
    echo json_encode($this);
  }
}

class Restaurant
{
  public $id;
  public $name;
  public $image;
  public $description;
  
  function Restaurant($id, $name, $image, $description)
  {
    $this->id = $id;
    $this->name = utf8_encode($name);
    if($image != "") $this->image = $image;
    else $this->image = "default.gif";    
    $this->description = utf8_encode($description);
  }
}
?>