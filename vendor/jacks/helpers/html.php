<?

function content_tag($tag, $value, $options = null)
{
	$picky_full_tags = array( 'script' );
	if (is_array($options)) foreach ($options as $k => $v) $options_string[] = "{$k}=\"{$v}\"";
	$content[] = "<{$tag} ".@join($options_string, " ");
	$content[] = ($value != null || in_array($tag, $picky_full_tags)) ? ">{$value}</{$tag}>" : " />";
	return join($content, '');
}

function error_messages_for($model)
{
	if (is_array($model->errors)) {
		$content[] = "<div class=\"error_messages\">";
		$content[] = "<h5>There were errors with the information you entered:</h5>";
		$content[] = "<ul class=\"errors\">";
		foreach ($model->errors as $k => $v) {
			$content[] = content_tag("li", $v);
		}
		$content[] = "</ul>";
		$content[] = "</div>";
		return join($content, "\n");
	}
}

function stylesheet_link_tag($styles, $cache = false)
{
	if (PERFORM_CACHING && $cache != false) {
  	$joined_stylesheet_name = ($cache == true) ? "all.css" : "{$cache}.css";
  	$joined_stylesheet_path = APPLICATION_ROOT."/public/stylesheets/{$joined_stylesheet_name}";
		write_asset_file_contents($joined_stylesheet_path, APPLICATION_ROOT.'/public/stylesheets', 'css');
		return content_tag("link", null, array( 'rel' => "stylesheet", 'href' => "/stylesheets/{$joined_stylesheet_name}", 'type' => "text/css", 'media' => "all" ));
	} else { 
		$tags = array();
		foreach ($styles as $style) $tags[] = content_tag("link", null, array( 'rel' => "stylesheet", 'href' => "/stylesheets/{$style}.css", 'type' => "text/css", 'media' => "all" ));
		return join($tags, "\n");
	}
}

function javascript_link_tag($javascripts, $cache = false)
{
	if (PERFORM_CACHING && $cache != false) {
  	$joined_javascript_name = ($cache == true) ? "all.js" : "{$cache}.js";
  	$joined_javascript_path = APPLICATION_ROOT."/public/javascripts/{$joined_javascript_name}";
		write_asset_file_contents($joined_javascript_path, APPLICATION_ROOT.'/public/javascripts', 'js');
		return content_tag("script", ' ', array( 'language' => "javascript", 'src' => "/javascripts/{$joined_javascript_name}", 'type' => "text/javascript" ));
	} else { 
		$tags = array();
		foreach ($javascripts as $javascript) $tags[] = content_tag("script", '', array( 'language' => "javascript", 'src' => "/javascripts/{$javascript}.js", 'type' => "text/javascript" ));
		return join($tags, "\n");
	}
}

function write_asset_file_contents($joined_file_path, $sources_path, $extension)
{
	$content = array();
	$d = Dir($sources_path);
	while (false !== ($entry = $d->read())) {
		if ($entry != '.' && $entry != '..' && "{$sources_path}/{$entry}" != $joined_file_path && preg_match("/\.{$extension}$/", $entry) > 0) {
			$content[] = file_get_contents("{$sources_path}/{$entry}");
		}
	}
	$d->close();
	file_put_contents($joined_file_path, join($content, "\n"));
}

function link_to($text, $url, $options = array())
{
	$options['href'] = $url;

	if (isset($options['confirm'])) {
		$options['onclick'] = "javascript:return confirm('".addslashes($options['confirm'])."')";
		unset($options['confirm']);
	}

	return content_tag("a", $text, $options);
}

function mail_to($email, $options = array())
{
	$options['href'] = "mailto:{$email}";
	return content_tag("a", $email, $options);
}

?>
