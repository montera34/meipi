<?
	$skipIdMeipiCheck = TRUE;
	require_once("functions/meipi.php");
	require_once("functions/language.php");

	
	$errors = "";

	$aUserUpdate = getUserUpdateFromRequest($_REQUEST);
	if($aUserUpdate["valid"])
	{
		updateUser($aUserUpdate);
	}
	else
	{
		$errors .= getErrors($aUserUpdate);
	}

	$aUser = getUserInfoParamsFromRequest($_REQUEST);
	if(count($aUser)==0)
	{
		// Get username from path info
		$dbLink = dbConnect();
		$aUser["user"] = encode(substr($_SERVER["PATH_INFO"], 1));
	}
	$aUserInfo = getUserInfo($aUser);

	$aMessage = getMessageFromRequest($_REQUEST, $aUserInfo["user"]["id_user"]);
	if($aMessage["valid"])
	{
		sendMessage($aMessage);
	}
	else
	{
		$errors .= getErrors($aMessage);
	}

	if(isLogged() && getIdUser()==$aUserInfo["user"]["id_user"])
	{
		$aDelete = getDeleteMessageFromRequest($_REQUEST);
		if($aDelete["valid"])
		{
			deleteMessage($aDelete);
		}

		$aMessages = getMessages();
		markMessagesAsRead();
	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?= getString("Title User Page") ?>  <?= $aUserInfo["user"]["login"] ?>- <?= getString("Title: meipi - collaborative spaces") ?></title>
	<? getHead() ?>
	<script src="<?= $commonFiles ?>js/functions.js" type="text/javascript"></script>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $google_maps_key ?>" type="text/javascript"></script>
	<script type="text/javascript">
	//<![CDATA[
		function onLoad()
		{
<?
	if(strlen($aUserInfo["user"]["id_user"])==0)
	{
?>
			showMessage("<?= getString("User not found") ?>");
<?
	}
?>
		}
	// ]]>
	</script>
</head>

<body onload="onLoad()">
	<div id="screen">
<? getCommonHeader(getString("User").": ".$aUserInfo["user"]["login"]) ?><!-- string: "xxxx" -->
<?
	if(strlen($aUserInfo["user"]["id_user"])==0)
  {
?>
		<div id="descrip">
			<div id="descrip_ori" <?= ($hideDesc ? "style=\"display: none;\"" : "") ?>>
			 <?= getString("user not found") ?>
			</div>
		</div>

		<div id="user" <?= ($hideHome ? "style=\"display: none;\"" : "") ?>>
			<div><?= getString("user not exist") ?></div>
		</div>
<?
	}
	else
	{
?>
		<div id="descrip">
			<div id="descrip_ori" <?= ($hideDesc ? "style=\"display: none;\"" : "") ?>>
				<?= getString("users page") ?> <b><?= $aUserInfo["user"]["login"] ?></b>.<br/>
				<? // Puedes editar algunos campos de tu perfil. ?>
			</div> 
		</div><!-- end id descrip -->
	
<?
	if(strlen($errors)>0)
	{
		echo "<div class=\"result\" id=\"userResult\"><h3>".getString("Errors found").":</h3>".$errors."</div>";
	}
?>
		<div id="user" <?= ($hideHome ? "style=\"display: none;\"" : "") ?>>
			<div id="formulario-user" class="formulario">
				<div class="fo-tabla">

					<div class="fo-fila">
							<div id="param_login" class="<?= ($userParams["param_login"] ? "fo-error" : "fo-type") ?>"><?= getString("User") ?></div>
							<div class="fo-input"><?= $aUserInfo["user"]["login"] ?></div>
							<div class="fo-desc">&nbsp;</div>
					</div>

<?
					// Profile picture
					if(strlen($aUserInfo["user"]["image"])>0)
					{
?>
					<div class="fo-fila">
							<div id="param_picture" class="<?= ($userParams["param_picture"] ? "fo-error" : "fo-type") ?>"><?= getString("Profile picture") ?></div>
							<div class="fo-input"><img src="<?= $commonFiles.$dirProfileProfile ?>/<?= $aUserInfo["user"]["image"] ?>" /></div>
							<div class="fo-desc">&nbsp;</div>
					</div>
<?
					}
					else
					{
?>
					<div class="fo-fila">
							<div id="param_picture" class="<?= ($userParams["param_picture"] ? "fo-error" : "fo-type") ?>"><?= getString("Profile picture") ?></div>
							<div class="fo-input"><img src="<?= $commonFiles ?>images/default_big.jpg" /></div>
							<div class="fo-desc">&nbsp;</div>
					</div>
<?
					}

					if(strlen($aUserInfo["user"]["web"])>0)
					{
?>
					<div class="fo-fila">
							<div id="param_web" class="<?= ($userParams["param_web"] ? "fo-error" : "fo-type") ?>"><?= getString("Web") ?></div>
							<div class="fo-input"><? if(strlen($aUserInfo["user"]["web"])>0) { ?><a href="<?= safeForJavascript($aUserInfo["user"]["web"]) ?>"><?= encode($aUserInfo["user"]["web"]) ?></a><? } else { ?>&nbsp;<? } ?></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
					</div>
<?
					}
/*
?>
						<div class="fo-fila">
							<div id="param_registered" class="<?= ($userParams["param_registered"] ? "fo-error" : "fo-type") ?>"><?= getString("Registered") ?></div><!-- string: registered -->
							<div class="fo-input"><?= $aUserInfo["user"]["dateFormatted"] ?></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>

<?
*/
					if(strlen($aUserInfo["user"]["fullname"])>0)
					{
?>
					<div class="fo-fila">
							<div id="param_fullname" class="<?= ($userParams["param_fullname"] ? "fo-error" : "fo-type") ?>"><?= getString("Full name") ?></div><!-- string: Full name -->
							<div class="fo-input"><?= $aUserInfo["user"]["fullname"] ?></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
					</div>
<?
					}

					if(strlen($aUserInfo["user"]["about"])>0)
					{
?>
					<div class="fo-fila">
							<div id="param_about" class="<?= ($userParams["param_about"] ? "fo-error" : "fo-type") ?>"><?= getString("About me") ?></div><!-- string: About me -->
							<div class="fo-input"><?= $aUserInfo["user"]["about"] ?></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
					</div>
<?
					}
?>

				</div> <!--end fo-tabla -->

<?
	if(isLogged() && getIdUser()==$aUserInfo["user"]["id_user"])
	{
				// Update user info form
?>
				<form id="update_user_el" name="update_user_form" method="post" action="<?= getProfilePage($aUserInfo["user"]["id_user"], $aUserInfo["user"]["login"]) ?>" enctype="multipart/form-data" style="display: none;">
					<input type="hidden" name="update" value="true" />
					<div class="fo-tabla" id="update_user">
						<div class="fo-fila">
							<div id="param_login_update" class="<?= ($userParams["param_login"] ? "fo-error" : "fo-type") ?>"><?= getString("User") ?></div>
							<div class="fo-input"><?= $aUserInfo["user"]["login"] ?></div>
							<div class="fo-desc">&nbsp;</div>
						</div>

						<div class="fo-fila">
							<div id="param_mail_update" class="<?= ($userParams["param_mail"] ? "fo-error" : "fo-type") ?>"><?= getString("Mail") ?></div>
							<div class="fo-input"><input type="text" name="mail" value="<?= safeForJavascript($aUserInfo["user"]["mail"]) ?>" /></div>
							<div class="fo-desc"><?= getString("Your mail will not be shared") ?></div>
						</div>
						<div class="fo-fila">
							<div id="param_mail_subscription_update" class="<?= ($userParams["param_mail_subscription"] ? "fo-error" : "fo-type") ?>"><?= getString("Mail subscription") ?></div>
							<div class="fo-input"><input type="checkbox" name="mail_subscription" <?= ($aUserInfo["user"]["mail_subscription"]==1 ? "checked" : "") ?> /></div>
							<div class="fo-desc">&nbsp;</div>
						</div>

						<div class="fo-fila">
							<div id="param_web_update" class="<?= ($userParams["param_web"] ? "fo-error" : "fo-type") ?>"><?= getString("Web") ?></div>
							<div class="fo-input"><input type="text" name="web" value="<?= safeForJavascript($aUserInfo["user"]["web"]) ?>" /></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>

						<div class="fo-fila">
							<div id="param_fullname_update" class="<?= ($userParams["param_fullname"] ? "fo-error" : "fo-type") ?>"><?= getString("Full name") ?></div><!-- string: Full name -->
							<div class="fo-input"><input type="text" name="fullname" value="<?= safeForJavascript($aUserInfo["user"]["fullname"]) ?>" /></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>

						<div class="fo-fila">
							<div id="param_about_update" class="<?= ($userParams["param_about"] ? "fo-error" : "fo-type") ?>"><?= getString("About me") ?></div><!-- string: About me -->
							<div class="fo-input"><textarea name="about"><?= replaceBr($aUserInfo["user"]["about"]) ?></textarea></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>

						<div class="fo-fila">
							<div id="param_old_pwd_update" class="<?= ($userParams["param_old_pwd"] ? "fo-error" : "fo-type") ?>"><?= getString("Old password") ?></div>
							<div class="fo-input"><input type="password" name="old_pwd" /></div>
							<div class="fo-desc"><?= getString("Enter your old password if you want to update it") ?></div>
						</div>
						<div class="fo-fila">
							<div id="param_pwd1_update" class="<?= ($userParams["param_pwd1"] ? "fo-error" : "fo-type") ?>"><?= getString("New password") ?></div>
							<div class="fo-input"><input type="password" name="pwd1" /></div>
							<div class="fo-desc"><?= getString("Enter your new password if you want to update it") ?></div>
						</div>
						<div class="fo-fila">
							<div id="param_pwd2_update" class="<?= ($userParams["param_pwd2"] ? "fo-error" : "fo-type") ?>"><?= getString("Repeat new password") ?></div>
							<div class="fo-input"><input type="password" name="pwd2" /></div>
							<div class="fo-desc"><?= getString("Repeat your new password to confirm it") ?></div>
						</div>

						<div class="fo-fila">
							<div id="param_picture_update" class="<?= ($userParams["param_picture"] ? "fo-error" : "fo-type") ?>"><?= getString("Profile picture") ?></div><!-- string: Profile picture -->
							<div class="fo-input"><input type="file" name="picture" /></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>

						<div class="fo-fila">
							<div id="param_language" class="<?= ($userParams["param_language"] ? "fo-error" : "fo-type") ?>"><?= getString("Favourite language") ?></div><!-- string: Prefered language -->
							<div class="fo-input">
								<select name="lang" style="width:80px;font-size:1em;" >
									<?
										global $lang, $meipiLangs, $meipiLanguages;
										for ($j=0; $j<count($meipiLangs); $j++)
										{
											echo "<option value=\"".$meipiLangs[$j]."\"".($lang==$meipiLangs[$j] ? " selected" : "").">".$meipiLanguages[$j]."</option>\n";
										}
									?>
						  	 </select>
							</div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>

						<div class="fo-fila">
							<div id="param_submit" class="<?= ($userParams["param_submit"] ? "fo-error" : "fo-type") ?>"><?= getString("Submit") ?></div>
							<div class="fo-input"><input type="submit" value="<?= getString("Submit") ?>" /></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>

					</div> <!--end fo-tabla -->
				</form>
				<div class="fo-tabla">
					<button id="update_user_button" onclick="javascript:showElement('update_user_el'); hideElement('update_user_button');"><?= getString("Update your profile") ?></button>
				</div> <!--end fo-tabla -->

<?			// Update user info form - End

				// Internal messages
				if(count($aMessages)>0)
				{
?>
					<div class="messages">
<?
					foreach($aMessages as $message)
					{
						$msgId = $message["id_message"];
						$from = $message["from"];
						$fromUser = getUser($message["from"]);
						$messageText = $message["message"];
						$read = $message["read"];
						$date = $message["dateFormatted"];
?>
						<div class="message <?= ($read ? "read" : "unread") ?>">
<?
								$aUserInfoParams["id_user"] = intval($from);
								$aUserInfoMessage = getUserInfo($aUserInfoParams);

								if(strlen($aUserInfoMessage["user"]["image"])>0)
								{
									$fromProfilePicture = $commonFiles.$dirProfileSquare.$aUserInfoMessage["user"]["image"];
								}
								else
								{
									$fromProfilePicture = $commonFiles."images/default.jpg";
								}
?>
								<span class="messageSenderPicture"><a href="<?= getProfilePage($from, $fromUser) ?>"><img src="<?= $fromProfilePicture ?>" /></a></span>	
							<div class="messageDelete"><a href="<?= setParams(getProfilePage($aUserInfo["user"]["id_user"], $aUserInfo["user"]["login"]), Array("msg" => "", "delete" => "true", "msg_id" => $msgId)) ?>"><?= getString("Delete") ?></a></div>
							<div class="messageSender"><?= getString("Message from") ?>
								<span class="messageSenderName"><a href="<?= getProfilePage($from, $fromUser) ?>"><?= $fromUser ?></a></span>
							</div><span class="messageDate"><?= $date ?></span>
							<div class="messageText"><?= $messageText ?></div>

							<div style="clear:both;"></div>
						</div>
<?
					}
?>
					</div>
<?
				}
				// Internal messages - End
	}
	else if(isLogged())
	{
?>
				<form id="send_message_el" name="send_message_form" method="post" action="<?= getProfilePage($aUserInfo["user"]["id_user"], $aUserInfo["user"]["login"]) ?>" style="display: none;">
					<input type="hidden" name="send" value="true" />
					<div class="fo-tabla" id="send_message">
						<div class="fo-fila">
							<div id="param_message" class="<?= ($userParams["param_message"] ? "fo-error" : "fo-type") ?>"><?= getString("Message") ?></div><!-- string: Message -->
							<div class="fo-input"><textarea name="message"></textarea></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>

						<div class="fo-fila">
							<div id="param_submit" class="<?= ($userParams["param_submit"] ? "fo-error" : "fo-type") ?>"><?= getString("Submit") ?></div>
							<div class="fo-input"><input type="submit" value="<?= getString("Submit") ?>" /></div>
							<div class="fo-desc">&nbsp;</div><!-- string: optional -->
						</div>
					</div>
				</form>
				<div class="fo-tabla">
					<div id="send_message_button" class="boton"><a href="javascript:showElement('send_message_el'); hideElement('send_message_button');"><?= getString("Send a message to this user") ?></a></div>
				</div> <!--end fo-tabla -->
<?
	}
	else // user not logged
	{
?>
				<div class="fo-tabla">
					<div class="boton"><a href="javascript:showLoginFormParams('');"><?= getString("Log in to send a message to this user") ?></a></div>
				</div> <!--end fo-tabla -->
<?
	}
?>
		</div><!--end home -->
<?
	} // end else (user found)
?>
<? getCommonFooter() ?>
	</div><!-- end id screen -->

	<? getLoginForm($_REQUEST); ?>
	<? getMessageWindow(); ?>

</body>
</html>
