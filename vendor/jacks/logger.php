<?

class Logger
{
	function log($message, $level = "INFO")
	{
		file_put_contents(APPLICATION_ROOT."/log/".ENVIRONMENT.".log", "{$level}: {$message}\n", FILE_APPEND);
	}
}

?>
