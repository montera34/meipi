<?
	$configsPath = "../";
	$skipIdMeipiCheck = TRUE;
	require_once("../functions/meipi.php");

	// TODO: Change recaptcha order (check user even captcha is not OK)
	$resp = recaptcha_check_answer ($reCaptchaPrivateKey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);
	if (!$resp->is_valid) {
		$captchaOK = false;
		$sErrors = $resp->error;
	} else {
		$captchaOK = true;
	
		$aUser=getUserFromRequest($_REQUEST);
		if($aUser["ok"])
		{
			$aRegisteredUser=registerUser($aUser);
			if($aRegisteredUser["ok"])
			{
				endRequest();
			}
			else
			{
				$sErrors = getErrors($aRegisteredUser);
			}
		}
		else
		{
			$sErrors = getErrors($aUser);
		}

		endRequest();

	}

	$nextPage=$_REQUEST["next"];
	if(strlen($nextPage)==0 || strpos($nextPage, "login.php")!==FALSE || strpos($nextPage, "registration.php")!==FALSE || strpos($nextPage, "logout.php")!==FALSE)
	{
		$nextPage="index.php";
	}
	if($aRegisteredUser["ok"] && $captchaOK)
	{
		$nextPage = setParams($nextPage, Array("msg" => getString("Registered"), "showLoginForm" => "", "login" => "", "mail" => ""));
	}
	else
	{
		$nextPage = setParams($nextPage, Array("showLoginForm" => "true", "login" => $aUser["login"], "msg" => $sErrors, "mail" => $aUser["mail"], "web" => $aUser["web"]));
	}

	Header("Location: $nextPage");
?>
