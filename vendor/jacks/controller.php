<?

class Controller
{
	var $params;
	var $route;

	function render($path, $controller, $action)
	{
		$this->before_filter();

		ob_start();
		$this->$action();
		require(APPLICATION_ROOT."/app/views/{$path}/{$controller}/{$action}.php");
		$view = ob_get_contents();
		ob_end_clean();

		if (isset($this->layout) && $this->layout != false) {
			ob_start();
			require(APPLICATION_ROOT."/app/views/layouts/{$this->layout}.php");
			$layout = ob_get_contents();
			ob_end_clean();
		}

		if (isset($this->layout) && $this->layout != false) {
			$this->content_for_layout = str_replace("@@content_for_layout@@", $view, $layout);
		} else {
			$this->content_for_layout = $view;
		}

		$this->after_filter();

		return $this->content_for_layout;
	}

	function is_posted()
	{
		return ($_SERVER['REQUEST_METHOD'] == 'POST');
	}

	function render_partial($partial, $locals = null)
	{
		extract($locals);
		if (strstr($partial, "/")) {
			$path = explode("/", $partial);
			$partial = array_pop($path);
			include(APPLICATION_ROOT."/app/views/".join($path, "/")."/_{$partial}.php");
		} else {
			include(APPLICATION_ROOT."/app/views/{$this->route->events['nested_controller_path']}/".$this->controller_name()."/_{$partial}.php");
		}
	}

	function render_collection($partial, $collection)
	{
		while ($collection->next()) $this->render_partial($partial, array( "record" => $collection ));
	}

	function controller_name()
	{
		return str_replace("controller", "", strtolower(get_class($this)));
	}

	function authenticate_or_request_with_http_basic($username, $password)
	{
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header('WWW-Authenticate: Basic realm="My Realm"');
			header('HTTP/1.0 401 Unauthorized');
			return false;
		}
		if ($username != $_SERVER['PHP_AUTH_USER'] || $password != $_SERVER['PHP_AUTH_PW']) return false;
		return true;
	}

	// Callback Stubs
	
	function before_filter() { }
	function after_filter() { }
}

?>
