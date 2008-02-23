<?  

function add_error_message_to($model, $field, $message)
{
	$model->errors[$field] = $message;
}

function validates_presence_of($model, $field, $options = array())
{
	if (is_null($model->fields[$field]) || $model->fields[$field] == "") {
		add_error_message_to($model, $field, ((!$options['custom_message']) ? humanize($field)." must be present." : $options['custom_message']));
	}
}

function validates_email($model, $field, $options = array())
{
	if (preg_match("/\A[^@\s]+@[\w-]+(\.[\w-]+)*?(\.\w\w+)\z/", $model->fields[$field]) == 0) {
		add_error_message_to($model, $field, ((!$options['custom_message']) ? humanize($field)." must be a email address." : $options['custom_message']));
	}
}

function validates_uniqueness_of($model, $field, $options = array())
{
	$model_name = get_class($model);
	$m = new $model_name();
	$m->find_first(array( 'conditions' => "{$field} = '{$model->fields[$field]}' AND {$model->primary_key} <> '".addslashes($model->fields[$model->primary_key])."'" ));
	if ($m->row_count() > 0) add_error_message_to($model, $field, ((!$options['custom_message']) ? humanize($field)." must be unique." : $options['custom_message']));
}

function validates_confirmation_of($model, $field, $options = array())
{
	$params = array_merge($_GET, $_POST);
	$model_name = strtolower(get_class($model));
	if (isset($params[$model_name][$field])) {
		if ($params[$model_name]["confirm_{$field}"] != $model->fields[$field]) add_error_message_to($model, $field, ((!$options['custom_message']) ? pluralize(humanize($field))." do not match." : $options['custom_message']));
		unset($model->fields["confirm_{$field}"]);
	}
}

?>
