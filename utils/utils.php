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

class Utils {

	/**
	 * Function for generating error code
	 *
	 * @param string $errorCode
	 * @return string $errorCode
	 */
	public static function createErrorCode( $errorCode ) {
		if( isset( $errorCode ) && !empty( $errorCode )	){
			if( !defined( $errorCode ) ){
				$errorCode = "ERROR";
			}else{
				( strpbrk($errorCode,'ERROR_MSG_') )?
					$errorCode = str_replace( 'ERROR_MSG_', '', $errorCode )
				:	$errorCode = 'ERROR_MSG_'.$errorCode;
			}

		} else {
			$errorCode = "";
		}

		return $errorCode;
	}

	/**
	 * Function for generating an error message
	 *
	 * @param string $errorCode -- constant variable
	 * @return string $errorMsg
	 */
	public static function createErrorMsg( $errorCode ) {
		if ( isset( $errorCode ) && !empty( $errorCode ) ){
			if( !defined( $errorCode ) ){
				$errorMsg = $errorCode;
			} else {
				$errorMsg = constant($errorCode);
			}

			Logger::error( $errorMsg );
		} else {
			$errorMsg = "";
		}

		return $errorMsg;
	}

	/**
	 * Function for setting a result response
	 *
	 * @param string $newResponseName
	 */
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
	 *
	 * @param string $errorCode
	 * @param array  $data
	 * @return array $msg
	 */
	public static function createMsg( $errorCode, $data ) {
		$msg['responseName'] = $_POST['responseName'];
		$msg['errorCode']    = self::createErrorCode( $errorCode );
		$msg['errorMsg']     = self::createErrorMsg( $errorCode );


		( isset( $data ) && !empty( $data ) && !is_null( $data ) )?
			$msg['data'] = $data : $msg['data'] = array();

		echo json_encode( $msg );
	}

	/**
	 * Check if request is valid
	 *
	 * @return boolean $valid
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
	 * Function for sanitizing file directory
	 *
	 * @param string $dir
	 * @return string
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
	 * @param string $dir -- directory
	 * @return boolean -- true (valid directory)
	 *                 -- false (return an error message)
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