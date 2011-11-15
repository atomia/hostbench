<?php
	function measure_file_read($config) {
		$read_closure = function ($config, $link) {
			for ($idx = 0; $idx < $config['test_rounds']; $idx++) {
				$buf = file_get_contents(__FILE__);
				if ($buf == null || strlen($buf) <= 0) {
					log_message("error reading file");
					return null;
				}
			}

			return 1;
		};
		return time_task($read_closure, $config, $link);
	}
?>
