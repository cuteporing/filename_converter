<?php
/**
 * ENVIRONMENT ( Development / Production )
 * 	Developement = enables logging
 *	Production   = disables logging
 */

$config['ENVIRONMENT'] = "Production";
$config['host']        = "localhost";

$config['HEADER']      = 'http://';
$config['DEFINE_PATH'] = $config['HEADER'].$config['host'].'/filename_converter/define.json'; // define.json

$RESPONSE_NAME = array("getFiles", "renameFiles" );
?>