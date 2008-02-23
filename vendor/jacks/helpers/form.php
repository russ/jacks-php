<?

function form_start($action = "", $method ="post", $enc_type = null)
{
	if ($enc_type) {
		$enc = " enctype=\"{$enc_type}\"";
	}
	$content[] = "<form action=\"{$action}\" method=\"{$method}\"$enc>";
	return join($content, "\n");
}

function form_end()
{
	$content[] = "</form>";
	return join($content, "\n");
}

function label($model, $field, $options = array())
{
	$options['for'] = $model->name()."_{$field}";
	return content_tag("label", humanize($field), $options);
}

function hidden_field($model, $field, $options = array())
{
	$options['type'] = "hidden";
	$options['name'] = $model->name()."[{$field}]";
	$options['value'] = $model->fields[$field];
	return content_tag("input", null, $options);
}

function text_field($model, $field, $options = array())
{
	$options['type'] = "text";
	if ($model) {
		$options['name'] = $model->name()."[{$field}]";
		$options['value'] = $model->fields[$field];
	} else {
		$options['name'] = $field;
	}
	return content_tag("input", null, $options);
}

function file_field($model, $field, $options = array())
{
	$options['type'] = "file";
	$options['name'] = $model->name()."[{$field}]";
	$options['value'] = "";
	return content_tag("input", null, $options);
}

function checkbox($model, $field, $checked = null, $options = array())
{
	$options['type'] = "checkbox";
	$options['name'] = $model->name()."[{$field}]";
	if ($checked === true) {
		$options['checked'] = '"checked"';
	}
	$options['value'] = $model->fields[$field];
	return content_tag("input", null, $options);
}

function password_field($model, $field, $options = array())
{
	$options['type'] = "password";
	$options['name'] = $model->name()."[{$field}]";
	$options['value'] = '';
	return content_tag("input", null, $options);
}

function text_area($model, $field, $options = array())
{
	$options['name'] = $model->name()."[{$field}]";
	return content_tag("textarea", (($model->fields[$field]) ? $model->fields[$field] : " "), $options);
}

function submit($button_text = "Submit", $options = array())
{
	$options['type'] = "submit";
	$options['value'] = $button_text;
	return content_tag("input", null, $options);
}

function select($model, $field, $selected = '', $choices = array(), $options = array())
{
	if (!isset($options['name'])) $options['name'] = $model->name()."[{$field}]";

	foreach ($options as $k => $v) $ops .= "{$k}=\"{$v}\"";
	$tags[] = "<select {$ops}>";
	foreach ($choices as $k => $v) {
		$choice_options = array();
		$choice_options['value'] = $k;
		if ($v == $selected) $choice_options['selected'] = "selected";
		$tags[] = content_tag("option", $v, $choice_options);
	}
	$tags[] = "</select>";

	return join($tags, "\n");
}

function date_select($model, $field, $date = null)
{
	switch (true)
	{
		case (!$date  || ($date == '0000-00-00 00:00:00')):
			$date = time();
		break;
		
		case (is_array($date)):
			$date = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
		break;
		
		case (is_numeric($date)):
		break;
		
		case (is_string($date)):
			$date = strtotime($date);
		break;	
	}
	
	$i = 1;
	$months = array();
	while ($i <= 12) { $months[$i] = $i; $i++; }	

	$i = 1;
	$days = array();
	while ($i <= 31) { $days[$i] = $i; $i++; }	

	$i = (date('Y') - 3);
	$years = array();
	while ($i <= (date('Y') + 3)) { $years[$i] = $i; $i++; }	

	if (is_null($date)) $date = $model->fields[$field];
	$date = (is_null($date)) ? time() : $date;

	$out[] = select($model, $field, date('m', $date), $months, array( 'name' => $model->name()."[{$field}][month]" ));
	$out[] = select($model, $field, date('d', $date), $days, array( 'name' => $model->name()."[{$field}][day]" ));
	$out[] = select($model, $field, date('Y', $date), $years, array( 'name' => $model->name()."[{$field}][year]" ));

	return join($out, "\n");
}

?>
