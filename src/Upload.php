<?php
require __DIR__ . '/../autoload.php';
use MK\Logger;

class Upload {

	private $file; // $_FILES["fileToUpload"]
	private $target_dir = "uploads/";
	private $uploadOk = 1;
	private $target_file;
	private $imageFileType;

	public function __construct($dir,$file) {
		$this->file = $file;
		Logger::debug(__LINE__ . " Upload() DIR=$dir, File=" . json_encode($file));
		if($dir && !empty($dir)) { $this->target_dir = $dir; }
		Logger::debug(__LINE__ . " Upload() target_dir=" . $this->target_dir);
		$this->target_file = "/var/www/html/Produkte/uploads/" . basename($file["name"]);
		Logger::debug(__LINE__ . " Upload() target_file=" . $this->target_file);
		$this->imageFileType = strtolower(pathinfo($this->target_file,PATHINFO_EXTENSION));
		Logger::debug(__LINE__ . " Upload() imageFileType=" . $this->imageFileType);
	}

	public function upload() {
		// $this->isImage();
		// $this->exists();
		// $this->checkFileSize();
		// $this->allowFileFormat();

		// Check if $uploadOk is set to 0 by an error
		//if ($this->uploadOk == 0) {
		//	Logger::error(__LINE__ . " Upload::upload() Sorry, your file was not uploaded.");
		// if everything is ok, try to upload file
		//} else {
		Logger::debug(__LINE__ . " Upload::upload() source=" . $this->file["tmp_name"] . ", target=" . $this->target_file);
			if (move_uploaded_file($this->file["tmp_name"], $this->target_file)) {
				Logger::info(__LINE__ . " Upload::upload() The file ". htmlspecialchars( basename( $this->file["name"])). " has been uploaded.");
			} else {
				Logger::error(__LINE__ . " Upload::upload() Sorry, there was an error uploading your file.");
			}
		//}
	}

	private function isImage() {
	  // Check if image file is a actual image or fake image
	  $check = getimagesize($this->file["tmp_name"]);
	  if($check !== false) {
	    Logger::info(__LINE__ . " Upload::isImage() File is an image - " . $check["mime"] . ".");
	    $this->uploadOk = 1;
	  } else {
	    Logger::warn(__LINE__ . " Upload::isImage() File is not an image.");
	    $this->uploadOk = 0;
	  }
	}
	
	private function exists() {
		// Check if file already exists
		Logger::debug(__LINE__ . " Upload::exists() File: $target_file");
		if (file_exists($target_file)) {
			Logger::warn(__LINE__ . " Upload::exists() Sorry, file already exists.");
			$uploadOk = 0;
		}
	}

	private function checkFileSize() {	
		// Check file size
		Logger::debug(__LINE__ . " Upload::checkFileSize()");
		if ($_FILES["fileToUpload"]["size"] > 500000) {
			Logger::warn(__LINE__ . " Upload::checkFileSize() Sorry, your file is too large.");
			$uploadOk = 0;
		}
	}
	
	private function allowFileFormat() {
		// Allow certain file formats
		Logger::debug(__LINE__ . " Upload::allowFileFormat()");
		$imageFileType = $this->imageFileType;
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
			Logger::warn(__LINE__ . "Upload::allowFileFormat() Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
	  		$uploadOk = 0;
		}
	}
	
}
?>

