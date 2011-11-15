<?php
	function measure_nfs_network_latency($config) {
		$command = 'mount | grep `mount | grep -v debugfs | cut -d " " -f 3 | while read fs; do stat -c "%d %n" "$fs" | grep "^$(stat -c %d .) "; done | cut -d " " -f 2`" type nfs" | cut -d : -f 1';
		if (function_exists("popen")) {
			$fh = popen($command, "r");
			if ($fh === false) {
				log_message("failed to popen command for determining nfs ip");
			}

			$ip = fgets($fh);
			if (!empty($ip)) {
				$latency = calculate_network_latency($ip);
				if ($latency != null) {
					return array('single_rtt' => $latency, 'rtt_for_rounds' => $latency * $config['test_rounds']);
				} else {
					return null;
				}
			} else {
				return null;
			}
		} else {
			log_message("popen isn't available");
		}
	}
?>
