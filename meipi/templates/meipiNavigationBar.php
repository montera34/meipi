<?
		$navigationBar .= "<div id=\"cabecera\">";
		$navigationBar .= "<div id=\"barra\">";
		$navigationBar .= "<div id=\"secciones\">";
		if(strlen($webTitle)>0)
		{
		  $navigationBar .= "<img class=\"proyecto-logo\" src=\"".$commonFiles."images/header-proyecto.gif\" alt=\"$webTitle\" />";
		  $navigationBar .= "<a class=\"proyecto\" href=\"".setParams("meipi.php", null)."\"><span class=\"meipi-home\">$webTitle</span></a>";
		}
		//$navigationBar .= "<span class=\"logo\"><img alt=\"\" src=\"".$commonFiles."images/logo2.gif\" /></span>";
		$navigationBar .= "<ul><li><a alt=\"".getString("MAP")."\" class=\"li-mapo\" title=\"".getString("MAP")."\" href=\"".setParams("map.php", null)."\">".getString("MAP")."</a></li>";
		$navigationBar .= "<li><a alt=\"".getString("LIST")."\" class=\"li-lista\" title=\"".getString("LIST")."\" href=\"".setParams("list.php", null)."\">".getString("LIST")."</a></li>";
		$navigationBar .= "<li><a alt=\"".getString("CATEGORIES")."\" class=\"li-canal\" title=\"".getString("CATEGORIES")."\" href=\"".setParams("categories.php", null)."\">".getString("CATEGORIES")."</a></li>";
		$navigationBar .= "<li><a alt=\"".getString("MOSAIC")."\" class=\"li-mosac\" title=\"".getString("MOSAIC")."\" href=\"".setParams("mosaic.php", null)."\">".getString("MOSAIC")."</a></li></ul>";
		$navigationBar .= "</div>";
		$navigationBar .= "<div id=\"buscador\">";
		if($type=="map")
		{
			$navigationBar .= "<form method=\"get\" action=\"".setParams("map.php", null)."\">";
		}
		else
		{
			$navigationBar .= "<form method=\"get\" action=\"".setParams("list.php", null)."\">";
		}
		$navigationBar .= "<input type=\"hidden\" name=\"id_meipi\" value=\"$idMeipi\" />";
		$navigationBar .= "<input class=\"caja-list\" type=\"text\" name=\"search\" value=\"".$request["search"]."\" />";
		$navigationBar .= "<input class=\"boton-list\" type=\"submit\" Value=\"".getString("Search")."\" />";
		//$navigationBar .= "<input class=\"boton\" type=\"button\" Value=\"V\" onClick=\"javascript:switchAdvancedSearchMap();\" />";
		//$navigationBar .= "<a href=\"javascript:switchAdvancedSearchMap();\"><img alt=\"b&uacute;squeda avanzada\" src=\"".$commonFiles."images/header-avanzada1.gif\" /></a>";
		$navigationBar .= "</form>";
		$navigationBar .= "</div>";	
		$navigationBar .= "<div id=\"accion\"><ul>";
		//$navigationBar .= "<a href=\"javascript:showHelpInfo();\">".getString("Help")."</a> ";
		if(isLogged())
		{
			$nextPage = $_SERVER["REQUEST_URI"];
			$navigationBar .= "<li><a title=\" ".getString("Log out")." ".getString("from")." ".getLogin()." \" href=\"".setParam($commonFiles."actions/logout.php", "next", $nextPage)."\">";
			//$navigationBar .= "<img alt=\"\" src=\"".$commonFiles."images/header-sesion-fin.gif\" />";
			$navigationBar .= getString("Log out");
			$navigationBar .= "</a></li> ";

			$navigationBar .= '<li><a href="'.$commonFiles.'myprofile.php">'.getString("My profile").'</a></li>';

			// Unread messages count
			$aMessages = getMessages(FALSE);
			$iMessages = count($aMessages);
			if($iMessages>0)
			{
				$navigationBar .= '<li><a href="'.getProfilePage(getIdUser(), getLogin()).'">'.$iMessages.' '.getString("message".($iMessages>1 ? "s" : "")).'</a></li>';
			}

			$navigationBar .= "<li><a alt=\"".getString("NEW ENTRY")."\" title=\"".getString("NEW ENTRY")."\" href=\"javascript:showNewEntryForm();\">";
			//$navigationBar .= "<img alt=\"\" src=\"".$commonFiles."images/header-entrada.gif\" />";
			$navigationBar .= getString("NEW ENTRY");
			$navigationBar .= "</a></li>";
			/*if(strlen($idMeipi)>0)
			{
				// Edit meipi page not working, link removed temporarily
				if(canEditMeipimatic($idMeipi))
				{
					$navigationBar .= "<li><a title=\"".getString("Edit meipi")."\" href=\"".setParam("editmeipi.php", "id_meipi", $idMeipi)."\">"; 
					//$navigationBar .= "<img alt=\"\" src=\"".$commonFiles."images/header-editar.gif\" />";
					$navigationBar .= getString("Edit meipi");
					$navigationBar .= "</a></li>";
				}
			}*/
		}
		else
		{
			$navigationBar .= "<li><a title=\"".getString("Log in")."\" href=\"javascript:showLoginForm();\">";
			//$navigationBar .= "<img alt=\"\" src=\"".$commonFiles."images/header-sesion.gif\" />";
			$navigationBar .= getString("Log in");
			$navigationBar .= "</a></li> ";
			$navigationBar .= "<li><a title=\"".getString("NEW ENTRY")."\" href=\"javascript:showLoginFormParams('showEntryForm=true');\">";
			//$navigationBar .= "<img alt=\"\" src=\"".$commonFiles."images/header-entrada.gif\" />";
			$navigationBar .= getString("NEW ENTRY");
			$navigationBar .= "</a></li>";
		}
		//get string: HELP
		//$navigationBar .= "<li><a title=\"Ayuda\" href=\"javascript:showHelpInfo();\"><img alt=\"\" src=\"".$commonFiles."images/header-ayuda.gif\" /></a></li>";
		$navigationBar .= "<!--<li><a title=\"Ayuda\" href=\"javascript:showHelpInfo();\">".getString("Help")."</a></li>-->";
		$navigationBar .= "<li><a href=\"javascript:switchAdvancedSearchMap();\">".getString("Advanced search")."</a></li>";
		$navigationBar .= "</ul></div>";

		$navigationBar .= "</div>";
		$navigationBar .= "<div id=\"avanzamarco\" class=\"paginas\" style=\"display: none;\">";
		$navigationBar .= "<div id=\"avanza\">";
		$navigationBar .= "<ul><li><strong>".getString("Advanced search")."</strong> ".getString("in meipi")." ".$idMeipi.":</li><li>".getString("To search")." <strong>".getString("entries by author")."</strong> ".getString("use field")." <strong>".getString("User")."</strong>.</li><li>".getString("To search")." <strong>".getString("entries by tag")."</strong> ".getString("use field")." <strong>".getString("Tag")."</strong>.</li></ul>";
		$navigationBar .= getSelectionBar($request, $type);
		$navigationBar .= "</div>";
		$navigationBar .= "</div>";
		$navigationBar .= "</div>";

?>
