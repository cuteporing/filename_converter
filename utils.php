<?php
require_once( 'error_msg.php' );

class Utils {

	public static function log( $info ) {
		if ( ENVIRONMENT == 'Development' ) {
			echo '<pre>',print_r( $info ),'</pre>';
		}
	}

	public static function createMsg( $errorCode, $data ) {
		( isset( $errorCode ) && !empty( $errorCode ) )?
			$msg['errorCode'] = $errorCode : $msg['errorCode'] = '';

		( isset( $errorCode ) && !empty( $errorCode ) )?
			$msg['errorMsg'] = constant($errorCode) : $msg['errorMsg'] = '';

		( isset( $errorCode ) && !empty( $errorCode ) )?
			$msg['data']
	}
}

?>