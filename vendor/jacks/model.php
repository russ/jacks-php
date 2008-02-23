<?

class Model
{
	var $database_link;

	var $last_query;
	var $query_link;
	var $pointer = -1;

	var $action;
	var $table_name;
	var $primary_key = 'id';

	var $errors;

	function Model($params = null)
	{
		global $database_config;

		$this->__connect_to_database($database_config[ENVIRONMENT]);

		if (!is_null($params) && is_array($params)) { 
			$this->fields = $params;
		} elseif (!is_null($params)) {
			$this->find($params);
		}

		$this->initialize();
	}

	function find($id)
	{
		$this->__query($this->__build_query(array( "conditions" => "{$this->primary_key} = \"".addslashes($id)."\"" )));
		$this->pointer++;
		$this->__load_row_into_fields();
		return true;
	}

	function find_all($options = array())
	{
		$this->__query($this->__build_query($options));
	}

	function find_first($options = array())
	{
		$this->__query($this->__build_query($options));
		$this->pointer++;
		$this->__load_row_into_fields();
		$this->after_find();
		return true;
	}

	function page($page, $per_page = 10, $options = array())
	{
		$options['limit'] = $per_page;
		$options['offset'] = ($page - 1 <= 0) ? 0 : ($per_page * ($page - 1));
		$this->find_all($options);
	}

	function find_by_sql($sql)
	{
		$this->__query($sql);
		$this->__load_row_into_fields();
	}

	function query($sql)
	{
		$this->__query($sql);
	}

	function next()
	{
		$this->pointer++;
		$result = @mysql_data_seek($this->query_link, $this->pointer);
		if (!$result) return false;
		$this->__load_row_into_fields();
		$this->after_find();
		return true;
	}

	function previous()
	{
		$this->pointer--;
		$result = @mysql_data_seek($this->query_link, $this->pointer);
		if (!$result) return false;
		$this->__load_row_into_fields();
		return true;
	}

	function row_count()
	{
		return @mysql_num_rows($this->query_link);
	}

	function affected_rows()
	{
		return @mysql_affected_rows($this->query_link);
	}

	function name()
	{
		return underscore(get_class($this));
	}

	function table_name()
	{
		if (isset($this->table_name)) return $this->table_name;
		return pluralize(underscore(get_class($this)));
	}

	function is_valid()
	{
		$this->validation();
		return (count($this->errors) == 0);
	}

	function params($params)
	{
		if (is_array($this->fields)) {
			$this->fields = @array_merge($this->fields, $params);
		} else {
			$this->fields = $params;
		}
	}

	function save()
	{
		if (!is_array($this->fields)) return false;

		$this->before_save();

		// Call the Appropriate function
		$result = (is_null($this->fields[$this->primary_key])) ? $this->create() : $this->update();
		if (!$result) return false;

		$this->after_save();

		return true;
	}

	function create()
	{
		$this->action = "create";
		if (!$this->is_valid()) return false;
		$this->before_create();
		$this->query("INSERT INTO {$this->table_name()} SET {$this->__fields_to_sql_string($this->fields)}");
		$this->fields[$this->primary_key] = $this->last_insert_id();
		$this->process_uploads();
		$this->after_create();
		return true;
	}

	function update()
	{
		$this->action = "update";
		if (!$this->is_valid()) return false;
		$this->before_update();
		$this->query("UPDATE {$this->table_name()} SET {$this->__fields_to_sql_string($this->fields)} WHERE {$this->primary_key} = \"".addslashes($this->fields[$this->primary_key])."\"");
		$this->process_uploads();
		$this->after_update();
		return true;
	}

	function destroy()
	{
		$this->action = "destroy";
		$this->before_destroy();
		$this->query("DELETE FROM {$this->table_name()} WHERE {$this->primary_key} = '".addslashes($this->fields[$this->primary_key])."'");
		$this->destroy_uploads();
		$this->after_destroy();
		return true;
	}
	
	function last_insert_id()
	{
		return mysql_insert_id($this->database_link);
	}

	function process_uploads()
	{
		if (isset($this->uploads) && count($this->uploads) > 0) {
			for ($i = 0; $i < count($this->uploads); $i++) {
				$this->uploads[$i]->upload_file();
				unset($this->uploads[$i]);
				$this->update();
			}
		}
	}

	function destroy_uploads()
	{
		if (isset($this->uploads)) foreach ($this->uploads as $upload) $upload->destroy();
	}

	// Callback Stubs

	function initialize() { }

	function before_save() { }
	function before_create() { }
	function before_update() { }
	function after_create() { }
	function after_update() { }
	function after_save() { }

	function after_find() { }

	function before_destroy() { }
	function after_destroy() { }

	function validation() { return true; }

	// PRIVATE

	function __query($sql)
	{
		$this->query_link = mysql_query($sql, $this->database_link);
		if (!$this->query_link) { die("Invalid Query: ".mysql_error()); }
		$this->last_query = $sql;
		Logger::log($sql);
	}

	function __build_query($options)
	{
		$from = ($options['from']) ? $options['from'] : $this->table_name();
		$select = ($options['select']) ? $options['select'] : "*";
		$sql = "SELECT {$select} FROM ".strtolower($from);

		if ($options['conditions'])	$sql .= " WHERE {$options['conditions']}";
		if ($options['order'])			$sql .= " ORDER BY {$options['order']}";
		if ($options['limit'])			$sql .= " LIMIT {$options['limit']}";
		if (isset($options['offset']))			$sql .= " OFFSET {$options['offset']}";

		return $sql;
	}

	function __fields_to_sql_string($fields)
	{
		foreach ($fields as $k => $v) 
		{
			// Set Magic Fields
			switch ($k) {
				case "created_at":
					if (is_null($v) && is_null($this->fields[$primary_key])) $v = datetime();
					break;
				case "updated_at":
					if (is_null($v)) $v = datetime();
					break;
			}

			// Append Fields
			$sql[] = "{$k} = \"".addslashes($v)."\"";
		}
		return join(", ", $sql);
	}

	function __load_row_into_fields()
	{
		$this->fields = mysql_fetch_assoc($this->query_link);
		if (is_array($this->fields)) foreach ($this->fields as $k => $v) $this->fields[$k] = stripslashes($v);
	}

	function __connect_to_database($config)
	{
		$this->database_link = mysql_connect($config['host'], $config['user'], $config['password']) or die("Could not connect to database.");
		$result = mysql_select_db($config['database'], $this->database_link);
		if (!$result) { die("Database Error: ".mysql_error()); }
	}
}

?>
