<?php

class DatabaseConnection
{
  /////////////////////////////////
  // Database
  /////////////////////////////////
  function DatabaseConnection($servername, $username, $password)
  {
    $connected = mysql_connect($servername, $username, $password);
    if(!$connected) die('Could not connect: '.mysql_error());
    mysql_select_db("Restaurants") or die(mysql_error());
  }

  function disconnect()//disconnect from the database
  {
    if($this->$connected) mysql_close($this->$connected);
  }
  
  //send basic query to database
  function query($query)//send a query to the database
  {
    $result = mysql_query($query);
    $this->errorCheck($result);
    return $result;
  }

  function errorCheck($result)
  {
    if(!$result)
    {
      $message = 'Invalid query: ' . mysql_error() . "\n";
      //$message .= 'Whole query: ' . $query;
      die($message);
    }
  }
  
  //ADDING DATA/////////////
  function addUser(SafeString $name, SafeString $email, SafeString $pass)
  {
    $sql = "INSERT INTO `users` (`user_id`, `name`, `email`, `hash`, `admin`) VALUES (NULL, '$name->value', '$email->value', SHA1('$pass->value'), '0')";
    $this->query($sql);
  }
  
  function addRestaurant(SafeString $name, SafeString $image, SafeString $description, SafeString $website, SafeString $address, SafeString $chef, SafeString $seats, SafeString $phone)
  {
    $sql = "INSERT INTO `restaurants` (`name`, `image`, `description`, `website`, `address`, `chef`, `seats`, `phone`) VALUES ('$name->value', '$image->value', '$description->value', '$website->value', '$address->value', '$chef->value', '$seats->value', '$phone->value')";
    $this->query($sql);
  }
  
  function addReview(SafeString $user_id, SafeString $restaurant_id, SafeString $text)
  {
    $sql = "INSERT INTO `reviews` (`user_id`, `restaurant_id`, `text`) VALUES ('$user_id->value', '$restaurant_id->value', '$text->value')";
    $this->query($sql);
  }
  
  function addInterest(SafeString $user_id, SafeString $restaurant_id)
  {
    $sql = "INSERT INTO `interested` (`user_id`, `restaurant_id`) VALUES ('$user_id->value', '$restaurant_id->value')";
    $this->query($sql);
  }
  
  function addVisited(SafeString $user_id, SafeString $restaurant_id)
  {
    $sql = "INSERT INTO `visited` (`user_id`, `restaurant_id`, `rating`) VALUES ('$user_id->value', '$restaurant_id->value', '0')";
    $this->query($sql);
  }
  
  function addTag(SafeString $restaurant_id, SafeString $tag_id)
  {
    $sql = "INSERT INTO `tags` (`restaurant_id`, `tag_id`) VALUES ('$restaurant_id->value', '$tag_id->value')";
    $this->query($sql);
  }
  
  function addTagType(SafeString $description)
  {
    $sql = "INSERT INTO `tag_types` (`tag_id`, `description`) VALUES (NULL, '$description->value')";
    $this->query($sql);
  }
  
  //GETTING DATA//////////////////
  function getUserInfo(SafeString $name)
  {
    $sql = "SELECT * FROM `users` WHERE `name` ='$name->value'";
    return $this->query($sql);
  }
  
  function getRestaurantInfo(SafeString $restaurant_id)
  {
    $sql = "SELECT * FROM `restaurants` WHERE `restaurant_id` ='$restaurant_id->value'";
    return $this->query($sql);  
  }
    
  function getUserReviews(SafeString $user_id)
  {
    $sql = "SELECT * FROM `reviews` WHERE `user_id` ='$user_id->value'";
    return $this->query($sql);
  }
  
  function getUserName(SafeString $user_id)
  {
    $sql = "SELECT name FROM `users` WHERE `user_id` = '$user_id->value'";
    $result = mysql_fetch_array($this->query($sql));
    return $result['name'];
  }
  
  function getTagTypeID(SafeString $description)
  {
    $sql = "SELECT * FROM `tag_types` WHERE `description` = '$description->value'";
    return $this->query($sql);
  }
  
  function getRestaurantName(SafeString $restaurant_id)
  {
    $sql = "SELECT name FROM `restaurants` WHERE `restaurant_id` = '$restaurant_id->value'";
    $result = mysql_fetch_array($this->query($sql));
    return $result['name'];
  }
  
  //needs optimisation
  function getRestaurantTags(SafeString $restaurant_id)
  {
    $sql = "SELECT tag_types.description, tag_types.group FROM `tags` LEFT JOIN `tag_types` ON tags.tag_id = tag_types.tag_id WHERE tags.restaurant_id = '$restaurant_id->value'";
    return $this->query($sql);
  }
  
