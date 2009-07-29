<?
	header("Content-type: text/html; charset=iso-8859-1");

	$skipIdMeipiCheck = TRUE;
	$configsPath = "../";
	require_once("../functions/meipi.php");
	unset($skipIdMeipiCheck);

	$aUser=getLoginFromRequest($_REQUEST);
	$nextPage = $_REQUEST["next"];
	if($aUser["ok"])
	{
		$aLoggedUser=login($aUser);
		if($aLoggedUser["ok"])
		{
			// Logged in
		}
		else
		{
			$sErrors = getErrors($aLoggedUser);
		}
	}

	endRequest();

	$nextPage=$_REQUEST["next"];
	if(strlen($nextPage)==0 || strpos($nextPage, "login.php")!==FALSE || strpos($nextPage, "registration.php")!==FALSE || strpos($nextPage, "logout.php")!==FALSE)
	{
		$nextPage="index.php";
	}
	if($aLoggedUser["ok"])
	{
		$nextPage = setParams($nextPage, Array("msg" => "Logged in", "showLoginForm" => ""));
	}
	else
	{
		$nextPage = setParams($nextPage, Array("showLoginForm" => "true", "login" => $aUser["login"], "msg" => $sErrors, "mail" => $aUser["mail"], "web" => $aUser["web"]));
	}

	Header("Location: $nextPage");
?>
