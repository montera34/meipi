<?
		if($type=="map")
		{
			$selectionBar .= "<form style=\"float: right; border-left: 1px solid #aacde0; padding-left: 5px;\" name=\"address\" onsubmit=\"mapGoTo();return false;\">";
			//$selectionBar .= "<div class=\"avanzar\">";
			//$selectionBar .= "<input class=\"caja\" type=\"text\" name=\"address\" onKeyPress=\"KeyPress(event)\" />";
			$selectionBar .= "<span class=\"corrector\"><input class=\"caja-list\" type=\"text\" name=\"address\" /></span>";
			$selectionBar .= "<span class=\"corrector\"> <input class=\"boton-list\" type=\"button\" name=\"go\" onclick=\"mapGoTo()\" value=\"".getString("Go")."\" /></span>";
			//$selectionBar .= "</div>";
			$selectionBar .= "</form>";

			$selectionBar .= "<form style=\"padding-right: 170px;\" name=\"user\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"get\">";
			//$selectionBar .= "<div class=\"avanzar\">";
			$selectionBar .= "<span class=\"corrector\"> ".getString("Login").": <input class=\"caja-list\"type=\"text\" name=\"user\" value=\"".$user."\" /></span>";
			$selectionBar .= "<span class=\"corrector\"> ".getString("Tag").": <input class=\"caja-list\" type=\"text\" name=\"tag\" value=\"".$tag."\" /></span>";
			$selectionBar .= "<span class=\"corrector\"> <input class=\"boton-list\" type=\"submit\" value=\"".getString("Search")."\" /></span>";
			//$selectionBar .= "</div>";
			$selectionBar .= "</form>";
		}
		else // list, mosaic, categories
		{
			$selectionBar .= "<form name=\"user\" action=\"".setParams("list.php", null)."\" method=\"get\">";
			$selectionBar .= "<input type=\"hidden\" name=\"id_meipi\" value=\"$idMeipi\" />";
			$selectionBar .= "<span class=\"corrector\"> ".getString("Login").": <input class=\"caja-list\"type=\"text\" name=\"user\" value=\"".$user."\" /></span>";
			$selectionBar .= "<span class=\"corrector\"> ".getString("Tag").": <input class=\"caja-list\" type=\"text\" name=\"tag\" value=\"".$tag."\" /></span>";
			$selectionBar .= "<span class=\"corrector\"> ".getString("Order").": <select name=\"order\">";
			$selectionBar .= "<option value=\"date_desc\"".($request["order"]=="date_desc" ? "selected" : "").">".getString("Date")."</option>";
			$selectionBar .= "<option value=\"date\"".($request["order"]=="date" ? "selected" : "").">".getString("Reverse date")."</option>";
			$selectionBar .= "<option value=\"rank_desc\"".($request["order"]=="rank_desc" ? "selected" : "").">".getString("Ranking")."</option>";
			$selectionBar .= "<option value=\"rank\"".($request["order"]=="rank" ? "selected" : "").">".getString("Reverse ranking")."</option>";
			$selectionBar .= "<option value=\"title\"".($request["order"]=="title" ? "selected" : "").">".getString("Title")."</option>";
			$selectionBar .= "<option value=\"title_desc\"".($request["order"]=="title_desc" ? "selected" : "").">".getString("Reverse title")."</option>";
			$selectionBar .= "</select></span>";
			$selectionBar .= "<span class=\"corrector\"> <input class=\"boton-list\" type=\"submit\" value=\"".getString("Search")."\" /></span>";
			$selectionBar .= "</form>";
		}

?>
