<?php
	require("config.php");
	require("helpers.php");

	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', true);
	set_time_limit(600);

	header("Content-Type: text/plain");

	/*
	 * The plugin interface works like this:
	 * 1. You place the plugin in the plugins folder named your_thing.php
	 * 2. You implement your test in the function measure_your_thing($config)
	 * 3. If you determine that the test is not available on the running PHP version, platform
	 *    etc, then you return null
	 * 4. Otherwise you return the elapsed time for the test in ms.
	 * 5. In addition if you want to return several counters, then return
	      array('something' => value, 'somethingelse' => value), still in ms.
	 * 6. If you want to log something informationally, then just call log_message("your message")
	 *
	 * See helpers.php for some useful helper functions.
	 */
	if ($dh = opendir("plugins")) {
		while (($plugin = readdir($dh)) !== false) {
			if (preg_match('/^([a-z0-9_-]+)\.php$/', $plugin, $regs)) {
				require_once("plugins/$plugin");
				$plugin_function = "measure_" . $regs[1];
				if (function_exists($plugin_function)) {
					$result = $plugin_function($config);
					if ($result != null) {
						if (!is_array($result)) {
							echo $regs[1] . " $result\n";
						} else {
							while (list($key, $val) = each($result)) {
								if ($val != null && $key != null) {
									echo $regs[1] . "_$key $val\n";
								}
							}
						}
					}
				}
			}
		}
		closedir($dh);
	}
?>
