<?php
	function measure_db_network_latency($config) {
		$ip = gethostbyname($config['db_host']);
		$latency = calculate_network_latency($ip);
		if ($latency != null) {
			return array('single_rtt' => $latency, 'rtt_for_rounds' => $latency * $config['test_rounds']);
		} else {
			return null;
		}
	}
?>
