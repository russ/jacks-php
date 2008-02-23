<?

class FileUpload
{
	var $upload_directory;
	var $accepted_file_types;

	var $model;
	var $field;
	var $file_type;
	var $file_name;
	var $file_path;

	var $max_file_size;

	function FileUpload($model, $field, $options = array())
	{
		$this->model = $model;
		$this->field = $field;

		$this->upload_directory = $options['upload_directory'];
		$this->accepted_file_types = (isset($options['accepted_file_types'])) ? $options['accepted_file_types'] : array( 'jpeg', 'jpg', 'png', 'gif' );
		if (isset($options['max_file_size'])) $this->max_file_size = $options['max_file_size'];
		if (isset($options['resize'])) $this->resize = $options['resize'];
		if (isset($options['thumbnails'])) $this->thumbnails = $options['thumbnails'];
	}

	function upload_file()
	{
		if (!isset($_FILES[$this->model->name()])) return;

		// Set Information for Later Use
		$this->file_path = "{$this->upload_directory}".$this->partitioned_path();
		$this->file_name = $_FILES[$this->model->name()]['name'][$this->field];
		$this->full_path = "{$this->file_path}/{$this->file_name}";

		// Check for max file size
		if (isset($this->max_file_size) && $_FILES[$this->model->name()]['size'][$this->field] > $this->max_file_size) $this->model->errors[] = "Uploaded file is to large.";

		// Check for accepted file types
		$this->file_type = strtolower($this->file_extension($_FILES[$this->model->name()]['name'][$this->field]));
		if (!in_array($this->file_type, $this->accepted_file_types)) $this->model->errors[] = "The file type '{$this->file_type}' not allowed.";

		// Upload the file
		if (file_exists(APPLICATION_ROOT."/public{$this->file_path}")) $this->destroy(); 
		mkdirp(APPLICATION_ROOT."/public/{$this->upload_directory}".$this->partitioned_path());
		if (!is_uploaded_file($_FILES[$this->model->name()]['tmp_name'][$this->field])) $this->model->errors[] = "Error uploading file.";
		if (!copy($_FILES[$this->model->name()]['tmp_name'][$this->field], APPLICATION_ROOT."/public/{$this->full_path}")) $this->model->errors[] = "Error uploading file.";

		// Resize the image
		if (isset($this->resize)) $this->resize_image($this->resize, APPLICATION_ROOT."/public/{$this->full_path}", APPLICATION_ROOT."/public/{$this->full_path}");
		$this->model->fields[$this->field] = $this->full_path;

		// Create thumbnails
		if (isset($this->thumbnails)) {
			foreach ($this->thumbnails as $name => $resize) {
				$this->resize_image($resize, APPLICATION_ROOT."/public/{$this->full_path}", APPLICATION_ROOT."/public/{$this->file_path}/{$name}_{$this->file_name}");
				$this->model->fields["{$name}_{$this->field}"] = "{$this->file_path}/{$name}_{$this->file_name}";
			}
		}
	}

	function partitioned_path()
	{
		return preg_replace("/(....)/", "/$1", sprintf("%08d", $this->model->fields[$this->model->primary_key]));
	}

	function file_extension($filename)
	{
		$i = strrpos($filename, '.');
		if (!$i) return '';
		$l = strlen($filename) - $i;
		$ext = substr($filename, $i + 1, $l);
		return $ext;
	}

	function destroy()
	{
		exec("rm -rf ".APPLICATION_ROOT."/public{$this->upload_directory}".$this->partitioned_path());
	}

	// Resizing Functions

	function resize_image($size, $original, $copy)
	{
		$this->image_size = getimagesize($original);

		switch ($this->file_type)
		{
			case "gif":
				imagegif($this->process_image($size, imagecreatefromgif($original)), $copy);
				break;
			case "jpg":
			case "jpeg":
				imagejpeg($this->process_image($size, imagecreatefromjpeg($original)), $copy);
				break;
			case "png":
				imagepng($this->process_image($size, imagecreatefrompng($original)), $copy);
				break;
		}
	}

	function process_image($size, $image)
	{
		if ($this->image_size[0] >= $this->image_size[1]) {
			$width = $size;
			$height = ($width / $this->image_size[0]) * $this->image_size[1];
		} else {
			$height = $size;
			$width = ($height / $this->image_size[1]) * $this->images_size[0];
		}

		$canvas = ImageCreateTrueColor($width, $height);
		imagecopyresampled($canvas, $image, 0, 0, 0, 0, $width, $height, $this->image_size[0], $this->image_size[1]);

		return $canvas;
	}
}

?>
