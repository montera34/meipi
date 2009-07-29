<?
	$configsPath = "../";
	$languagePath = "../";
	require_once($configsPath."functions/common.php");
	require_once($configsPath."functions/language.php");

	$aParamsCancelMail = getCancelMailSubscriptionParamsFromRequest($_REQUEST);
	if($aParamsCancelMail["ok"])
	{
		$resultCancelMail = cancelMailSubscription($aParamsCancelMail);
		//echo "login: ".$aParamsCancelMail["login"]."<br/>";
		//echo "code: ".$aParamsCancelMail["code"]."<br/>";
		//echo "mail: ".$aParamsCancelMail["mail"]."<br/>";
	}
	else
	{
		//echo "wrong parameters!<br/>";
	}

	endRequest();
	if ($resultCancelMail)
	{
		Header("Location: /?msg=Mail+subscription+successfully+cancelled");
	}
	else
	{
		Header("Location: /?msg=Sorry,+operation+was+not+completed!");
	}
?>
