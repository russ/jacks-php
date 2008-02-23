<?

function include_files_in($directory)
{
	$models = dir($directory);
	while (false !== ($file = $models->read())) { 
		if ($file != "." && $file != ".." && preg_match("/\.php$/", $file)) {
			require_once("{$directory}/{$file}");
		}
	}
}

function redirect_to($url)
{
	header("Location: {$url}");
	die;
}

function flash($message = null, $type = "notice")
{
	if ($message || $_SESSION['flash']['message']) {
		if ($message) {
			$_SESSION['flash']['message'] = $message;
			$_SESSION['flash']['type'] = $type;
			$_SESSION['flash']['checked'] = 'no';
		} elseif ($_SESSION['flash']['checked'] == 'no') {
			$_SESSION['flash']['checked'] = 'yes';	
			return true;
		} else {
			$message = $_SESSION['flash']['message'];
			unset($_SESSION['flash']);
			return $message;
		}
	} else {
		return false;
	}
}

function mkdirp($path, $mode = 0777)
{
	$dirs = explode('/', $path);
	foreach ($dirs as $dir) {
		$p .= '/'.$dir;
		@mkdir($p, $mode);
	}
	if (!is_dir($p) && is_writable($p)) return false;
	return true;
}

function format_date($time, $format = "%m/%d/%y")
{
	$time = strtotime($time);
	return strftime($format, $time);
}

function datetime($timestamp = null)
{
	if (is_null($timestamp)) $timestamp = time();
	return strftime("%Y-%m-%d %T", $timestamp);
}

function timestamp_from_array($array, $format = "%Y-%m-%d %T")
{
	$array['hour'] = ($array['ampm'] == 'pm') ? ($array['hour'] + 12) : $array['hour'];
	return strftime($format, mktime($array['hour'], $array['minute'], 0, $array['month'], $array['day'], $array['year']));
}

function print_obj($obj = null, $die = false)
{
	if (!$obj) {
		echo "nothing to print";
		die;
	}
	echo '<pre>';
	print_r($obj);
	if ($die) {
		die;
	}
	echo '</pre>';
}

?>
