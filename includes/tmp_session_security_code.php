<?php

if (!isset($_SESSION))
{
  session_start();
}

if (isset($SESSION['HTTP_USER_AGENT'])
{
  if ($_SESSION['HTTP_USER_AGENT']  != md5($_SERVER['HTTP_USER_AGENT'] . SALT))
  {
  /* TODO redirect to login form */
  exit;
  }
}
else
{
  // Set the session variable if it isn't already set
  $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT'] . SALT);
}



?>
