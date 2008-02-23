<?

class Session extends Model
{
	function Session()
	{
		global $database_config;

		// Crap Re-implmentation of the parent function
		$this->__connect_to_database($database_config[ENVIRONMENT]);

		// Register Session Functions
		session_set_save_handler(
			array( &$this, 'open' ),
			array( &$this, 'close' ),
			array( &$this, 'read' ),
			array( &$this, 'write' ),
			array( &$this, 'destroy' ),
			array( &$this, 'gc' )
		);

		// $this->__table_check();

		register_shutdown_function('session_write_close');
		session_start();
	}

	function data($id = false)
	{
		if (!$id) return false;

		$this->__table_check();
		$this->find($id);
	}

	function open($save_path, $session_name)
	{
		return true;
	}
	
	function close()
	{
		$this->gc(get_cfg_var("session.gc_maxlifetime"));
	}
	
	function read($id = false)
	{
		if (!$id) return false;

		$this->find($id);
		return $this->fields['data'];
	}
	
	function write($id, $data)
	{
		if (!$id) return false;

		$expires = time() + get_cfg_var("session.gc_maxlifetime");

		$this->query("SELECT * FROM sessions WHERE id = \"{$id}\"");
		$this->next();

		if ($this->row_count()) {
			$this->query("UPDATE sessions SET expires_at = \"".datetime($expires)."\", updated_at = \"".datetime()."\", data = \"".addslashes($data)."\" WHERE id = \"{$id}\"");
		} else {
			$this->query("INSERT INTO sessions SET id = \"".addslashes($id)."\", expires_at = \"".datetime($expires)."\", updated_at = \"".datetime()."\", data = \"".addslashes($data)."\"");
		}
	}
	
	function destroy($id)
	{
		$this->query("DELETE FROM session WHERE id = \"{$id}\"");
		return $this->affected_rows();
	}
	
	function gc($maxlifetime)
	{
		$this->query("DELETE FROM sessions WHERE expires_at < \"".datetime()."\"");
		return $this->affected_rows();
	}

	// private
	
	function __table_check()
	{
		$this->query("
			CREATE TABLE IF NOT EXISTS sessions (
				id VARCHAR(255) NOT NULL,
				started_at DATETIME DEFAULT NULL,
				updated_at DATETIME DEFAULT NULL,
				expires_at DATETIME DEFAULT NULL,
				data TEXT NOT NULL,
				PRIMARY KEY (id)
			)
		");
	}
}

?>
