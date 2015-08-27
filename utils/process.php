<?php
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