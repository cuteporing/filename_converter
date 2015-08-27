<?php
class Utils {
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

			Logger::error( $errMsg );
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
	 * Create response message
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

	/**
	 * Check if request is valid
	 * @return <boolean> $valid
	 */
	public static function isValidRequest() {
		Logger::debug( '------------------------------------------' );
		global $RESPONSE_NAME;
		$valid = false;

		if( isset( $_POST['responseName'] ) && !is_null( $_POST['responseName'] ) ) {

			if ( in_array( $_POST['responseName'], $RESPONSE_NAME ) ) {
				$valid = true;
			} else {
				$valid = false;
				Logger::warn( "Request name {$_POST['responseName']} is not valid" );
			}
		}

		return $valid;
	}

	/**
	 * @param <string> $dir
	 * @return mixed
	 */
	public static function sanitizeDir( $dir ) {
		if( !empty( $dir ) ) {
			return rtrim( str_replace('\\', '/', $dir), '/' ).'/';
		} else {
			return "";
		}
	}

	/**
	 * Checks if directory is valid
	 * @param <string> $dir
	 * @return boolean
	 */
	public static function isDir( $dir ) {
		Logger::debug( 'Directory: '.$dir );
		if( ! is_dir( $dir ) || $dir == "" ) {
			Utils::createMsg( 'ERROR_MSG_0002' );
			return false;
		}

		return true;
	}
}

?>