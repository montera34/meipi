<?
	function dbConnect()
	{
		global $server, $usr, $pass, $db;

		$dbLink = $_REQUEST["dbLink"];
		if(isset($dbLink) && $dbLink!=null)
			return $dbLink;

		$dbLink = mysql_connect($server, $usr, $pass);
		mysql_select_db($db, $dbLink);
		$_REQUEST["dbLink"] = $dbLink;
		return $dbLink;
	}

	function endRequest()
	{
		/*
		// If used too early, when db connection is still needed, time spent is longer

		$dbLink = $_REQUEST["dbLink"];
		if(isset($dbLink) && $dbLink!=null)
		{
			dbDisconnect($dbLink);
			unset($_REQUEST["dbLink"]);
		}
		*/
		return;
	}
	
	function dbDisconnect($dbLink)
	{
		mysql_close($dbLink);
	}
	
	function dbSelect($sQuery, $dbLink=null)
	{
		if($dbLink==null)
		{
			$dbLink = dbConnect();
		}
		
		$result = mysql_query($sQuery, $dbLink);
		$rows = 0;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$aResult[$rows] = $row;
			$rows++;
		}
		return $aResult;
	}

	function dbGetRows($sQuery, $dbLink=null)
	{
		if($dbLink==null)
		{
			$dbLink = dbConnect();
		}
		$result = mysql_query($sQuery, $dbLink);
		$iRows = mysql_num_rows($result);
		return $iRows;
	}
	
	function dbUpdate($sQuery, $dbLink)
	{
		return mysql_query($sQuery, $dbLink);
	}

	function dbGetSelectedRows($aResult)
	{
		if(isset($aResult["selectedRows"]))
		{
			return $aResult["selectedRows"];	
		}
		else
		{
			$iRows = count($aResult);
			if(isset($aResult["rows"]))
				$iRows = $iRows-1;
			return $iRows;
		}
	}

	function dbGetTotalRows($aResult)
	{
		if(isset($aResult["rows"]))
		{
			return $aResult["rows"];	
		}
		else
		{
			$iRows = count($aResult);
			if(isset($aResult["selectedRows"]))
				$iRows = $iRows-1;
			return $iRows;
		}
	}

?>
