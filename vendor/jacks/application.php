<?

class Application
{
	function run()
	{
		global $database_config, $routing;

		$application = new Application();

		$model = new Model();
		$model->__connect_to_database($database_config[ENVIRONMENT]);

		// Load Up Controller
		$routing->params($_SERVER['REQUEST_URI']);

		if ($routing->current->events['nested_controller_path']) {
			$controller_path = $routing->current->events['nested_controllers'];
		} else {
			$path = explode("/", $routing->current->events['controller']);
			array_pop($path);
			$controller_path = $path;
		}

		// Require base controller if there is one
		$base_controller = $controller_path[count($controller_path) - 1];
		$base_controller = APPLICATION_ROOT."/app/controllers/".join($controller_path, "/")."/{$base_controller}_controller.php";
		if (file_exists($base_controller)) require_once($base_controller);

		// Require needed controller
		require_once(APPLICATION_ROOT."/app/controllers/{$routing->current->events['nested_controller_path']}/{$routing->current->events['controller']}_controller.php");
		$controller_name = explode("/", $routing->current->events['controller']);
		$controller_name = array_pop($controller_name);
		$controller = ucwords($controller_name)."Controller";
		$controller = new $controller();

		// Set Controller Params
		$controller->params = $this->params();
		$controller->route = $routing->current;

		return $controller->render(join("/", $controller_path), $controller_name, $routing->current->events['action']);
	}

	function controller($uri)
	{
		global $routing;

		$params = $routing->params($_SERVER['REQUEST_URI']);

		if ( isset($params['controller']) && ( count( $c = explode('/', $params['controller'] ) ) > 1 ) ) {
  		$params['controller'] = $c[ count($c) - 1 ];
  		array_pop($c);
   		$params['nested_controller'] = implode('/', $c);
		}

		return $controller;
	}

	function params()
	{
		global $routing;

		$params = $routing->params($_SERVER['REQUEST_URI']);

 		$params = array_merge($params, $_GET, $_POST);
		// $GLOBALS['HTTP_RAW_POST_DATA'] used for observer ajax calls
 		// Note: HTTP_RAW_POST_DATA must set to on in php.ini
 		if ( isset($GLOBALS['HTTP_RAW_POST_DATA']) ) {
   		$params =  array_merge( $params, array( 'raw_post' => str_replace('&_=', '', $GLOBALS['HTTP_RAW_POST_DATA']) ) );
 		}
 		unset($params['_']);
 		return $params;
	}

}

?>
