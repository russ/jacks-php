<?

class Mailer
{
	function deliver($view, $data)
	{
		$headers = array();
		$headers[] = "From: {$this->from}";
		if (isset($this->reply_to)) $headers[] = "Return-Path: {$this->reply_to}";

		$class_name = strtolower(pluralize(str_replace('Mailer', '', get_class($this))));

		// Load HTML View
		if (!isset($this->html) && file_exists(APPLICATION_ROOT."/app/views/{$class_name}/{$view}.php")) {
			ob_start();
			require APPLICATION_ROOT."/app/views/{$class_name}/{$view}.php";
			$this->html = ob_get_contents();
			ob_end_clean();
		}

		// Load Text View
		if (!isset($this->text) && file_exists(APPLICATION_ROOT."/app/views/{$class_name}/{$view}_text.php")) {
			ob_start();
			require APPLICATION_ROOT."/app/views/{$class_name}/{$view}_text.php";
			$this->text = ob_get_contents();
			ob_end_clean();
		}
		$mime_boundary = '='.md5(time());

		// Headers
		$headers[] = "From: {$this->from}";
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-Type: multipart/alternative; boundary=\"{$mime_boundary}\""; 

		// Mashed Body
		$body .= "--{$mime_boundary}\n";
		$body .= "Content-Type: text/plain; charset=iso-8859-1\n";
		$body .= "Content-Transfer-Encoding: 8bit\n\n";
		$body .= "{$this->text}\n\n";
		$body .= "--{$mime_boundary}\n";
		$body .= "Content-Type: text/html; charset=iso-8859-1\n";
		$body .= "Content-Transfer-Encoding: quoted-printable\n\n";
		$body .= "{$this->html}\n\n";
		$body .= "--{$mime_boundary}--\n\n";

		foreach ($this->recipients as $recipient) mail($recipient, $this->subject, $body, join($headers, "\n"));
	}
}

?>
