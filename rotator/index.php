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
  <title>LJScripts Free Rotator Admin</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <style type="text/css">
   <!--
    body,td,ul { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;}
   -->
  </style>
 </head>
 <body link="#0000FF" vlink="#0000FF" alink="#0000FF">
  <table width="600" border="0" align="center" cellpadding="4" cellspacing="0" style="border: solid 1px #000000;">
   <tr>
    <td width="774" height="184" valign="top">
     <table width="100%">
      <tr><td>
	   <p style="font-size: 24px; font-weight: bold;">LJScripts Free Rotator Admin</p>
	   <p><a href="<?=$_SERVER['PHP_SELF']?>" target="_blank">Your Rotator</a> - Click to test or Right-Click and choose Add to Favorites.</p>
      </td></tr>
     </table>
<?php
if (strcmp($_GET['admin'], 'edadmin') == 0 or strcmp($_POST['admin'], 'edadmin') == 0)
{
	chkConfig($inc_file, false);
	if (!empty($_POST['adminuser']) and strcmp(md5($_POST['cpass']), $_SESSION['admin_pass']) == 0)
	{
		if (!empty($_POST['adminpass']) and strcmp(trim($_POST['adminpass']), trim($_POST['adminpass2'])) == 0) $newpass = trim($_POST['adminpass']);
		else $newpass = $login_password;
		$newuser = trim($_POST['adminuser']);
		$content = "<?php
\$login_username = \"$newuser\";
\$login_password = \"$newpass\";
\$url_file = \"$url_file\";
?>";
		file_writer("$inc_file",$content);
		echo "<h3>Admin details were changed successfully!</h3>";
		$_SESSION['admin_pass'] = md5($newpass);
	}
	else
	{
		?>
		<table width="100%">
		 <tr>
		  <td colspan="2"><h2>Change Admin Username</h2></td>
		 </tr>
		 <form action="<?=$_SERVER['PHP_SELF']?>?admin=edadmin" method="post">
		 <tr>
		  <td width="40%" nowrap="nowrap">Admin username:</td>
		  <td width="60%"><input type="text" name="adminuser" size="40" value="<?=$_SESSION['admin_user']?>" /></td>
		 </tr>
		 <tr>
		 <td colspan="2">&nbsp;</td>
		 </tr>
		  <tr>
		   <td nowrap="nowrap">New admin password:</td>
		   <td><input type="password" name="adminpass" size="40" value="" /></td>
		  </tr>
		  <tr>
		  <td nowrap="nowrap">Re-enter your new admin password:</td>
		  <td><input type="password" name="adminpass2" size="40" value="" /></td>
		  </tr>
		  <tr>
		    <td colspan="2" nowrap="nowrap">&nbsp;</td>
	       </tr>
		  <tr>
		    <td nowrap="nowrap">Current password: </td>
		    <td><input name="cpass" type="password" size="40" /></td>
	       </tr>
		  <tr>
		  <td colspan="2"><br /><br /><input type="submit" value="  Change Admin Details  " /></td>
		  </tr>
		 </form>
	  </table>
		<br><br><br><br>
		<?php
	}
}
elseif (strcmp($_GET['admin'], 'edurls') == 0 or strcmp($_POST['admin'], 'edurls') == 0)
{
	if (!empty($_POST['addnewurl']))
	{
		$newurl = trim($_POST['addnewurl']);
		$newURLs = getURLs($newurl);
		if (count($newURLs) > 0)
		{
			$fp = fopen($url_file, "a");
			for ($i = 0; $i < count($newURLs); $i++) @fwrite($fp, "$newURLs[$i]\n");
			@fclose($fp);
			echo "<h3>".count($newURLs)." New URL(s) successfully added.</h3>";
		}
		echo "<p><a href=\"".$_SERVER['PHP_SELF']."?admin=edurls\">Edit/Add more URL's</a></p>";
	}
	else if (count($_POST['del_url']) > 0)
	{
		$results = "";
		$fp = @fopen($url_file, "r");
		$read = @fread($fp, filesize($url_file));
		@fclose($fp);
		$read = str_replace("\r", "", $read);
		$up_tot = 0;
		for ($i = 0; $i < count($_POST['del_url']); $i++)
		{
			$cur_url = trim($_POST['del_url'][$i]);
			if (!empty($cur_url))
			{
				$read = str_replace("$cur_url\n", "", $read);
				$results .= "The URL \"$cur_url\" has been deleted<br />";
				$up_tot++;
			}
		}
		
		if ($up_tot > 0)
		{
			$read = trim($read);
			if (empty($read)) $results = "You cannot remove all URL's from this rotator!<br />";
			else
			{
				$fp = @fopen($url_file, "w");
				@fwrite($fp, $read."\n");
				@fclose($fp);
			}
		}
		echo "<p>$results</p>";
		echo "<br /><a href=\"{$_SERVER['PHP_SELF']}?admin=edurls\">Edit/Add more URL's</a>";
	}
	elseif (strcmp($_GET['optim'], 'true') == 0)
	{
		optimizeURLS();
		echo "<p>Successfully optimized the $url_file file.</p>";
		echo "<a href=\"{$_SERVER['PHP_SELF']}?admin=edurls\">Go back</a>";
	}
	else
	{
		?>
		<form action="<?=$_SERVER['PHP_SELF']?>?admin=edurls" name="addurlform" method="post">
		<h2>Add New URL(s)</h2>
		<p><b>Separate each URL with a new line. You must Enter http://<br />
	      <br />
		  </b>
	      <textarea name="addnewurl" rows="10" wrap="off" style="width: 600px;">http://</textarea>
	      </p>
		<p>
		  <input type="submit" value="  Add New URL(s)  " />
		  </p>
		</form>
		<br>
		<h2>Delete URL(s)</h2>
		<br><b><font style="color: red;">PLEASE NOTE:</font> There must be at least one site listed in your rotator to function correctly!</b><br /><br />
		<form action="<?=$_SERVER['PHP_SELF']?>?admin=edurls" method="post">
		<input type="hidden" name="delete_url" value="true" />
		<?php
		$urls = file($url_file);
		$overhead = 0;
		for ($i = 0; $i < sizeof($urls); $i++)
		{
			$durl = trim($urls[$i]);
			if (!empty($durl)) echo("<input type=\"checkbox\" name=\"del_url[]\" value=\"".$durl."\" /> ".$durl."<br />\n");
			else $overhead++;
		}
		?>
		<p><input type="submit" value="Delete Selected URL(s)" />
		<?php if ($overhead > 0) { ?>
		<input type="button" value="  Optimize " onClick="top.location.href='<?=$_SERVER['PHP_SELF']?>?admin=edurls&optim=true';" />
		<?php } ?>
		</p>
		</form>
		<?php
	}
}
?>
    </td>
	</tr>
	<tr>
	 <td align="center">
		<p><a href="<?=$_SERVER['PHP_SELF']?>?admin=edurls">Add/Remove URL's</a>
		&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="<?=$_SERVER['PHP_SELF']?>?admin=edadmin">Change Login Details</a>&nbsp;&nbsp;&nbsp;
		|&nbsp;&nbsp;&nbsp;<a href="<?=$_SERVER['PHP_SELF']?>?LOGOUT">Logout</a></p>
		<p style="font-size:10px;">
		<strong>|</strong> Free URL Rotator Version 1.2 &copy; <a href="http://www.ljscripts.com/" target="_blank">LJScripts.com</a> <strong>|</strong></p>
 	</td>
   </tr>
 </table>
 </body>
