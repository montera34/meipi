<?
	$configsPath = "../";
	$languagePath = "../";
	require_once($configsPath."functions/common.php");
	require_once($configsPath."functions/language.php");

	header("Content-type: text/html; charset=iso-8859-1");

	$aParamsReset = getResetPasswordParamsFromRequest($_REQUEST);
	if($aParamsReset["ok"])
	{
		$resultReset = resetPassword($aParamsReset);
	}

	$aParams = getSendPasswordParamsFromRequest($_REQUEST);
	if($aParams["ok"])
	{
		$result = sendPasswordCode($aParams);
	}

	$nextPage=$_REQUEST["next"];
	if(strlen($nextPage)==0 || strpos($nextPage, "login.php")!==FALSE || strpos($nextPage, "registration.php")!==FALSE || strpos($nextPage, "logout.php")!==FALSE)
	{
		$nextPage="index.php";
	}
	if($resultReset)
	{
		$nextPage = setParams($nextPage, Array("msg" => "Password reseteada", "showLoginForm" => ""));
	}
	else
	{
		$nextPage = setParams($nextPage, Array("showLoginForm" => "true", "msg" => "Error reseteando password. No se pudo completar la operaci&oacute;n.", "showPwdForm" => "true" ));
	}

	Header("Location: $nextPage");
?>
