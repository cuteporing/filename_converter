<?php
require_once( 'error_msg.php' );

class Utils {

	public static function log( $msg, $label, $notResponse = false ) {
		$path = LOG_PATH . date('Y-m-d') . ".fconverter.log";
		$fp   = fopen( $path ,"a" );
		if($fp){
			if( is_array( $msg ) ) {
				if( isset( $label ) && !empty( $label ) ) {
					fwrite( $fp, PHP_EOL."[".date('Y-m-d H:i:s')."] : ".$label );
				}

				fwrite( $fp, PHP_EOL.print_r( $msg, 1 ) );
			} else {
				if( isset( $label ) && !empty( $label ) ) {
					$msg = $label.PHP_EOL.$msg;
				}

				fwrite( $fp, PHP_EOL."[".date('Y-m-d H:i:s')."] : ".$msg );
			}

			fclose( $fp );
		}

	}

	/**
	 * @param <string>  $errorCode
	 * @return <string> $errorCode
	 */
	public static function createErrCode( $errorCode ) {
		if( isset( $errorCode ) && !empty( $errorCode )	){
			if( !defined( $errorCode ) ){
				$errorCode = "ERROR";
			}else{
				( strpbrk($errorCode,'ERROR_MSG_') )?
					$errorCode = str_replace( 'ERROR_MSG_', '', $errorCode )
					: $errorCode = 'ERROR_MSG_'.$errorCode;
			}

			Utils::log( $errorCode, "ERROR CODE" );
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
		if ( isset( $errorCode ) && !empty( $errorCode ) ){
			if( !defined( $errorCode ) ){
				$errMsg = $errorCode;
			} else {
				$errMsg = constant($errorCode);
			}

			Utils::log( $errMsg, "ERROR MESSAGE" );
		} else {
			$errMsg = "";
		}

		return $errMsg;
	}

	public static function setResultMsg( $newResponseName ) {
		if ( isset( $newResponseName ) && !is_null( $newResponseName ) &&
				!empty( $newResponseName ) ) {
			$_POST['responseName'] = $newResponseName;
		} else {
			$_POST['responseName'] = 'result';
		}
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

		echo json_encode( $msg );
	}

	public static function isValidRequest() {
		global $RESPONSE_NAME;
		$valid = false;

		if( isset( $_POST['responseName'] ) && !is_null( $_POST['responseName'] ) ) {

			( in_array( $_POST['responseName'], $RESPONSE_NAME ) )?
				$valid = true : $valid = false;
		}

		Utils::log( $valid, "** isValidRequest **" );
		Utils::log( $_POST['responseName'], "responseName" );

		return $valid;
	}
}

?>