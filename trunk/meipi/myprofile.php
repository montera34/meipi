<?
	$skipIdMeipiCheck = TRUE;
	require_once("functions/meipi.php");
	require_once("functions/language.php");

	if(isLogged())
	{
		$idUser = getIdUser();
		$login = getLogin();

		$nextPage = getProfilePage($idUser, $login);
		Header("Location: $nextPage");
	}
	else
	{
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?= getString("Title My Profile") ?> - <?= getString("Title: meipi - collaborative spaces") ?></title>
	<? getHead() ?>
	<script src="<?= $commonFiles ?>js/functions.js" type="text/javascript"></script>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $google_maps_key ?>" type="text/javascript"></script>
	<script type="text/javascript">
	//<![CDATA[
		function onLoad()
		{
			showLoginForm();
		}
	// ]]>
	</script>
</head>

<body onload="onLoad()">
	<div id="screen">
<?
 		getCommonHeader(getString("My profile page"));
?>
		<p style="margin: 100px 0 100px 0;">
			<?= getString("Log in to view your profile") ?>
		</p>
<? getCommonFooter() ?>
	</div><!-- end id screen -->

	<? getLoginForm($_REQUEST); ?>
	<? getMessageWindow(); ?>
<?
	}

?>
