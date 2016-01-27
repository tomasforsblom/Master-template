<?php 
/**
 * This is a Master pagecontroller.
 *
 */
// Include the essential config-file which also creates the $master variable with its defaults.
include(__DIR__.'/config.php'); 



// Do it and store it all in variables in the Anax container.
$master['title'] = "404";
$master['header'] = "";
$master['main'] = "This is a Anax 404. Document is not here.";
$master['footer'] = "";

// Send the 404 header 
header("HTTP/1.0 404 Not Found");


// Finally, leave it all to the rendering phase of Master.
include(MASTER_THEME_PATH);
