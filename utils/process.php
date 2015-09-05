<?php
/*********************************************************************************
 ** The contents of this file are subject to file_converter
 * Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: file_converter
 * The Initial Developer of the Original Code is Krishia Valencia.
 * All Rights Reserved.
 *
 ********************************************************************************/

require_once( 'constants.php' );
require_once( 'error_msg.php' );
require_once( 'logger.php' );
require_once( 'utils.php' );
require_once( 'renameFile.php' );

if( Utils::isValidRequest() ) {

	$renameFile = new RenameFile;
	$renameFile->setResponseName( $_POST['responseName'] );
	$renameFile->init( $_POST );
} else {
	Utils::createMsg( 'ERROR_MSG_0004' );
}
?>