<?
	require_once("functions/common.php");
	global $commonFiles;
?>
		<div id="ante">
<?
		if(isLogged())
		{
			$nextPage = $_SERVER["REQUEST_URI"];
?>
			<a title="<?= getString("Log out") ?> <?= getString("from") ?> <?= getLogin() ?>" href="<?= setParam($commonFiles."actions/logout.php", "next", $nextPage) ?>"><?= getString("Log out") ?></a>

			<a href="<?= $commonFiles ?>myprofile.php"><?= getString("My profile") ?></a>
<?
			$aMessages = getMessages(FALSE);
			$iMessages = count($aMessages);
			if($iMessages>0)
			{
?>
				<a href="<?= getProfilePage(getIdUser(), getLogin()) ?>"><?= $iMessages ?> <?= getString("message".($iMessages>1 ? "s" : "")) ?></a>
<?
			}
		} else {
?>
			<a href="javascript:showLoginForm();"><?= getString("Log in") ?></a>
<?
		}
?>
		</div><!-- end id ante -->
		<div id="pre">
			<a href="<?= $commonFiles ?>meipi.php" title="home"><img src="<?= $commonFiles ?>images/logo.gif" alt="Meipi logo" border="0" class="logo" /></a>
			<h1><?= $commonHeaderTitle ?></h1>
</div><!-- end id pre -->
