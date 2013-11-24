<?php 
include_once("tempVariables.php");
include_once("databaseConnection.php");

//connecting
$connection = new DatabaseConnection($servername, $userRead, $userReadPass);//connecting to database

$name = new SafeString($_SERVER['QUERY_STRING']);
$users = $connection->getUserInfo(new SafeString($name->value));
$user = mysql_fetch_array($users);

$reviews = $connection->getUserReviews(new SafeString($user['user_id']));

?><html>
<head>
  <title>User - <?echo $user['name'];?></title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <meta name="viewport" content="width=565" />
</head>
<body>
<div id="content">
  <div id="glass"></div>
  <div id="header"><h1>Canberra Restaurant Guide</h1></div>
  <div id="requirements">  
    <div id="links">
    <a href="/restaurants/">All</a><a href="/restaurants/">Search</a><a href="/restaurants/">Sign In</a><a href="/restaurants/">About</a>
    </div>
  </div>
  <div id="detail">
    <h2>User - <?echo $user['name'];?></h2>
    <h3>Recent Reviews</h3>
    <?while($review = mysql_fetch_array($reviews))
    {?>
    <a href="/restaurants/restaurant.php?<?echo $review['restaurant_id'];?>"><h5><?echo $connection->getRestaurantName(new SafeString($review['restaurant_id']));?></h5></a>
    <p><?echo $review['time'];echo $review['text'];?></p>
    <?
    }?>
  </div>
</div>
</body>
</html>