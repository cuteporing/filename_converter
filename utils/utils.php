<?php
require_once( 'error_msg.php' );

class Utils {

	public static function log( $info, $label, $notResponse = false ) {

		if ( ENVIRONMENT != 'Development' && !$notResponse) {
			return;
		}
		if( isset( $label ) && !empty($label) ) {
			echo "<hr>";
			echo $label."\xA";
			echo "<hr>";
		}


		if( is_array( $info ) )
			echo '<pre>',print_r( $info ),'</pre>';
		else
			echo $info;

	}

	/**
	 * @param <string>  $errorCode
	 * @return <string> $errorCode
	 */
	public static function createErrCode( $errorCode ) {
		if( isset( $errorCode ) && !empty( $errorCode )	){
			( strpbrk($errorCode,'ERROR_MSG_') )?
				$errorCode = str_replace( 'ERROR_MSG_', '', $errorCode )
			: $errorCode = 'ERROR_MSG_'.$errorCode;
		} else {
			$errorCode = "";
		}

		return $errorCode;
	}

	/**
	 * @param <string> $errorCode
	 * @return <string>
	 */
	public static function createErrMsg( $errorCode ) {
		( isset( $errorCode ) && !empty( $errorCode ) )?
			$errMsg = constant($errorCode) : $errMsg = "";

		return $errMsg;
	}

	/**
	 * @param <string> $errorCode
	 * @param <array>  $data
	 * @return <array> $msg
	 */
	public static function createMsg( $errorCode, $data ) {
		$msg['responseName'] = $_POST['responseName'];
		$msg['errorCode']    = self::createErrCode( $errorCode );
		$msg['errorMsg']     = self::createErrMsg( $errorCode );


		( isset( $data ) && !empty( $data ) && !is_null( $data ) )?
			$msg['data'] = $data : $msg['data'] = array();

		self::log( $msg, '' );

		echo json_encode( $msg );
	}

	public static function isValidRequest() {
		global $RESPONSE_NAME;
		$valid = false;

		if( isset( $_POST['responseName'] ) && !is_null( $_POST['responseName'] ) ) {

			( in_array( $_POST['responseName'], $RESPONSE_NAME ) )?
				$valid = true : $valid = false;
		}

		return $valid;
	}
}

?>