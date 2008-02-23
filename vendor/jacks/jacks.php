<?

// Require the rest of the application files

require(APPLICATION_ROOT."/vendor/jacks/application.php");
require(APPLICATION_ROOT."/vendor/jacks/controller.php");
require(APPLICATION_ROOT."/vendor/jacks/file_upload.php");
require(APPLICATION_ROOT."/vendor/jacks/logger.php");
require(APPLICATION_ROOT."/vendor/jacks/mailer.php");
require(APPLICATION_ROOT."/vendor/jacks/model.php");
require(APPLICATION_ROOT."/vendor/jacks/routing.php");
require(APPLICATION_ROOT."/vendor/jacks/session.php");
require(APPLICATION_ROOT."/vendor/jacks/validation.php");
require(APPLICATION_ROOT."/config/database.php");
require(APPLICATION_ROOT."/config/routes.php");

// Built in Helpers

require(APPLICATION_ROOT."/vendor/jacks/helpers/application.php");
require(APPLICATION_ROOT."/vendor/jacks/helpers/form.php");
require(APPLICATION_ROOT."/vendor/jacks/helpers/html.php");
require(APPLICATION_ROOT."/vendor/jacks/helpers/inflector.php");
require(APPLICATION_ROOT."/vendor/jacks/helpers/pagination.php");

require(APPLICATION_ROOT."/app/controllers/application_controller.php");

include_files_in(APPLICATION_ROOT."/app/models");
include_files_in(APPLICATION_ROOT."/app/helpers");

// Require Environment File
require(APPLICATION_ROOT."/config/environments/".ENVIRONMENT.".php");

// Start the Session

if (USE_DATABASE_SESSIONS) {
	$session = new Session();
} else {
	session_start();
}

?>
