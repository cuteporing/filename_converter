<?php
/**
 * ENVIRONMENT ( Development / Production )
 * 	Development = enables logging
 *	Production   = disables logging
 */

$config['ENVIRONMENT'] = "Production";
$config['HOST']        = "localhost";
$config['HEADER']      = 'http://';

$config['DEFINE_PATH'] = $config['HEADER'].$config['HOST'].'/filename_converter/define.json'; // define.json

$RESPONSE_NAME = array("getFiles", "renameFiles" );
?>