<?php

$config['DEFINE_PATH'] = 'http://localhost/filename_converter/define.json'; // define.json
$config['ENVIRONMENT'] = "Development";
// $config['ENVIRONMENT'] = "Production";


// -----------------------------------------------
define( 'ENVIRONMENT', $config['ENVIRONMENT'] );
define( 'DEFINE_PATH', $config['DEFINE_PATH'] );
?>