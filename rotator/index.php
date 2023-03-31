<?php
/*
	Free URL Rotator Script
	
	Version: 1.2.1
	
	© iTechDev and LJScripts.com
*/

/* Your configuration file location */
$inc_file = "configuration.php";

/*
	DO NOT EDIT BELOW THIS LINE
*/
if (phpversion() <= '4.0.6') {$_SERVER=$HTTP_SERVER_VARS;$_POST=$HTTP_POST_VARS;$_GET=$HTTP_GET_VARS;$_ENV=$HTTP_ENV_VARS;$_COOKIE=$HTTP_COOKIE_VARS;}
require($inc_file);
if (empty($_SERVER['QUERY_STRING']))
{
	$urls = file($url_file);
	$url = '';
	$cnt = 0;
	while (empty($url) and $cnt < 5)
	{
		$url = trim($urls[mt_rand(0, sizeof($urls) - 1)]);
		$cnt++;
		if ($cnt == 3) optimizeURLS();
	}
	if (empty($url))
	{
		echo("Could not find a URL to display. Please add one first.");
		exit();
	}
	header("Location: $url");
	exit();
}
if (!empty($_SERVER['QUERY_STRING']) or strcmp($_POST['admin'], 'edurls') == 0 or strcmp($_POST['page'], 'edadmin') == 0 or 
	strcmp($_GET['admin'], 'edurls') == 0 or strcmp($_GET['admin'], 'edadmin') == 0)
{
	@session_start();
	checkInvasion();
	chkConfig($url_file, true);
	if (strcmp($_SERVER['QUERY_STRING'], 'LOGOUT') == 0)
	{
		$_SESSION = array();
		@session_destroy();
		header("Location: index.php?admin=edurls");
		exit();
	}
	if (!empty($_POST['usern']) and !empty($_POST['passwd']))
	{
		$_SESSION['admin_user'] = trim($_POST['usern']);
		$_SESSION['admin_pass'] = md5(trim($_POST['passwd']));
		header("Location: ".$_SERVER['PHP_SELF'] . "?".$_SERVER['QUERY_STRING']);
		exit();
	}
	if (!isset($_SESSION['admin_user']) or !isset($_SESSION['admin_pass']))
	{
		$_SESSION = array();
		@session_destroy();
		loginScreen();
		exit();
	}
	else
	{
		if (strcmp($_SESSION['admin_user'], $login_username) != 0 or strcmp($_SESSION['admin_pass'], md5($login_password)) != 0)
		{
			$_SESSION = array();
			@session_destroy();
			loginScreen();
			echo("<script language=\"javascript\" type=\"text/javascript\">alert('Admin login details incorrect please enter the correct details!');</script>");
			exit();
		}	
	}
}
?>
<html>
<head>
<title>glbohrer top sites</title>
</head>
<body>
<center><h1>Please wait for the page to load ok?</h1><p>Beginning of the construction of pages linked to the glbohrer site with a brief sample in the webcam category gallery...</p>
  </br>
<script type="text/javascript" src="//traffdaq.com/delivery/gl/92364?&categories=general,gay,ebony,amateur,teen,shemale,bbw,babe,vr,dating,women_dating,gay_dating,lesbian_dating,trans_dating,webcam&rows=4&columns=4&width=200&height=300&borderColor=000000&borderHoverColor=ff0096&borderWidth=2&showText=1&textColor=000000&textHoverColor=ff0096"></script>
  </br>
<p>Online webmaster GLBOHRER.</p>
  </br>
  <center>
</body>
</html>
