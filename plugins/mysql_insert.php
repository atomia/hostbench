<?php
	function measure_mysql_insert($config) {
		$link = setup_schema($config, false);
		if (!$link) {
			log_message("error setting up schema");
			return null;
		}

		$disabled_counters = get_disabled_counters();

		$insert_closure_stupid = function ($config, $link, $table) {
			return function ($config, $link) use ($table) {
				for  ($idx = 0; $idx < $config['test_rounds']; $idx++) {
					if (!mysql_query("INSERT INTO $table (keycol) VALUES ($idx)", $link)) {
						log_message("error inserting row into $table: " . mysql_error($link));
						return null;
					}
				}
				return 1;
			};
		};

		$insert_closure = function ($config, $link, $table) {
			return function ($config, $link) use ($table) {
				$stop_at = $config['test_rounds'];
				$query = "INSERT INTO $table (keycol) VALUES ";

				for  ($idx = 0; $idx < $stop_at; $idx++) {
					$query .= "($idx)" . ($idx < $stop_at - 1 ? "," : ";");
				}

				if (!mysql_query($query, $link)) {
					log_message("error inserting rows into $table: " . mysql_error($link));
					return null;
				} else {
					return 1;
				}
			};
		};

		$innodb_indexed_stupid = $disabled_counters['innodb_indexed_stupid'] != null ? time_task($insert_closure_stupid($config, $link, "perftest_indexed_innodb"), $config, $link) : null;
		$myisam_indexed_stupid = $disabled_counters['myisam_indexed_stupid'] != null ? time_task($insert_closure_stupid($config, $link, "perftest_indexed_myisam"), $config, $link) : null;
		$innodb_stupid = $disabled_counters['innodb_non_indexed_stupid'] != null ? time_task($insert_closure_stupid($config, $link, "perftest_non_indexed_innodb"), $config, $link) : null;
		$myisam_stupid = $disabled_counters['myisam_non_indexed_stupid'] != null ? time_task($insert_closure_stupid($config, $link, "perftest_non_indexed_myisam"), $config, $link) : null;

		$innodb_indexed = $disabled_counters['innodb_indexed'] != null ? time_task($insert_closure($config, $link, "perftest_indexed_innodb"), $config, $link) : null;
		$myisam_indexed = $disabled_counters['myisam_indexed'] != null ? time_task($insert_closure($config, $link, "perftest_indexed_myisam"), $config, $link) : null;
		$innodb = $disabled_counters['innodb_non_indexed'] != null ? time_task($insert_closure($config, $link, "perftest_non_indexed_innodb"), $config, $link) : null;
		$myisam = $disabled_counters['myisam_non_indexed'] != null ? time_task($insert_closure($config, $link, "perftest_non_indexed_myisam"), $config, $link) : null;

		return array(
			'innodb_indexed_stupid' => $innodb_indexed_stupid, 'myisam_indexed_stupid' => $myisam_indexed_stupid, 'innodb_non_indexed_stupid' => $innodb_stupid, 'myisam_non_indexed_stupid' => $myisam_stupid,
			'innodb_indexed' => $innodb_indexed, 'myisam_indexed' => $myisam_indexed, 'innodb_non_indexed' => $innodb, 'myisam_non_indexed' => $myisam,
		);
	}
?>
