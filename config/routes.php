<?

$routing = new Routing();
$mapper = new Mapper();

$mapper->connect(':controller/:action/:id', array( 'controller' => "pages", 'action' => "index", 'name' => "default" ));

?>
