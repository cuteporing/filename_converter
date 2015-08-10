<?php
require_once( 'utils/constants.php' );
require_once( 'utils/utils.php' );

class RenameFile {
	var $definePath      = null;
	var $masterData      = null;
	var $illegalFilename = null;
	var $supportedExt    = null;
	var $title           = null;
	var $season          = null;
	var $responseName    = null;
	var $fileList        = array();

	/**
	 * Defines path for define.json
	 */
	public function setDefinePath() {
		$this->definePath = DEFINE_PATH;
	}

	/**
	 * Set responsename
	 */
	public function setResponseName( $responsename ) {
		if( isset( $responsename ) && !empty( $responsename ) &&
				!is_null( $responsename ) ) {
					$this->responseName = $responsename;
				}
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
	 * Set supported file extension
	 */
	public function setSupportedExt() {
		$this->supportedExt = $this->masterData['SUPPORTED_FILE_EXT'][0];
	}

	/**
	 * @param <string> $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
	}

	/**
	 * @param <string> $season
	 */
	public function setSeason( $season ) {
		$this->season = $season;
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
// 		$this->setSessionList();
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

		$title = ( is_null( $this->title ) )?
			ucwords( $newFilename[0] ) : $this->title;

// 		Utils::log( $newFilename );
		$tempFilename .= ' ';
		$tempFilename .= $this->season.' - ';
		$tempFilename .= $this->getEpisode( $newFilename[1] );
		$tempFilename .= $this->fileDesc( $newFilename );

		$newFilename = $title.$tempFilename;

		return $newFilename;
	}


	/**
	 * @param <string> $filename
	 * @return <string> $episode
	 */
	public function getEpisode( $filename ) {
		$episode = "";

		if( strpos( $filename, "E" ) ) {
			$temp    = explode( "E" , $filename);
			$episode = (String) $temp[1];
		} else {
			$episode = $filename;
		}

		return $episode;
	}

	/**
	 * @param <array> $filename
	 * @return <string> $description
	 */
	public function fileDesc( $filename ) {
		$description = "";
		if( count( $filename ) > 2 ) {
			$description .= ' (';
			for( $i = 2; $i < count( $filename ); $i++ ){
				$description .= ' '.$filename[$i];
			}

			$description .= ' ) ';
		}

		$description = ucwords( strtolower( $description ) );

		return $description;
	}


	/**
	 * @param <string> $filename
	 * @return <string> $newFilename
	 */
	public function getNewFilename( $filename ) {
		for( $i = 0; $i < count( $this->fileList ); $i++ ) {
			$oldFilename = $this->fileList[$i]['filename']['old'];
			$newFilename = $this->sanitizeFilename( $oldFilename );

			$this->fileList[$i]['filename']['new'] = $newFilename;
		}

		Utils::log( $this->fileList );
	}

	/**
	 * Checks if the file type is supported
	 * -------------------------------------
	 * @param <object> $finfo
	 * @param <object> $file
	 * @return <boolean>
	 */
	public function isSupported( $type ) {


		if ( in_array( $type, $this->supportedExt["VIDEO"] ) ||
				 in_array( $type, $this->supportedExt["MUSIC"] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get all files under the directory
	 * @param <string> $dir
	 */
	public function getAllFiles( $dir ) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);

		foreach(glob($dir.'{*.avi,*.mp4,*.mkv}', GLOB_BRACE) as $file) {
			$type = finfo_file($finfo, $file);

			if( ! $this->isSupported( $type ) ) {
				Utils::createMsg('ERROR_MSG_0001');
				return;
			}

			$tempFile = array();
			$tempFile['ext']  = $this->getFileExtention( $file );
			$tempFile['type'] = $type;
			$tempFile['dir']  = $dir;
			$tempFile['filename']['old'] = basename(
					$file, '.'.$tempFile['ext'] );

			array_push( $this->fileList, $tempFile );
		}

		finfo_close($finfo);

		if( count( $this->fileList ) == 0 ) {
			Utils::createMsg('ERROR_MSG_0003');
		}
	}

	/**
	 * Checks if directory is valid
	 * @param <string> $dir
	 * @return boolean
	 */
	public function isDir( $dir ) {
		if( ! is_dir( $dir ) ) {
			Utils::createMsg( 'ERROR_MSG_0002' );
			return false;
		}

		return true;
	}

	/**
	 * Initialize process
	 * -------------------------------------
	 * @param <string> $dir
	 */
	public function init( $dir ) {
		if( $this->isDir( $dir ) ) {
			$this->setDefinePath();
			$this->setData();

			if( $this->responseName == "getFiles" ) {
				$this->getAllFiles( $dir );
				$this->getNewFilename();

				Utils::createMsg( '', $this->fileList );
			}
		}
	}

	/**
	 * @param <string> $dir
	 * @return mixed
	 */
	public static function sanitizeDir( $dir ) {
		return str_replace('\\', '/', $dir).'/';
	}

}

$dir = RenameFile::sanitizeDir( "C:\Users\USER\Videos\Vids\SERIES\LEVERAGE\Season 4" );

$renameFile = new RenameFile;
$renameFile->setTitle( "Leverage" );
$renameFile->setSeason( "III" );
$renameFile->setResponseName( "getFiles" );
$renameFile->init( $dir );
?>
