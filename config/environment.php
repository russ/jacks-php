<?

// Getting Application Root

$application_root = explode("/", __FILE__);
array_pop($application_root);
array_pop($application_root);
$application_root = join("/", $application_root);

// Application Environment

$environment = $_ENV['APPLICATION_ENV'];
$environment = (is_null($environment)) ? "development" : $environment;

define("ENVIRONMENT", $environment);
define("APPLICATION_ROOT", $application_root);
define("USE_DATABASE_SESSIONS", 1);

require(APPLICATION_ROOT."/vendor/jacks/kimba.php");

?>
