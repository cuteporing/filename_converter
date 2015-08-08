<?php
define( 'DEFINE_PATH', 'http://localhost/filename_converter/define.json' );

class RenameFile {
	var $definePath      = null;
	var $masterData      = null;
	var $illegalFilename = null;
	var $sessionList     = null;

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
	 * Set data
	 */
	public function setData() {
		$this->setMasterData();
		$this->setIllegalFilename();
		$this->setSessionList();
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

	public function getAllFiles( $dir ) {
		$files = array_diff( scandir($dir), array('..', '.', 'Thumbs.db') );

		var_dump( $files );
	}

	/**
	 * Initialize process
	 * -------------------------------------
	 * @param <string> $filename
	 */
	public function init( $dir ) {
		$this->setDefinePath();
		$this->setData();
		foreach(glob($dir.'{*.avi,*.mp4}', GLOB_BRACE) as $file)
		{
			echo "filename: $file : filetype: " . filetype($file) . "<br />";
		}
// 		$filename = $this->sanitizeFilename( $filename );
// 		$this->getAllFiles( $dir );
// 		$newFilename = $this->getNewFilename( $filename );

	}
// 	public function init( $filename ) {
// 		$this->setDefinePath();
// 		$this->setData();

// 		$filename = $this->sanitizeFilename( $filename );
// 		$newFilename = $this->getNewFilename( $filename );

// 	}

}

$renameFile = new RenameFile;
$renameFile->init( 'C:\Users\USER\Videos\Vids\SERIES\LEVERAGE\Season 3\*' );
// $renameFile->init( 'Leverage.S03E14.The.Ho.Ho.Ho.Job.HDTV.XviD-FQM' );
?>
