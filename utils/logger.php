<?php
/*********************************************************************************
 ** The contents of this file are subject to file_converter
 * Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: file_converter
 * The Initial Developer of the Original Code is Krishia Valencia.
 * All Rights Reserved.

 ********************************************************************************/
/*
 * This class is used for creating logs
 * There are 4 types of logs:
 *    1) [INFO]    -- LOG_LVL_INFO
 *    2) [WARNING] -- LOG_LVL_WARN
 *    3) [ERROR]   -- LOG_LVL_ERR
 *    4) [DEBUG]   -- LOG_LVL_DEBUG
 *
 * LOG_PATH = constant variable for log directory.
 *
 */
class Logger {

	private static $instance;

	const LOG_LVL_INFO  = '[INFO]';
	const LOG_LVL_WARN  = '[WARNING]';
	const LOG_LVL_ERR   = '[ERROR]';
	const LOG_LVL_DEBUG = '[DEBUG]';

	private function __construct() {
		$this->logLvl   = self::LOG_LVL_INFO;
		$this->logFilename  = null;
		$this->message  = null;

		$this->init();
	}

	/**
	 * Initialize logger path
	 */
	private function init() {
		$this->logFilename = LOG_PATH.date('Y-m-d').".".LOG_NAME.".log";
	}

	function __destruct() {
		$this->logLvl;
		$this->logFilename;
		$this->message;
	}

	/**
	 * Create logs
	 */
	private function createLog() {
		// Create log directory
		if( ! is_dir( LOG_PATH ) || LOG_PATH == "" ) {
			mkdir(LOG_PATH, 0700);
		}
		$fp = fopen( $this->logFilename, "a" );
		if($fp){
			fwrite( $fp, $this->message );
		}
		fclose( $fp );
	}

	/**
	 * Create message log
	 * @param $msg
	 */
	private function createMsg( $msg ) {
		$this->message = PHP_EOL."[".date('Y-m-d H:i:s')."] ".$this->logLvl;
		// if message is an array
		( is_array( $msg ) )?
			$this->message.= PHP_EOL.print_r( $msg, 1 )
		:	$this->message.= " ".$msg;

		$this->createLog();
	}

	/**
	 * Create info log
	 * @param $msg
	 */
	public static function info( $msg ) {
		if (!self::$instance)
			self::$instance = new self();

		self::$instance->logLvl = self::LOG_LVL_INFO;
		self::$instance->createMsg( $msg );
	}

	/**
	 * Create warning log
	 * @param $msg
	 */
	public static function warn( $msg ) {
		if (!self::$instance) {
			self::$instance = new self();
		}

		self::$instance->logLvl = self::LOG_LVL_WARN;
		self::$instance->createMsg( $msg );
	}

	/**
	 * Create error log
	 * @param $msg
	 */
	public static function error( $msg ) {
		if (!self::$instance) {
			self::$instance = new self();
		}

		self::$instance->logLvl = self::LOG_LVL_ERR;
		self::$instance->createMsg( $msg );
	}

	/**
	 * Create debug log
	 * @param $msg
	 */
	public static function debug( $msg ) {
		if (!self::$instance) {
			self::$instance = new self();
		}

		self::$instance->logLvl = self::LOG_LVL_DEBUG;
		self::$instance->createMsg( $msg );
	}
}
?>