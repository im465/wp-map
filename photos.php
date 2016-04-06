<?php

class Photos {

	function dispatch() {
		$method = "method_{$_GET['method']}";
		if (! empty($_GET['method']) &&
		      method_exists($this, $method)) {
			$this->$method($_GET, array($this, 'respond'));
		} else {
			$methods = get_class_methods('Photos');
			$methods = array_filter($methods, function($method) {
				return substr($method, 0, 7) == 'method_';
			});
			$methods = array_map(function($method) {
				return substr($method, 7);
			}, $methods);
			$methods = array_values($methods);
			$this->respond(array(
				'ok' => 0,
				'help' => "Please specify a 'method' argument.",
				'methods' => $methods
			));
		}
	}

	function respond($rsp) {
		header('Content-Type: application/json');
		echo json_encode($rsp);
		exit;
	}

	function method_index_photos($args, $callback) {
		$dir = __DIR__ . '/photos';
		$subdir = '';
		if (! empty($args['dir'])) {
			$subdir = '/' . str_replace('..', '', $args['dir']);
			$dir .= $subdir;
		}

		if (! file_exists($dir)) {
			$this->respond(array(
				'help' => 'Not found.'
			));
		}

		$index = array();
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if (substr($file, 0, 1) == '.') {
				continue;
			}
			if (preg_match('/\.jpe?g$/i', $file)) {
				$exif = exif_read_data("$dir/$file", 'IFD0', false);
				$timestamp = strtotime($exif['DateTime']);
				if (! empty($args['exif_offset'])) {
					$timestamp += intval($args['exif_offset']);
				}
			} else {
				$timestamp = filemtime("$dir/$file");
			}
			if (is_dir($file)) {
				$type = 'dir';
			} else {
				$type = 'file';
			}
			$index[] = array(
				'path' => "photos$subdir/$file",
				'type' => $type,
				'timestamp' => $timestamp,
				'date' => date('Y-m-d H:i:s', $timestamp)
			);
		}

		call_user_func($callback, array(
			'ok' => 1,
			'help' => "Use the 'dir' argument to traverse the directory tree.",
			'index_photos' => $index
		));
	}

	function method_map_photos($args, $callback) {
		if (empty($args['gpx'])) {
			call_user_func($callback, array(
				'ok' => 0,
				'help' => "Please specify a 'gpx' argument."
			));
		}

		$path = str_replace('..', '', $args['gpx']);
		if (! file_exists($path)) {
			call_user_func($callback, array(
				'ok' => 0,
				'help' => "Could not find '$path'."
			));
		}

		$trace = array();
		$gpx = simplexml_load_file($path);
		foreach ($gpx->trk->trkseg->trkpt as $point) {
			$timestamp = (int) strtotime($point->time);
			$trace[$timestamp] = array(
				'lat' => (float) $point['lat'],
				'lng' => (float) $point['lon']
			);
		}

		$index_args = array(
			'dir' => 'ramsay'
		);
		$this->method_index_photos($index_args, function($rsp) use ($trace, $callback) {
			$photos = $rsp['index_photos'];
			usort($photos, function($a, $b) {
				if ($a['timestamp'] > $b['timestamp']) {
					return 1;
				} else {
					return -1;
				}
			});
			call_user_func($callback, array(
				'ok' => 1,
				//'trace' => $trace,
				'photos' => $photos
			));
		});
	}

}

date_default_timezone_set('America/New_York');
$photos = new Photos();
$photos->dispatch();

?>
