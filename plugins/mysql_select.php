<?php
	function measure_mysql_select($config) {
		$link = setup_schema($config, true);
		if (!$link) {
			log_message("error setting up schema");
			return null;
		}

		$select_closure = function ($config, $link, $table) {
			return function ($config, $link) use ($table) {
				for  ($idx = 0; $idx < $config['test_rounds']; $idx++) {
					if (!mysql_query("SELECT keycol FROM $table WHERE keycol = $idx", $link)) {
						log_message("error querying $table: " . mysql_error($link));
						return null;
					}
				}
				return 1;
			};
		};

		$innodb_indexed = time_task($select_closure($config, $link, "perftest_indexed_innodb"), $config, $link);
		$myisam_indexed = time_task($select_closure($config, $link, "perftest_indexed_myisam"), $config, $link);
		$innodb = time_task($select_closure($config, $link, "perftest_non_indexed_innodb"), $config, $link);
		$myisam = time_task($select_closure($config, $link, "perftest_non_indexed_myisam"), $config, $link);

		return array('innodb_indexed' => $innodb_indexed, 'myisam_indexed' => $myisam_indexed, 'innodb_non_indexed' => $innodb, 'myisam_non_indexed' => $myisam);
	}
?>
