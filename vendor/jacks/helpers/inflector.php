<?

function pluralize($word)
{
	$plural = array (
		'/(matr)ix$/i' => '\1ices',
		'/(octop|vir)us$/i' => '\1i',
		'/([m|l])ouse/i' => '\1ice',
		'/(tomato)$/i' => '\1es',
		'/(th)$/i' => '\1s',
		'/(h)$/i' => '\1es',
		'/(ay)$/i' => '\1s',
		'/y$/i' => '\1ies',
		'/^(ox)/i' => '\1en',
		'/(ex)$/i' => 'ices',
		'/(x)$/i' => '\1es',
		'/(ss)$/i' => '\1es',
		'/(us)$/i' => '\1es',
		'/(sis)$/i' => 'ses',
		'/(f|fe)$/i' => 'ves',
		'/(n)ews$/i' => '\1ews',
		'/ium$/i' => '\1ia',
		'/([ti])a$/i' => '\1um',
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
		'/(^analy)ses$/i' => '\1sis',
		'/([^f])ves$/i' => '\1fe',
		'/(hive)s$/i' => '\1',
		'/(tive)s$/i' => '\1',
		'/([lr])ves$/i' => '\1f',
		'/([^aeiouy]|qu)ies$/i' => '\1y',
		'/(s)eries$/i' => '\1eries',
		'/(m)ovies$/i' => '\1ovie',
		'/(x|ch|ss|sh)es$/i' => '\1',
		'/(bus)es$/i' => '\1',
		'/(lo)$/i' => '\1es',
		'/(o)es$/i' => '\1',
		'/(shoe)s$/i' => '\1',
		'/(ax)is$/i' => '\1es',
		'/(us)i$/i' => '\1us',
		'/(vert|ind)ices$/i' => '\1ex',
		'/(alias|status)$/i' => '\1es',
		'/(iz)$/i' => '\1zes',
		'/(tis)$/i' => 'tes',
		'/s$/i' => 's',
		'/$/' => 's'
	);
	
	$uncountable = array( 'data', 'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep', 'history' );
	
	$irregular = array (
		'person' => 'people',
		'man' => 'men',
		'child' => 'children',
		'sex' => 'sexes',
		'move' => 'moves'
	);
	
	$lowercased_word = strtolower($word);
	
	foreach ($uncountable as $_uncountable) {
		if (substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable) {
			return $word;
		}
	}
	
	foreach ($irregular as $_plural=> $_singular) {
		if (preg_match('/('.$_plural.')$/i', $word, $arr)) {
			return preg_replace('/('.$_plural.')$/i', substr($arr[0],0,1).substr($_singular,1), $word);
		}
	}
	
	foreach ($plural as $rule => $replacement) {
		if (preg_match($rule, $word)) {
			return preg_replace($rule, $replacement, $word);
		}
	}

	return false;
}

function singularize($word)
{
	$singular = array (
		'/(n)ews$/i' => '\1ews',
		'/([ti])a$/i' => '\1um',
		'/(perspective)a$/i' => '\1um',
		'/(analy|ba|diagno|parenthe|progno|synop|the)ses$/i' => '\1\2sis',
		'/(^analy)ses$/i' => '\1sis',
		'/(archive)s$/i' => '\1',
		'/(hal)ves$/i' => '\1f',
		'/(dwar)ves$/i' => '\1f',
		'/(tive)s$/i' => '\1',
		'/(l)ves/i' => '\1f',
		'/ves$/i' => '\1fe',
		'/(ax)es/i' => '\1is',
		'/([^f])ves$/i' => '\1fe',
		'/(hive)s$/i' => '\1',
		'/(tive)s$/i' => '\1',
		'/([lr])ves$/i' => '\1f',
		'/(movie)s$/i' => '\1',
		'/([^aeiouy]|qu)ies$/i' => '\1y',
		'/(s)eries$/i' => '\1eries',
		'/(m)ovies$/i' => '\1ovie',
		'/(x|ch|ss|sh)es$/i' => '\1',
		'/([m|l])ice$/i' => '\1ouse',
		'/(bus)es$/i' => '\1',
		'/(shoe)s$/i' => '\1',
		'/(o)es$/i' => '\1',
		'/(cris|ax|test)es$/i' => '\1is',
		'/(octop|vir)i$/i' => '\1us',
		'/(alias|status)es$/i' => '\1',
		'/^(ox)en/i' => '\1',
		'/(vert|ind)ices$/i' => '\1ex',
		'/(matr)ices$/i' => '\1ix',
		'/(quiz)zes$/i' => '\1',
		'/s$/i' => '',
	);
	
	$uncountable = array( 'data', 'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep' );
	
	$irregular = array(
		'person' => 'people',
		'man' => 'men',
		'child' => 'children',
		'sex' => 'sexes',
		'move' => 'moves'
	);
	
	$lowercased_word = strtolower($word);
	foreach ($uncountable as $_uncountable) {
		if (substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable) {
			return $word;
		}
	}
	
	foreach ($irregular as $_plural=> $_singular) {
		if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
			return preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural,1), $word);
		}
	}
	
	foreach ($singular as $rule => $replacement) {
		if (preg_match($rule, $word)) {
			return preg_replace($rule, $replacement, $word);
		}
	}
	
	return $word;
}

function underscore($word)
{
	return strtolower(preg_replace('/[^A-Z^a-z^0-9]+/','_', preg_replace('/([a-z\d])([A-Z])/','\1_\2', preg_replace('/([A-Z]+)([A-Z][a-z])/','\1_\2',$word))));
}

function humanize($word, $uppercase = '')
{
	$uppercase = $uppercase == 'all' ? 'ucwords' : 'ucfirst';
	return $uppercase(str_replace('_',' ',preg_replace('/_id$/', '',$word)));
}

?>
