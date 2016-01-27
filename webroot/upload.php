<?php
// Include the essential config-file which also creates the $master variable with its defaults.
include(__DIR__.'/config.php');

// Store the title in the the Master container.
$master['title'] = "Bekräftelse på uppladdade filer";

$target_dir = "img/movie/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
$output = null;

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	if($check !== false) {
		$output = "Filen är en bild av typen - " . $check["mime"] . ".";
		$uploadOk = 1;
	} else {
		$output .= "Filen är inte en bild.";
		$uploadOk = 0;
	}
}
// Check if file already exists
if (file_exists($target_file)) {
	$output .= "Filen finns redan.";
	$uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
	$output .= "Filen är för stor. Maximal storlek är 5 mb.";
	$uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			$output .= "Filen kunde inte laddas upp. Du kan enbart ladda upp filer i formaten JPG, JPEG, PNG & GIF.";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$output .= "Filen kunde inte laddas upp.";
			// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				$output .= "Filen ". basename( $_FILES["fileToUpload"]["name"]). " har laddats upp.";
			} else {
				$output .= "Tyvärr kunde filen inte laddas upp.";
			}
		}

$master['main']= <<<EOD
{$output}
<br>
<br>
<br>
<p>Tryck här för att gå tillbaks till Mitt konto: <a class="btn" href="my_account.php">Mitt konto</a></p>
EOD;

// Finally, leave it all to the rendering phase of Master.
include(MASTER_THEME_PATH);		
