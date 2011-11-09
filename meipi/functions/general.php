<?
	// Functions that do not need config files

	function isValidEmail($email)
	{
		return preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email) || 
		    		preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/',$email);
	}

	function encode($str)
	{
		$str = htmlentities($str, ENT_QUOTES, "cp1252");
		$str = str_replace("\r\n", "<br/>", $str);
		$str = str_replace("\n", "<br/>", $str);
		return $str;
	}

	function allowedHtml($msg, $encode=TRUE, $nofollow=TRUE)
	{
		$htmlAllowed = Array(
										"&lt;ol&gt;&lt;br/&gt;" => "<ol>",
										"&lt;ul&gt;&lt;br/&gt;" => "<ul>",
										"&lt;li&gt;&lt;br/&gt;" => "<li>",
										"&lt;/ol&gt;&lt;br/&gt;" => "</ol>",
										"&lt;/ul&gt;&lt;br/&gt;" => "</ul>",
										"&lt;/li&gt;&lt;br/&gt;" => "</li>",

										"&lt;u&gt;" => "<u>",
										"&lt;/u&gt;" => "</u>",
										"&lt;i&gt;" => "<i>",
										"&lt;/i&gt;" => "</i>",
										"&lt;b&gt;" => "<b>",
										"&lt;/b&gt;" => "</b>",
										"&lt;strong&gt;" => "<strong>",
										"&lt;/strong&gt;" => "</strong>",
										"&lt;p&gt;" => "",
										"&lt;/p&gt;" => "<br/>",
										"&lt;em&gt;" => "<em>",
										"&lt;/em&gt;" => "</em>",
										"&lt;ul&gt;" => "<ul>",
										"&lt;/ul&gt;" => "</ul>",
										"&lt;ol&gt;" => "<ol>",
										"&lt;/ol&gt;" => "</ol>",
										"&lt;li&gt;" => "<li>",
										"&lt;/li&gt;" => "</li>",
										"&lt;br/&gt;" => "<br/>",
										"&lt;br /&gt;" => "<br/>",
										"&lt;blockquote&gt;" => "<blockquote>",
										"&lt;/blockquote&gt;" => "</blockquote>",
										"&lt;small&gt;" => "<small>",
										"&lt;/small&gt;" => "</small>",

										"&amp;nbsp;" => "&nbsp;",
										"&amp;amp;" => "&amp;",
										"&amp;lt;" => "&lt;",

										"&lt;/span&gt;" => "</span>",
										"&lt;/a&gt;" => "</a>",

										"<br/><blockquote><br/>" => "<blockquote>",
										"<br/></blockquote><br/>" => "</blockquote>",
									);

		if($encode)
		{
			$msg = str_replace("<", "&lt;", $msg);
			$msg = str_replace(">", "&gt;", $msg);
		}
		foreach($htmlAllowed as $encoded => $decoded)
		{
			$msg = str_replace($encoded, $decoded, $msg);
		}
		$msg = ereg_replace("&lt;a (title=&quot;([-_&:;,#a-zA-Z0-9\\. ]*)&quot;)? ?href=&quot;(https?://[-+_a-zA-Z0-9@\\.,:/\?=&;%#]*)&quot;( target=&quot;[a-zA-Z0-9_]*&quot;)?&gt;", "<a title=\"\\2\" href=\"\\3\" target=\"_blank\"".($nofollow ? " rel=\"nofollow\"" : "").">", $msg);

		// Images
		$msg = ereg_replace("&lt;img class=&quot;([- :;#a-zA-Z0-9]*)&quot; ", "&lt;img ", $msg);
		$msg = ereg_replace("&lt;img[ \t]+title=&quot;(([-_a-zA-Z0-9;: \.]|(&[aeiou]acute;))*)&quot; ", "&lt;img ", $msg);
		$msg = ereg_replace("&lt;img (style=&quot;([-a-zA-Z0-9;: \.]*)&quot; )?src=&quot;(https?://[-+_a-zA-Z0-9@\\.,:/\?=&;%\\|]*)&quot;( alt=&quot;([-a-zA-Z0-9\\.,:/\?=;% ]*)&quot;)?( width=&quot;([0-9]*)&quot;)?( height=&quot;([0-9]*)&quot;)? /&gt;", "<img style=\"\\2\" src=\"\\3\" alt=\"\\5\" width=\"\\7\" height=\"\\9\" />", $msg);
		//$msg = ereg_replace("&lt;img( title=&quot;([-a-zA-Z0-9\\.,:/\?=;% ]*)&quot;)? (style=&quot;([-a-zA-Z0-9;: ]*)&quot; )?src=&quot;(https?://[-+_a-zA-Z0-9\\.,:/\?=&;%\\|]*)&quot;( alt=&quot;([-a-zA-Z0-9\\.,:/\?=;% ]*)&quot;)?( width=&quot;([0-9]*)&quot;)?( height=&quot;([0-9]*)&quot;)? /&gt;", "<img style=\"\\4\" src=\"\\5\" alt=\"\\7\" width=\"\\9\" height=\"\\11\" />", $msg);

		$msg = ereg_replace("&lt;span style=&quot;([- :;#a-zA-Z0-9\.]*)&quot;&gt;", "<span style=\"\\1\">", $msg);
		$msg = ereg_replace("&lt;p style=&quot;([- :;#a-zA-Z0-9\.]*)&quot;&gt;", "<p style=\"\\1\">", $msg);
		$msg = ereg_replace("&lt;p class=&quot;([- :;#a-zA-Z0-9]*)&quot;&gt;", "<p class=\"\\1\">", $msg);

		// Div elements not allowed
		$msg = ereg_replace("&lt;/?div([-_= :;#a-zA-Z0-9]|&quot;)*&gt;", "", $msg);
		return $msg;
	}

	function basicHtml($msg)
	{
		$htmlAllowed = Array(
										"<u>" => "",
										"</u>" => "",
										"<i>" => "",
										"</i>" => "",
										"<b>" => "",
										"</b>" => "",
										"<strong>" => "",
										"</strong>" => "",
										"<p>" => " <br/>",
										"</p>" => " <br/>",
										"<em>" => "",
										"</em>" => "",
										"<br/>" => " <br/>",
										"<br />" => "<br/>",
										"<ul>" => "",
										"</ul>" => " <br/>",
										"<ol>" => "",
										"</ol>" => " <br/>",
										"<li>" => " <br/>",
										"</li>" => "",
										"<blockquote>" => "",
										"</blockquote>" => "",
										"<small>" => "",
										"</small>" => "",

										//"&amp;nbsp;" => "&nbsp;",

										"</span>" => "",
										"</a>" => "",
										"\r\n" => " ",
										"\n" => " ",
										"  " => " ",
									);

		foreach($htmlAllowed as $encoded => $decoded)
		{
			$msg = str_replace($encoded, $decoded, $msg);
		}
		$msg = ereg_replace("<a title=\"[-_&:;,#a-zA-Z0-9@\\. \.]*\" href=\"(https?://[-+_a-zA-Z0-9@\\.,:/\?=&;%#]*)\" target=\"[a-zA-Z0-9_]*\"( rel=\"nofollow\")?>", "", $msg);
		$msg = ereg_replace("<img style=\"[-a-zA-Z0-9;: \.]*\" src=\"(https?://[-+_a-zA-Z0-9@\\.,:/\?=&;%\\|]*)\" alt=\"[-a-zA-Z0-9@\\.,:/\?=;% ]*\" width=\"[0-9]*\" height=\"[0-9]*\" />", "", $msg);
		$msg = ereg_replace("<span style=\"([- :;#a-zA-Z0-9\.]*)\">", "", $msg);
		$msg = ereg_replace("<p style=\"([- :;#a-zA-Z0-9\.]*)\">", "<br/>", $msg);
		$msg = ereg_replace("<p class=\"([- :;#a-zA-Z0-9]*)\">", "<br/>", $msg);
		return $msg;
	}
	
	function safeStripslashes($string)
	{
		if(get_magic_quotes_gpc())
		{
			return stripslashes($string);
		}
		else
		{
			return $string;
		}
	}

	function removeAcutes($s)
	{
		$vowels = Array("a", "e", "i", "o", "u");
		foreach($vowels as $vowel)
		{
			$s = ereg_replace("&".$vowel."acute;", $vowel, $s);
			$s = ereg_replace("&".$vowel."grave;", $vowel, $s);
			$s = ereg_replace("&".$vowel."uml;", $vowel, $s);

			$vowel = strtoupper($vowel);
			$s = ereg_replace("&".$vowel."acute;", $vowel, $s);
			$s = ereg_replace("&".$vowel."grave;", $vowel, $s);
			$s = ereg_replace("&".$vowel."uml;", $vowel, $s);
		}

		$s = str_replace("&ntilde;","n",$s);
		$s = str_replace("&Ntilde;","N",$s);

		$s = str_replace("&ccedil;","c",$s);
		$s = str_replace("&Ccedil;","C",$s);

		return $s;
	}
	
	function checkValidChars($str, $validRegExp="[[:alnum:]\-_]*")
	{
		if(!eregi("^($validRegExp)\$", $str, $regs))
		{
			return FALSE;
		}
		return $regs[1] == $str;
	}

	function getCancelMailSubscriptionLink($login, $mail)
	{
		// TODO
		$link = "http://meipi.org/actions/cancelMailSubscription.php?login=".urlencode($login)."&mail=".urlencode($mail)."&code=";
		$link .= md5($login."code".$mail);
		return $link;
	}

	function sendEmail($toAddress, $subject, $body, $fromAddress, $fromName, $toNickname, $toFullname)
	{
		if(isValidEmail($toAddress))
		{
			$cancelMailLink = getCancelMailSubscriptionLink($toNickname, $toAddress);

			//$subject = html_entity_decode(getString("pwd_recovery_subject"),ENT_QUOTES,"cp1252");
			//$body = html_entity_decode(getString("pwd_recovery_body_1"),ENT_QUOTES,"cp1252");
			$headers = "From: $fromName <$fromAddress>\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			$body = "<html><head><title>$subject</title></head><body>$body";
			$body .= "\r\n\r\n<br/><br/>";
			$body .= "You can modify your account at <a href='http://www.meipi.org/myprofile'>http://www.meipi.org/myprofile</a> or cancel your subscription by clicking on <a href=\"$cancelMailLink\">$cancelMailLink</a>";
			$body .= "</body></html>";

			$toFullname = removeAcutes($toFullname);
			$toNickname = removeAcutes($toNickname);
			//echo "nn: [$toNickname] ";
			//echo "fn: [$toFullname]";
			if(strlen($toFullname)>0 && checkValidChars($toFullname, "[[:alnum:]\-_ ]*"))
			{
				$toAddress = "$toFullname <$toAddress>";
			}
			else if(strlen($toNickname)>0 && checkValidChars($toNickname, "[[:alnum:]\-_ ]*"))
			{
				$toAddress = "$toNickname <$toAddress>";
			}
			$mailReplaced = str_replace('$cancelmailsubscription', $cancelMailLink, $mail);
			mail($toAddress, $subject, $body, $headers);
		}
	}

	function replaceBr($str)
	{
		$str = str_replace("<br/>", "\r\n", $str);
		return $str;
	}

	function getProfilePage($idUser, $login)
	{
		global $commonFiles;
		return $commonFiles."user.php?id_user=$idUser";
	}

?>