</html>
<?php
exit();

/*
	Functions
*/

function file_writer($fileurl,$contents)
{
	$file=@fopen($fileurl,'w') or die("$fileurl File Doesn't Exist");
	if($contents)
	{
		if(@fwrite($file,$contents))
		{
			@fclose($file);
			return true;
		}
	}
}


function chkConfig($url_file, $isURL = true)
{
	if (!file_exists($url_file))
	{
		echo "Error! The rotator is not correctly configured - The \"$url_file\" file is not found!";
		exit();
	}
	if ($isURL and filesize($url_file) < 1)
	{
		echo "Error! Your rotator has no sites saved, there must be at least 2 (two) sites entered.";
		exit();
	}
	if (!is_readable($url_file) or !is_writable($url_file))
	{
		echo "Error! The rotator is not correctly set - The $url_file file is not readable or writable (CHMOD to 777)!";
		exit();
	}
}

function loginScreen()
{
	?>
<html>
<head>
<title>Rotator Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.formcss {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
}
body,td,ul { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;}
-->
</style>
</head>

<body>
<p style="font-size: 18px;" align="center">Rotator Administration Login</p>
<form name="form_login" method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>">
<table width="20%" border="0" align="center" cellpadding="2" cellspacing="0">
  <tr>
    <td nowrap="nowrap"><strong><font size="2" face="Tahoma">Username : </font></strong></td>
    <td nowrap="nowrap"><input name="usern" type="text" class="formcss" size="40" /></td>
  </tr>
  <tr>
    <td nowrap="nowrap"><strong><font size="2" face="Tahoma">Password :</font></strong></td>
    <td><input name="passwd" type="password" class="formcss" size="40" /></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">&nbsp;</font></td>
    <td><div align="right">
      <input type="submit" class="formcss" value="  Login  " />
    </div></td>
  </tr>
</table>
</form>
</body>
</html>
	<?php
}

function getURLs($url_str)
{
	$ret_urls = array();
	$url_str = str_replace("\r", '', $url_str);
	$tmp_u = explode("\n", $url_str);
	for ($i = 0; $i < count($tmp_u); $i++)
	{
		$uval = trim($tmp_u[$i]);
		if (empty($uval)) continue;
		if (!empty($uval) and (preg_match('/http:\/\//i', $uval) or preg_match('/https:\/\//i', $uval)) and preg_match('/\./', $uval)) $ret_urls[] = $uval;
		else echo("<br /><b>$uval</b> was not a valid URL!");
	}
	return $ret_urls;
}

function checkInvasion()
{
	if (isset($_REQUEST['_SESSION']) or isset($_POST['_SESSION']) or isset($_GET['_SESSION']) or isset($_COOKIE['_SESSION']))
	{
		header("Location: index.php");
  		@session_destroy();
  		exit();
 	}
}

function optimizeURLS()
{
	global $url_file;
	$new_str = "";
	$urls = @file($url_file);
	for ($i = 0; $i < @sizeof($urls); $i++)
	{
		$ourl = trim($urls[$i]);
		if (!empty($ourl)) $new_str .= $ourl."\n";
	}
	$fp = @fopen($url_file, "w");
	@fwrite($fp, $new_str);
	@fclose($fp);
}
?>