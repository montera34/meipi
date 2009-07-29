<?

	header("Content-type: text/html; charset=iso-8859-1");

	$skipIdMeipiCheck = TRUE;
	$configsPath = "../";
	require_once("../functions/meipi.php");

	logout();
	endRequest();

	$nextPage = $_REQUEST["next"];
	
	if(strlen($nextPage)==0 || strpos($nextPage, "login.php")!==FALSE || strpos($nextPage, "registration.php")!==FALSE || strpos($nextPage, "logout.php")!==FALSE)
	{
		$nextPage="index.php";
	}
	$nextPage = setParams($nextPage, Array("msg" => "", "showLoginForm" => ""));
	
	Header("Location: $nextPage");
?>
