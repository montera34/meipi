<?
	$configsPath = "../";
	require_once "../functions/meipi.php";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title><?= getString("Password Required") ?></title></head>
<body>
	<form action="<?= setParams($_SERVER["PHP_SELF"], null) ?>" method="post">
		Insert the password required for this meipi: <input type="password" name="meipiPassword" />
		<input type="submit" />
	</form>
</body>
</html>
