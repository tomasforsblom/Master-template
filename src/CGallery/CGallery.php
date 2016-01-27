<?php

class CGallery {

	// Variabler
	private $GALLERY_PATH;
	private $GALLERY_BASEURL;
	private $path;
	private $validImages = array('png', 'jpg', 'jpeg');

	/**
	 * Konstruktor
	 */ //pathToGallery, $baseUrlToGallery,
	public function __construct($GALLERY_PATH, $GALLERY_BASEURL, $path){
		$this->GALLERY_PATH = $GALLERY_PATH;
		$this->GALLERY_BASEURL = $GALLERY_BASEURL;
		$this->path = realpath($GALLERY_PATH . DIRECTORY_SEPARATOR . $path);
	}

	/**
	 * Read and present images in the current directory
	 */
	public function showGallery(){
		//
		// Validate incoming arguments
		//
		($this->GALLERY_PATH !== false) or $this->errorMessage("The path to the gallery image seems to be a non existing path.");
		$basePath = realpath($this->GALLERY_PATH);
		($basePath !== false) or $this->errorMessage("The basepath to the gallery, GALLERY_PATH, seems to be a non existing path.");
		is_dir($this->GALLERY_PATH) or $this->errorMessage('The gallery dir is not a valid directory.');
		substr_compare($this->GALLERY_PATH, $this->path, 0, strlen($this->GALLERY_PATH)) == 0 or $this->errorMessage('Security constraint: Source gallery is not directly below the directory GALLERY_PATH.');
		
		//
		// Read and present images in the current directory
		//
		if(is_dir($this->path)) {
			return $this->readAllItemsInDir();
		}
		else if(is_file($this->path)) {
			return $this->readItem();
		}
				
	}

	/**
	 * Read directory and return all items in a ul/li list.
	 *
	 * @param string $path to the current gallery directory.
	 * @return string html with ul/li to display the gallery.
	 */
	public function readAllItemsInDir() {
		$files = glob($this->path . '/*');
		$gallery = "<ul class='gallery'>\n";
		$len = strlen($this->GALLERY_PATH);

		foreach($files as $file) {
			$parts = pathinfo($file);
			$href  = str_replace('\\', '/', substr($file, $len + 1));

			// Is this an image or a directory
			if(is_file($file) && in_array($parts['extension'], $this->validImages)) {
				$item = "<img src='img.php?src=" . $this->GALLERY_BASEURL . $href . "&amp;width=128&amp;height=128&amp;crop-to-fit' alt=''/>";
				$caption = basename($file);
			}
			elseif(is_dir($file)) {
				$item    = "<img src='img/folder.png' alt='bild på mapp'/>";
				$caption = basename($file) . '/';
			}
			else {
				continue;
			}

			// Avoid to long captions breaking layout
			$fullCaption = $caption;
			if(strlen($caption) > 18) {
				$caption = substr($caption, 0, 10) . '…' . substr($caption, -5);
			}

			$gallery .= "<li><a href='?path={$href}' title='{$fullCaption}'><figure class='figure overview'>{$item}<figcaption>{$caption}</figcaption></figure></a></li>\n";
		}
		$gallery .= "</ul>\n";

		return $gallery;
	}

	/**
	 * Read and return info on choosen item.
	 *
	 * @param string $path to the current gallery item.
	 * @return string html to display the gallery item.
	 */
	public function readItem() {
		$parts = pathinfo($this->path);
		if(!(is_file($this->path) && in_array($parts['extension'], $this->validImages))) {
			return "<p>This is not a valid image for this gallery. </p>.";
		}

		// Get info on image
		$imgInfo = list($width, $height, $type, $attr) = getimagesize($this->path);
		$mime = $imgInfo['mime'];
		$gmdate = gmdate("D, d M Y H:i:s", filemtime($this->path));
		$filesize = round(filesize($this->path) / 1024);

		// Get constraints to display original image
		$displayWidth  = $width > 800 ? "&amp;width=800" : null;
		$displayHeight = $height > 600 ? "&amp;height=600" : null;
		 
		// Display details on image
		$len = strlen($this->GALLERY_PATH);
		$href = $this->GALLERY_BASEURL . str_replace('\\', '/', substr($this->path, $len + 1));

		$item = <<<EOD
    <p><img src='img.php?src={$href}{$displayWidth}{$displayHeight}' alt=''/></p>
    <p>Original image dimensions are {$width}x{$height} pixels.</p>
    <p><a href='img.php?src={$href}&amp;verbose'>View original image, all information (Verbose mode)</a><br><a href='img.php?src={$href}'>View original image</a></p>
    <p>File size is {$filesize}KBytes.</p>
    <p>Image has mimetype: {$mime}.</p>
    <p>Image was last modified: {$gmdate} GMT.</p>
EOD;

		return $item;
	}

	/**
	 * Create a breadcrumb of the gallery query path.
	 *
	 * @param string $path to the current gallery directory.
	 * @return string html with ul/li to display the thumbnail.
	 */
	public function createBreadcrumb() {
		$parts = explode('/', trim(substr($this->path, strlen($this->GALLERY_PATH) + 1), '/'));
		$breadcrumb = "<ul class='breadcrumb'>\n<li><a href='?'>Hem </a> »</li>\n";

		if(!empty($parts[0])) {
			$combine = null;
			foreach($parts as $part) {
				$combine .= ($combine ? '/' : null) . $part;
				$breadcrumb .= "<li><a href='?path={$combine}'>$part</a> » </li>\n";
			}
		}

		$breadcrumb .= "</ul>\n";
		return $breadcrumb;
	}

	
	/**
	 * Display error message.
	 *
	 * @param string $message the error message to display.
	 */
	public function errorMessage($message) {
		header("Status: 404 Not Found");
		die('gallery.php says 404 - ' . htmlentities($message));
	}
	
}
