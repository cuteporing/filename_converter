<?php
/*********************************************************************************
 ** The contents of this file are subject to file_converter
 * Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: file_converter
 * The Initial Developer of the Original Code is Krishia Valencia.
 * All Rights Reserved.

 ********************************************************************************/

/*
 * ENVIRONMENT ( Development / Production )
 * 	Development = enables logging ( Client side )
 *	Production  = disables logging ( Client side )
 */

$config['ENVIRONMENT'] = "Development";
$config['HOST']        = "localhost";
$config['HEADER']      = 'http://';
// Directory for logs
// $default = '../log/'
$config['LOG_PATH']    = '../log/';

// End of user define settings.
// Do not edit anything beyond this line
// -------------------------------------------
$config['DEFINE_PATH'] = $config['HEADER'].$config['HOST'].'/filename_converter/define.json'; // define.json
$config['LOG_NAME']    = 'filename_converter';
$RESPONSE_NAME = array("getFiles", "renameFiles" );
?>