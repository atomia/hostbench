<?php
	function measure_mysql_update($config) {
		$link = setup_schema($config, true);
		if (!$link) {
			log_message("error setting up schema");
			return null;
		}

		$update_closure = function ($config, $link, $table) {
			return function ($config, $link) use ($table) {
				if (!mysql_query("UPDATE $table SET keycol = $keycol + 1", $link)) {
					log_message("error updating $table: " . mysql_error($link));
					return null;
				} else {
					return 1;
				}
			};
		};

		$innodb_indexed = time_task($update_closure($config, $link, "perftest_indexed_innodb"), $config, $link);
		$myisam_indexed = time_task($update_closure($config, $link, "perftest_indexed_myisam"), $config, $link);
		$innodb = time_task($update_closure($config, $link, "perftest_non_indexed_innodb"), $config, $link);
		$myisam = time_task($update_closure($config, $link, "perftest_non_indexed_myisam"), $config, $link);

		return array('innodb_indexed' => $innodb_indexed, 'myisam_indexed' => $myisam_indexed, 'innodb_non_indexed' => $innodb, 'myisam_non_indexed' => $myisam);
	}
?>
