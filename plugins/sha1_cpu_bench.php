<?php
	function measure_sha1_cpu_bench($config) {
		$sha1_closure = function ($config, $link) {
			for ($idx = 0; $idx < $config['test_rounds'] * 1000; $idx++) {
				$void = sha1($idx);
			}

			return 1;
		};
		return time_task($sha1_closure, $config, $link);
	}
?>
