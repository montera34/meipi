<?
	/**
	 * Plugins support for meipi
	 */
	$_meipiPlugins = Array();

	function loadPlugin($pluginName)
	{
		global $configsPath;
		require_once($configsPath."plugins/".$pluginName."/".$pluginName.".php");
	}

	function addPlugin($event, $fn, $priority=10)
	{
		global $_meipiPlugins;

		$plugin = Array();
		$plugin["fn"] = $fn;

		$_meipiPlugins[$event][$priority][] = $plugin;
	}

	function executePlugins($event, $args)
	{
		global $_meipiPlugins;

		$eventPlugins = $_meipiPlugins[$event];
		if(!$eventPlugins)
			return $args;

		ksort($eventPlugins);
		foreach($eventPlugins as $priority => $priorityPlugins)
		{
			foreach($priorityPlugins as $plugin)
			{
				$args = call_user_func_array($plugin["fn"], Array($args));
			}
		}
		return $args;
	}

	function loadPlugins()
	{
		global $plugins;
		$aPlugins = split(",", $plugins);
		foreach($aPlugins as $pluginName)
		{
			if(strlen($pluginName)>0)
				loadPlugin($pluginName);
		}
	}

	loadPlugins();
?>
