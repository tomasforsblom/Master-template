<?php
//f�r att f� det att fungera lokalt
//include('/../src/CImage/CImage.php');

//f�r att f� det att fungera p� studentservern
include('../src/CImage/CImage.php');

// Ensure error reporting is on
//
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly

$dir = __DIR__;

$image = new CImage($dir);
