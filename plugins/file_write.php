<?php
	function measure_file_write($config) {
		$write_closure = function ($config, $link) {
			for ($idx = 0; $idx < $config['test_rounds']; $idx++) {
				$tempname = tempnam('.', 'tmp_measure_file_write');
				if (!$tempname) {
					log_message("error creating temp file");
					return null;
				}

				$temp = fopen($tempname, "w");
				if (!$temp) {
					log_message("error opening temp file");
					return null;
				}

				if (!fwrite($temp, "writing to tempfile")) {
					log_message("error writing to tempfile");
					return null;
				}

				fclose($temp);
				unlink($tempname);
			}

			return 1;
		};
		return time_task($write_closure, $config, $link);
	}
?>
