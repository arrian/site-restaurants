<?php 
include_once("update.php");
include_once("session.php");
?><html>
<head>
  <title>Restaurant Guide</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <meta name="keywords" content="Canberra, Australia, restaurants, restaurant, winery, wineries, bars, cafes, reviews" />
  <meta name="description" content="Canberra's restaurant guide." />
  <meta name="viewport" content="width=640" />
  <link rel="apple-touch-icon" href="/restaurants/apple-touch-icon.png" />
  <script src="jquery-1.7.1.min.js"></script>
  <script language="javascript">
  function hideAll()
  {
    document.getElementById('search').style.display = 'none';
    document.getElementById('account').style.display = 'none';
    document.getElementById('about').style.display = 'none';
  }
  
  function showAll()
  {
    hideAll();
  }
  
  function showSpecified(id)
  {
    hideAll();
    document.getElementById(id).style.display = 'block';
  }
  
  function signUp()
  {
    document.account.action = 'signup.php';
    document.account.submit();
  }
  
  function logIn()
  {
    document.account.action = 'login.php';
    document.account.submit();
  }
  
  function search()
  {
    var restaurants = document.getElementById("restaurants");
    var tags = 'restaurants';
    for (i = 0; i < document.searchForm.elements.length; i++) 
    {
      if(searchForm.elements[i].type == "checkbox")
      {
        if(searchForm.elements[i].checked == true)
        {
          tags += "&" + document.searchForm.elements[i].name + "=" + document.searchForm.elements[i].value;
        }
      }
      else
      {
        if(document.searchForm.elements[i].value != "any" && document.searchForm.elements[i].value != "")
        tags += "&" + document.searchForm.elements[i].name + "=" + document.searchForm.elements[i].value;
      }
    }
    
    restaurants.innerHTML = "<h3>Loading...</h3>";
    $.getJSON("search.json.php?type=" + tags, function(json){
      if(json.restaurants.length == 0) restaurants.innerHTML = "<h3>No Results</h3>";
      else restaurants.innerHTML = "";
      $.each(json.restaurants, function(i, item) 
      {  
        try
        {
          restaurants.innerHTML += "<a href='restaurant.php?" + json.restaurants[i].id + "' style='background-image:url(images\/" + json.restaurants[i].image + ");'><h4>" + json.restaurants[i].name + "</h4></a>";
        }
        catch(err)
        {
          restaurants.innerHTML += "Error occured while searching.";
        }
      });
    });
  }
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
    <a href="#" onClick="showAll();">All</a>
    <a href="#" onClick="showSpecified('search');">Search</a>
    <a href="#" onClick="showSpecified('account');"><?if(loggedIn())echo 'Account'; else echo 'Log In / Sign Up';?></a>
    <a href="#" onClick="showSpecified('about');">About</a>
    </div>
    <div id="account">
      <hr />
      <span style="margin-left:auto;margin-right:auto;display:block;width:300px;">
    <?
    if(loggedIn())
    {?>
      <a href="#"><h3>Visited</h3></a>
      <a href="#"><h3>Interested</h3></a>
      <a href="#"><h3>Reviewed</h3></a>
      <a href="#"><h3>Settings</h3></a>
    <?
    }
    else
    {?>  <form name="account" method="post" action="javascript:logIn();">
      <table style="width:100%;">
      <tr>
        <td>Username</td><td style="width:100%;"><input style="width:100%;" type="text" name="username" /></td>
      </tr>
      <tr>
        <td>Password</td><td style="width:100%;"><input style="width:100%;" type="password" name="password" /></td>
      </tr>
      <tr>
      <td colspan="2" style="text-align:center;"><input type="submit" value="Log In" /><input type="button" value="Sign Up" onclick="signUp();" /></td>
      </tr>
      </table>
      </form>
    <?
    }?>
    </span></div>
    <div id="search">
    <hr />
    <span style="margin-left:auto;margin-right:auto;display:block;width:580px;">
      <form name="searchForm" action="javascript:search();">
      <div style="display:block;">
        <div>
          <h3>Keywords</h3>
          <input type="text" name="keywords" onKeyUp="search();" /><br />
        </div>
        <div>
          <h3>Location</h3>
          <select name="location" onChange="search();">
            <option value="any">Any</option>
            <option value="near">Nearby</option>
            <option value="belconnen">Belconnen</option>
            <option value="north">North Canberra</option>
            <option value="south">South Canberra</option>
            <option value="woden">Woden</option>
            <option value="tuggeranong">Tuggeranong</option>
            <option value="gungahlin">Gungahlin</option>
            <option value="surroundings">Surroundings</option>
          </select>
        </div>
        <div>
          <h3>Cuisine</h3>
          <select name="cuisine" onChange="search();">
            <option value="any">Any</option>
            <option value="chinese">Chinese</option>
            <option value="malaysian">Malaysian</option>
            <option value="australian">Australian</option>
            <option value="european">European</option>
            <option value="french">French</option>
            <option value="italian">Italian</option>
          </select>
        </div>
        <div>
          <h3>Atmosphere</h3>
          <select name="atmosphere" onChange="search();">
            <option value="any">Any</option>
            <option value="fine">Fine-dining</option>
            <option value="pub">Pub or club</option>
            <option value="winery">Winery</option>
            <option value="cafe">Cafe</option>
            <option value="bar">Bar</option>
          </select>
        </div>
      </div>
      <div style="display:block;">
        <div>
          <h3>Accessibility</h3>
          <input type="checkbox" name="accessibility" value="wheelchair" onChange="search();" /> Wheelchair accessible<br />
          <input type="checkbox" name="accessibility" value="parking" onChange="search();" /> Convenient parking<br />
          <input type="checkbox" name="accessibility" value="vegetarian" onChange="search();" /> Vegetarian options<br />
          <input type="checkbox" name="atmosphere" value="children" onChange="search();" /> Child-friendly<br />
        </div>
        <div>
          <h3>Open</h3>
          <input type="checkbox" name="open" value="breakfast" onChange="search();" /> Breakfast<br />
          <input type="checkbox" name="open" value="lunch" onChange="search();" /> Lunch<br />
          <input type="checkbox" name="open" value="dinner" onChange="search();" /> Dinner<br />
        </div>
        <div>
          <h3>Status</h3>
          <input type="checkbox" name="status" value="unvisited" onChange="search();" /> Unvisited<br />
          <input type="checkbox" name="status" value="top" onChange="search();" /> Top 10<br />
        </div>
        <div>
          <h3>Order Results</h3>
          <select name="order" onChange="search();">
            <option value="any">Alphabetically</option>
            <option value="fine">by Ranking</option>
            <option value="pub">by Distance</option>
          </select>
        </div>
      </div>
      </form>
    </span>
    </div>
    <div id="about">
      <hr />
      Restaurant Guide description...
    </div>
  </div>
  <div style="text-align:center;">
  <div id="restaurants">
  <?displayAllRestaurants();?>
  </div>
  </div>
</div>
</body>
</html>