  function getRestaurantReviews(SafeString $restaurant_id)
  {
    $sql = "SELECT * FROM `reviews` WHERE `restaurant_id` = '$restaurant_id->value'";
    return $this->query($sql);
  }
  
  //Note that $descriptions is not safe
  function searchRestaurants(SafeString $keywords, $descriptions)
  {
    if($keywords->value != "" && count($descriptions) > 0) return $this->searchRestaurantTags($descriptions);
    else if($keywords->value != "") return $this->searchRestaurantKeywords($keywords);
    else if(count($descriptions) > 0) return $this->searchRestaurantKeywordsTags($keywords, $descriptions);
    
    return $this->allRestaurants();
  }
  
  function searchRestaurantKeywords(SafeString $keywords)
  {
	  return $this->query("SELECT * FROM `restaurants` WHERE (`name` LIKE '%$keywords->value%' OR `description` LIKE '%$keywords->value%') LIMIT 20");
  }
  
  function searchRestaurantTags($descriptions)
  {
	  $searchString = "";
	  $counter = 0;
	  foreach ($descriptions as $string)
	  {
      if($counter != 0) $searchString .= " AND";
      $counter++;
      $searchString .= " `description` = '$string'";
	  }
	  $tagDescriptionSearch  = "SELECT tag_id FROM `tag_types` WHERE $searchString";
	  
	  $keywordSearch = "SELECT * FROM `restaurants` WHERE (`name` LIKE '%$keywords->value%' OR `description` LIKE '%$keywords->value%')";
	  $tagIDSearch = "SELECT restaurant_id FROM `tags`,($tagDescriptionSearch) AS descriptor WHERE tags.`tag_id` = descriptor.`tag_id`";
	  
	  return $this->query("SELECT * FROM ($keywordSearch) AS keyword,($tagIDSearch) AS tag WHERE keyword.restaurant_id = tag.restaurant_id LIMIT 20");
  }
  
  function searchRestaurantKeywordsTags(SafeString $keywords, $descriptions)
  {
    $searchString = "";
    $counter = 0;
    foreach ($descriptions as $string)
    {
      if($counter != 0) $searchString .= " AND";
      $counter++;
      $searchString .= " `description` = '$string'";
    }
    $tagDescriptionSearch  = "SELECT tag_id FROM `tag_types` WHERE $searchString";
    
    return $this->query("SELECT * FROM restaurants,(SELECT restaurant_id FROM `tags`,($tagDescriptionSearch) AS descriptor WHERE tags.`tag_id` = descriptor.`tag_id`) AS tagSearch WHERE restaurants.restaurant_id = tagSearch.restaurant_id LIMIT 20");
  }
  
  function allRestaurants()
  {
    $sql = "SELECT * FROM `restaurants` ORDER BY TRIM(LEADING 'a ' FROM TRIM(LEADING 'an ' FROM TRIM( LEADING 'the ' FROM LOWER(name))))";
    return $this->query($sql);
  }
  
  //EDITING DATA/////////////////////
  function editUser()
  {
  
  }
  
  function editRestaurant()
  {
  
  }
  
  function editReview(SafeString $newText)
  {
  
  }
  
  function editTagType(SafeString $tag_id, SafeString $newDescription)
  {
  
  }
  
  //REMOVING DATA//////////////////
  function removeUser(SafeString $user_id)
  {
  
  }
  
  function removeRestaurant(SafeString $restaurant_id)
  {
  
  }
  
  function removeReview(SafeString $review_id)
  {
  
  }
  
  function removeInterest(SafeString $user_id, SafeString $restaurant_id)
  {
  
  }
  
  function removeVisited(SafeString $user_id, SafeString $restaurant_id)
  {
  
  }
  
  function removeTag(SafeString $restaurant_id, SafeString $tag_id)
  {
  
  }
  
  function removeTagType(SafeString $tag_id)
  {
    $sql = "DELETE FROM `tag_types` WHERE `tag_id` = '" . $tag_id->value . "'";
    $this->query($sql);
    
    //should also remove all references to it here
  }
  
  //MISCELLANEOUS////////////////
  
  //checks for credential accuracy
  function isUsernamePasswordCorrect(SafeString $name, SafeString $password)
  {
    $sql = "SELECT name FROM `users` WHERE name = '" . $name->value . "' AND hash = '" . hash('sha256', $password->value) . "'";
    return (mysql_num_rows($this->query($sql)) == 1);
  }
}

//safestring requires active database connection
class SafeString
{
  public $value = "";
  
  function SafeString($unescaped)
  {
    $this->value = mysql_real_escape_string($unescaped);
  }
}

?>
