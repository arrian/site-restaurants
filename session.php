<?php
//checks if the user is logged in

function validateUser()
{
  startSession();
  session_regenerate_id();
  $_SESSION['valid'] = 1;
}

function loggedIn()
{
  startSession();
  if(isset($_SESSION['valid']) && $_SESSION['valid']) return true;
  return false;
}

function destroyUser()
{
  startSession();
  $_SESSION = array();
  session_destroy();
}

function startSession()
{
  if (!isset ($_SESSION))
  {
    session_start();
  }
}
?>
