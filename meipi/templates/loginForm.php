<?
	global $configsPath;
  require_once($configsPath."functions/common.php");
	global $commonFiles;
	global $legalUrl;
	global $reCaptchaPublicKey;

	$nextPage = removeQuotes($_SERVER["REQUEST_URI"]);
	$bIsVisiblePwd = ($request["showPwdForm"]=="true" && !isLogged());
	$bIsVisible = ($request["showLoginForm"]=="true" && !isLogged() && !$bIsVisiblePwd);
?>
<div <?= ($bIsVisible || $bIsVisiblePwd ? "" : "style=\"display: none;\"") ?> id="loginWindow">
	<div class="loginWindow">
		<div class="logh">
			<a href="javascript: cancelLogin()"><img src="<?= $commonFiles ?>images/cancel.gif" title="cerrar" /></a>
			<?= getString("Log in") ?>
			<?= $request["message"] ?>
		</div>
		<div <?= ($bIsVisible ? "" : "style=\"display: none;\"") ?> id="logconten">
			<div id="log" class="formulario">
				<form name="login" action="<?= $commonFiles ?>actions/login.php" method="post">
					<input type="hidden" name="next" value="<?= $nextPage ?>" />
					<input type="hidden"  name="params" value="" />
							
					<h2><?= getString("Registered User") ?></h2>
					<div class="fo-tabla">
						<div class="fo-fila">
							<div class="fo-type">
								<?= getString("Login") ?> *
							</div>
							<div class="fo-input">
								<input type="text" name="login" class="input" value="<?= removeQuotes($request["login"]) ?>" />
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type">
								<?= getString("Password") ?> *
							</div>
							<div class="fo-input">	
								<input type="password" name="pwd1" class="input" />
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type">
									&nbsp;
							</div>
							<div class="fo-input">	
								<input type="submit" class="regboton" value="<?= getString("Log In session") ?>" />
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type">
									&nbsp;
							</div>
							<div class="fo-input">	
								<a href="javascript:showPasswordRecoveryForm()"><?= getString("If forgotten password") ?></a>
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type">
									&nbsp;
							</div>
							<div class="fo-input">	
								<?= getString("I have read the") ?> <a href="<?= $legalUrl ?>" target="_blank"><?= getString("legal advice") ?></a>
							</div>
						</div>

					</div>
				</form>
				
			</div>
			
			<div id="reg" class="formulario">
				<form name="registration" action="<?= $commonFiles ?>actions/registration.php" method="post">
					<input type="hidden" name="next" value="<?= $nextPage ?>" />
					<input type="hidden" name="params" value="" />
					<h2><?= getString("New User") ?></h2>
					<div class="fo-tabla">
						<div class="fo-fila">
							<div id="param_login" class="<?= ($userParams["param_login"] ? "fo-error" : "fo-type") ?>"><?= getString("Login") ?> *</div>
							<div class="fo-input"><input class="input" size="30" name="login" value="<?= removeQuotes($_REQUEST["login"]) ?>"/></div>
							<div class="fo-desc"><?= getString("Valid chars") ?></div>
						</div>
						<div class="fo-fila">
							<div id="param_pwd1" class="<?= ($userParams["param_pwd1"] ? "fo-error" : "fo-type") ?>"><?= getString("Password") ?> *</div> 
							<div class="fo-input"><input type="password" class="input" size="30" name="pwd1" value=""/></div>
							<div class="fo-desc">&nbsp;</div>
						</div>
						<div class="fo-fila">
							<div id="param_pwd2" class="<?= ($userParams["param_pwd2"] ? "fo-error" : "fo-type") ?>"><?= getString("Repeat password") ?> *</div>
							<div class="fo-input"><input type="password" class="input" size="30" name="pwd2" value=""/></div>
							<div class="fo-desc">&nbsp;</div>
						</div>
						<div class="fo-fila">
							<div id="param_mail" class="<?= ($userParams["param_mail"] ? "fo-error" : "fo-type") ?>"><?= getString("E-Mail") ?> *</div> 
							<div class="fo-input"><input class="input" name="mail"  size="30" value="<?= removeQuotes($_REQUEST["mail"]) ?>"/></div>
							<div class="fo-desc">&nbsp;</div>
						</div>
						<div class="fo-fila">
							<div id="param_web" class="<?= ($userParams["param_web"] ? "fo-error" : "fo-type") ?>"><?= getString("Web") ?></div>
							<div class="fo-input"><input class="input" name="web"  size="30" value="<?= removeQuotes($_REQUEST["web"]) ?>"/></div>
							<div class="fo-desc"><?= getString("Optional") ?></div>
						</div>
					</div> <!--end fo-tabla -->
					<div class="fo-tabla">
						<div class="fo-fila">
							<div id="param_conditions" class="<?= ($meipiParams["param_conditions"] ? "fo-error" : "fo-type") ?>"><?= getString("Legal advice") ?> *</div>
							<div class="fo-input"><span class="legal"><input type="checkbox" name="conditions" value="yes" <?= $_REQUEST["conditions"]=="yes" ? "checked=\"checked\"" : "" ?>/> <?= getString("I have read the") ?> <a href="<?= $legalUrl ?>" target="_blank"><?= getString("legal advice") ?></a><br/></span></div>
							<div class="fo-desc"></div>
						</div>
					</div><!--fo-tabla -->
					<script>
						var RecaptchaOptions = {
							theme : 'custom',
							custom_theme_widget: 'recaptcha_widget',
							lang : 'es'
						};
					</script>
					<div class="fo-tabla" id="recaptcha_widget" style="display:none">
						<div class="fo-fila">
							<div id="param_captcha" class="<?= ($meipiParams["param_captcha"] ? "fo-error" : "fo-type") ?>"><?= getString("Security check") ?> *</div>
							<div class="fo-recapcha" id="recaptcha_image"></div>

						</div>
						<div class="fo-fila">
							<div class="fo-type">&nbsp;</div>
							<div class="fo-input"><input class="input" size="30" type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></div>
							<div class="fo-desc">
								<div class="recaptcha_only_if_incorrect_sol" style="color:red"><?= getString("Incorrect try...") ?></div>
								<span class="recaptcha_only_if_image"><?= getString("Enter the words above") ?></span>
								<span class="recaptcha_only_if_audio"><?= getString("Enter the numbers you hear") ?></span>
								<div><a href="javascript:Recaptcha.reload()"><?= getString("Get another CAPTCHA") ?></a></div>
								<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')"><?= getString("Audio CAPTCHA") ?></a></div>
								<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')"><?= getString("Image CAPTCHA") ?></a></div>
								<div><a href="javascript:Recaptcha.showhelp()"><?= getString("Help") ?></a></div>
							</div>
						</div>
						<? echo recaptcha_get_html($reCaptchaPublicKey); ?>
					</div><!--fo-tabla -->
					<div class="fo-tabla">
						<div class="fo-fila">
							<div class="fo-type">&nbsp;</div>
							<div class="fo-input"> 
							<input type="submit" class="regboton" value="<?= getString("Register") ?>" />
							</div>
							<div class="fo-desc">&nbsp;</div>
						</div>
					</div><!--fo-tabla -->
				</form>
			</div>
		</div>

		<div <?= ($bIsVisiblePwd ? "" : "style=\"display: none;\"") ?> id="pwdconten">
			<div id="pwdconten_form" class="formulario">
				<div id="emailPwd">
					<h2>1. <?= getString("Tell us your user") ?></h2>

					<form name="sendResetPasswordCode" method="post" onsubmit="return false;">
						<input type="hidden" name="next" value="<?= $nextPage ?>" />
						<div class="fo-tabla">
							<div class="fo-fila">
								<div class="fo-type"><?= getString("Login") ?>
								</div>
								<div class="fo-input">
									 <input type="text" name="login" />
								</div>
							</div>
							<div class="fo-fila">
								<div class="fo-type">&nbsp;
								</div>
								<div class="fo-input">
									<input type="button" onclick="sendPasswordRecoveryCode()" value="<?= getString("Submit") ?>"/>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div id="resetPwd">
					<h2>	2. <?= getString("Write the code") ?></h2>
					<form name="resetPassword" action="<?= $commonFiles ?>actions/resetPassword.php" method="post">
						<input type="hidden" name="next" value="<?= $nextPage ?>" />
						<div class="fo-tabla">
							<div class="fo-fila">
								<div class="fo-type">	<?= getString("Login") ?> 
								</div>
								<div class="fo-input">
									<input type="text" name="reset_password_login" />
								</div>
							</div>
							<div class="fo-fila">
								<div class="fo-type"><?= getString("Code") ?>  
								</div>
								<div class="fo-input">
									<input type="text" name="code" />
								</div>
							</div>
							<div class="fo-fila">
								<div class="fo-type">	<?= getString("Password") ?>
								</div>
								<div class="fo-input">
									<input type="password" name="pwd1" />
								</div>
							</div>
							<div class="fo-fila">
								<div class="fo-type"><?= getString("Repeat password") ?></div>
								<div class="fo-input">
									<input type="password" name="pwd2" />
								</div>
							</div>
							<div class="fo-fila">
								<div class="fo-type">&nbsp;
								</div>
								<div class="fo-input">
									<input type="submit" value="<?= getString("Submit") ?>"/>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>	
		</div>
	</div>
</div>
<?
	if($bIsVisible)
	{
		$form = (strlen($request["web"].$request["mail"])>0 ? "registration" : "login")
?>
	<script type="text/javascript">
		document.forms.<?= $form ?>.login.focus();
	</script>
<?
	}
?>
