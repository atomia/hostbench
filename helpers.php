<?php
	function log_message($message) {
		$backtrace = debug_backtrace();
		$function = $backtrace[1]["function"];
		echo "; $function: $message\n";
	}

	function get_disabled_counters() {
		$backtrace = debug_backtrace();
		$function = $backtrace[1]["function"];

		if ($config['disabled_counters'] != null && is_array($config['disabled_counters'])) {
			$ret = $config['disabled_counters'][$function];
			if ($ret != null && is_array($ret)) {
				return $ret;
			}
		}

		return array();
	}

	function calculate_network_latency($ip) {
		if (function_exists("popen")) {
			$fh = popen("ping -i 0.2 -c 10 \"$ip\" | awk -F / '/10 received/ { ok = 1 } $1 == \"rtt min\" && ok { print \$5 }'", "r");
			if ($fh === false) {
				log_message("failed to popen ping");
			}

			$latency = trim(fgets($fh));
			if (!empty($latency)) {
				return $latency;
			} else {
				log_message("failed to determine db network latency to $ip using popen() and ping");
				return null;
			}
		} else {
			log_message("popen isn't available");
		}
	}

	function setup_schema($config, $add_rows) {
		$link = mysql_connect($config['db_host'], $config['db_user'], $config['db_pass']);
		if (!$link) {
			log_message("error connecting to db: " . mysql_error($link));
			return null;
		}

		if (!mysql_select_db($config['db_name'], $link)) {
			log_message("error selecting db: " . mysql_error($link));
			return null;
		}

		if ($dh = opendir("schema")) {
			while (($file = readdir($dh)) !== false) {
				if (preg_match('/\.sql$/', $file)) {
					$content = file_get_contents("schema/$file");
					if (empty($content)) {
						log_message("error getting sql file schema/$file");
						return null;
					}

					$statements = explode(";", $content);
					foreach ($statements as $statement) {
						$statement = trim($statement);
						if (empty($statement)) {
							continue;
						}

						if (!mysql_query($statement, $link)) {
							log_message("error executing $statement: " . mysql_error($link));
							return null;
						}
					}
                                }
                        }
                	closedir($dh);
                } else {
			return null;
		}

		if ($add_rows) {
			$res = mysql_query("SHOW TABLES", $link);
			if (!$res) {
				log_message("error executing SHOW TABLES: " . mysql_error($link));
				return null;
			}

			while ($row = mysql_fetch_array($res)) {
				$table = $row[0];

				$query = "INSERT INTO `$table` (keycol) VALUES ";

				$stop_at = $config['test_rounds'];
				for  ($idx = 0; $idx < $stop_at; $idx++) {
					$query .= "($idx)" . ($idx < $stop_at - 1 ? "," : ";");
				}

				if (!mysql_query($query, $link)) {
					log_message("error executing query to insert data set into $table: " . mysql_error($link));
				}
			}
		}

		if (!mysql_query("SET profiling = 1", $link)) {
			log_message("error enabling profiling, ignoring");
		}

		return $link;
        }

	function time_task ($task, $config, $link) {
		list($usec, $sec) = explode(" ", microtime());
		$start = ((float)$usec + (float)$sec);

		$ret = $task($config, $link);

		list($usec, $sec) = explode(" ", microtime());
		$end = ((float)$usec + (float)$sec);

		return $ret != null ? ($end - $start) * 1000 : null;
	}
?>
