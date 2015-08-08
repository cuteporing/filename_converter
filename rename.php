<?php
require_once( 'constants.php' );
require_once( 'utils.php' );

class RenameFile {
	var $definePath      = null;
	var $masterData      = null;
	var $illegalFilename = null;
	var $sessionList     = null;
	var $fileList        = array();
	var $supportedExt    = null;

	public function setDefinePath() {
		$this->definePath = DEFINE_PATH;
	}

	/**
	 * Set masterData
	 */
	public function setMasterData() {
		$this->masterData = json_decode(
				file_get_contents( $this->definePath ), true );
	}

	/**
	 * Set list of illegal filenames
	 */
	public function setIllegalFilename() {
		$this->illegalFilename = $this->masterData['ILLEGAL_FILENAME'];
	}

	/**
	 * Set session list
	 */
	public function setSessionList() {
		$this->sessionList = $this->masterData['SEASON_ROMAN'];
	}

	/**
	 * Set supported file extension
	 */
	public function setSupportedExt() {
		$this->supportedExt = $this->masterData['SUPPORTED_FILE_EXT'];
	}

	/**
	 * Get file extension
	 */
	public function getFileExtention( $path ) {
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Set data
	 */
	public function setData() {
		$this->setMasterData();
		$this->setIllegalFilename();
		$this->setSessionList();
		$this->setSupportedExt();
	}

	/**
	 * Remove illegal filename
	 * -------------------------------------
	 * @param <string> $filename
	 * @return <array> $newFilename
	 */
	public function sanitizeFilename( $filename ) {
		$newFilename = array();
		$filename = explode( '.', $filename);

		for ( $i = 0; $i < count( $filename ); $i++ ) {
			if ( !in_array( strtoupper( $filename[$i] ), $this->illegalFilename ) ) {
				array_push( $newFilename, $filename[$i] );
			}
		}

		return $newFilename;
	}

	/**
	 * @param <string> $filename
	 * @return <string> $newFilename
	 */
	public function getNewFilename( $filename ) {
		$newFilename = "";
	}

	/**
	 * Checks if the file type is supported
	 * -------------------------------------
	 * @param <object> $finfo
	 * @param <object> $file
	 * @return <boolean>
	 */
	public function isSupported( $finfo, $file ) {
		$type = finfo_file($finfo, $file);

		if ( in_array( $type, $this->supportedExt[0]["VIDEO"] ) ||
				 in_array( $type, $this->supportedExt[1]["MUSIC"] ) ) {
			return true;
		}

		return false;
	}

	public function getAllFiles( $dir ) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);

		foreach(glob($dir.'{*.avi,*.mp4}', GLOB_BRACE) as $file) {

			if( ! $this->isSupported( $finfo, $file ) ) {
				echo 'not supported';
				return;
			}

			$tempFile = array();
			$tempFile['old']['ext']      = $this->getFileExtention( $file );
			$tempFile['old']['filename'] = basename($file, '.'.$tempFile['old']['ext']);
			$tempFile['old']['dir']      = $dir;

			array_push( $this->fileList, $tempFile );
		}

		Utils::log( $this->fileList );

		finfo_close($finfo);
	}

	/**
	 * Initialize process
	 * -------------------------------------
	 * @param <string> $dir
	 */
	public function init( $dir ) {
		$this->setDefinePath();
		$this->setData();

		$this->getAllFiles( $dir );
	}

}

// $dir = str_replace('\\', '/', "C:\Users\Krishia\Videos\SERIES\The Universe\Season 2");
$dir = str_replace('\\', '/', "C:\Users\Krishia\Videos\iPhone_VID");

$renameFile = new RenameFile;
$renameFile->init( $dir.'/' );
// $renameFile->init( 'Leverage.S03E14.The.Ho.Ho.Ho.Job.HDTV.XviD-FQM' );
?>
