<?
	global $lang, $languagePath, $meipiLangs;
	
	// 1) Use the one in _REQUEST, if set.
	if(strlen($_REQUEST["lang"])>0 && file_exists($languagePath."lang/strings_".$_REQUEST["lang"].".php"))
	{
		$lang = $_REQUEST["lang"];
		$_SESSION["lang"] = $_REQUEST["lang"];
	}
	// 2) Use the one in _SESSION, if set.
	else if(strlen($_SESSION["lang"])>0 && file_exists($languagePath."lang/strings_".$_SESSION["lang"].".php"))
	{
		$lang = $_SESSION["lang"];
	}
	// 3) Use the $lang variable, set in certain meipis.
	else if(file_exists($languagePath."lang/strings_".$lang.".php"))
	{
	}
	// 4) Use the browser variable.
	else if (getenv("HTTP_ACCEPT_LANGUAGE") != '')
	{ 
		$envLang = getenv("HTTP_ACCEPT_LANGUAGE");
		$aLangs = explode(",", $envLang);
		for ($i=0; $i<count($aLangs); $i++)
		{
			if (!isset($lang))
			{
				for ($j=0; $j<count($meipiLangs); $j++)
				{
					if ((substr($aLangs[$i], 0, 2) == $meipiLangs[$j]) && file_exists($languagePath."lang/strings_".$meipiLangs[$j].".php"))
					{
						$lang = $meipiLangs[$j];
						break;
					}
				}
			}
		}
		if (!isset($lang))
		{
			$lang = $meipiLangs[0];
		}
	}
	// 5) Use default language.
	else 
	{
		$lang = $meipiLangs[0];
	}

	require_once($languagePath."lang/strings_".$lang.".php");

	// returns the string in the set language.
	function getString($id)
	{
		global $strings;

		$text = $strings[$id];
		if(strlen($text)>0)
			return $text;
		return $id;
	}
?>
