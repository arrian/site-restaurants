<?php 
include_once("tempVariables.php");
include_once("databaseConnection.php");
include_once("session.php");

//connecting
$connection = new DatabaseConnection($servername, $userRead, $userReadPass);//connecting to database

$id = new SafeString($_SERVER['QUERY_STRING']);
if(!is_numeric($id->value)) $id = new SafeString("0");

$restaurants = $connection->getRestaurantInfo($id);
$restaurant = mysql_fetch_array($restaurants);

$reviews = $connection->getRestaurantReviews($id);
?><html>
<head>
  <title><?echo $restaurant['name'];?></title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <meta name="viewport" content="width=640" />
  
  
  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
  <script type="text/javascript">
  var map;

  function initGeolocation()
  {
    if( navigator.geolocation )
    {

      // Call getCurrentPosition with success and failure callbacks
      navigator.geolocation.getCurrentPosition( success, fail );
    }
    else
    {
      alert("Location is not supported by your browser.");
    }
  }

  var map;
  function success(position)
  {
    // Define the coordinates as a Google Maps LatLng Object
    var coords = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
    // Prepare the map options
    var mapOptions =
    {
      zoom: 15,
      center: coords,
      mapTypeControl: false,
      navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    // Create the map, and place it in the map_canvas div
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

    // Place the initial marker
    var marker = new google.maps.Marker({
    position: coords,
    map: map,
    title: "<?echo $restaurant['name'];?>"
    });
  }

  function fail()
  {
    // Could not obtain location
  }
  window.onload=initGeolocation();
  </script>
</head>
<body>
<div id="content">
  <?
  if(loggedIn())
  {?>
  <div id="info">
  <a href="user.php?<?echo $_SESSION['name'];?>"><?echo $_SESSION['name'];?></a> | <a href="logout.php">Log Out</a></div><?
  }?>
  <div id="header"><h1>Canberra Restaurant Guide</h1></div>
  <div id="requirements">  
    <div id="links">
      <a href="/restaurants/">All</a>
      <a href="/restaurants/">Search</a>
      <a href="/restaurants/"><?if(loggedIn())echo 'Account'; else echo 'Log In / Sign Up';?></a>
      <a href="/restaurants/">About</a>
    </div>
  </div>
  <div id="detail">
    <div style="width:600px;margin-left:auto;margin-right:auto;">
      <h2 style="text-align:center;"><?echo $restaurant['name'];?></h2>
      <p><?echo $restaurant['description'];?></p>
      <div id="images" style="text-align:center;"><img src="images/<?echo $restaurant['image'];?>" /><img src="images/default.gif"><img src="images/default.gif"><img src="images/default.gif"><img src="images/default.gif"><img src="images/default.gif"></div>
      <div id="map_canvas">
      </div>
      <?if(loggedIn()){?>
      <form method="post" action="visited.php">
      <input type="hidden" name="restaurant" value="<?echo $id->value;?>" />
      <input class="tick" type="submit" value="Visited" />
      </form>
      
      <form method="post" action="interested.php">
      <input type="hidden" name="restaurant" value="<?echo $id->value;?>" />
      <input class="star" type="submit" value="Interested" />
      </form>
      <?}?>
      <h3>Address</h3>
      <p><?echo $restaurant['address'];?></p>
      <h3>Website</h3>
      <p><a href="<?echo $restaurant['website'];?>"><?echo $restaurant['website'];?></a></p>
      <h3>Phone</h3>
      <p><?echo $restaurant['phone'];?></p>
      <h3>Seats</h3>
      <p><?echo $restaurant['seats'];?></p>
      <h3>Tags</h3>
      <p>
      <?
      $tags = $connection->getRestaurantTags($id);
      
      while($tag = mysql_fetch_array($tags))
      {?>
      <span class="tag">
      <?echo $tag['description'];?>
      </span>
      <?}?>
      </p>
      <h3>Reviews</h3>
      <?
      while($review = mysql_fetch_array($reviews))
      {
        $username = $connection->getUserName(new SafeString($review['user_id']));
      ?>
      <a href="/restaurants/user.php?<?echo $username;?>"><h5><?echo $username;?></h5></a>
      <p><?echo $review['time'];?></p><p><?echo $review['text'];?></p>
      <?
      }?>
      <form method="post" action="review.php">
      <h3>Add Review</h3>
      <textarea rows="10" style="width:100%;max-width:100%;" name="description"></textarea><br />
      <input type="hidden" value="<?echo $id->value?>" name="restaurant_id" />
      <input type="submit" value="Submit Review" />
      </form>
    </div>
  </div>
</div>
</body>
</html>