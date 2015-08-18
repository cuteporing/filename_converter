<?php
require_once( 'utils/constants.php' );
require_once( 'utils/utils.php' );

class RenameFile {
	var $definePath      = null;
	var $masterData      = null;
	var $illegalFilename = null;
	var $supportedExt    = null;
	var $supportedType   = null;
	var $responseName    = null;
	var $fileList        = array();

	var $title           = null;
	var $season          = null;
	var $directory       = null;
	var $pattern         = null;
	var $description     = "";
	var $episode         = "";

	var $isDone          = false;

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
	 * Set title
	 * @param <string> $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
	}

	/**
	 * Set season
	 * @param <string> $season
	 */
	public function setSeason( $season ) {
		$this->season = $season;
	}

	/**
	 * Set pattern
	 * @param <string> $pattern
	 */
	public function setPattern( $pattern ) {
		$this->pattern = $pattern;
	}

	/**
	 * Set directory
	 * @param <string> $directory
	 */
	public function setDirectory( $directory ) {
		$this->directory = self::sanitizeDir( $directory );
	}

	/**
	 * Get file extension
	 * @param <string> $path
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
		$this->setSupportedExt();
	}

	/**
	 * Remove illegal filename
	 * @param <string> $filename
	 * @return <array> $newFilename
	 */
	public function sanitizeFilename( $filename ) {
		$newFilename = array();

		if( $this->pattern == "pattern1" ) {

			$filename = explode( ' - ', $filename );

			$title = ( is_null( $this->title ) || $this->title == "" )?
				"" : $this->title;

			$this->getEpisode( $filename[0] );
			$this->getFileDesc( $filename[1] );

		} elseif( $this->pattern == "pattern2" ) {
			$filename = str_replace( ' ', '.', $filename );
			$filename = explode( '.', $filename);

			for ( $i = 0; $i < count( $filename ); $i++ ) {
				if ( !in_array( strtoupper( $filename[$i] ), $this->illegalFilename ) ) {
					array_push( $newFilename, $filename[$i] );
				}
			}
			// Title
			$title = ( is_null( $this->title ) || $this->title == "" )?
				ucwords( $newFilename[0] ) : $this->title;

			$this->getEpisode( $newFilename[1] );
			$this->getFileDesc( $newFilename );
		} elseif ( $this->pattern == "pattern3" ) {
			$filename = explode( '-', $filename );

			$title = ( is_null( $this->title ) || $this->title == "" )?
			"" : $this->title;

			$this->getEpisode( $filename[1] );
			$this->getFileDesc( $filename[1] );
		}

		$tempFilename  = ' ';
		$tempFilename .= $this->season.' - ';
		$tempFilename .= $this->episode;
		$tempFilename .= $this->description;

		$newFilename = $title.$tempFilename;

		return $newFilename;
	}

	/**
	 * Get episode from file name
	 * @param <string> $filename
	 * @return <string> $episode
	 */
	public function getEpisode( $filename ) {
		$this->episode = "";

		if( $this->pattern == "pattern1" ) {
			$this->episode = trim( ltrim( $filename, 'Episode'), ' ');
		} else if( $this->pattern == "pattern2" ) {
			if( strpos( $filename, "e" ) ) {
				$temp = explode( "e" , $filename);
				$this->episode = $temp[1];
			} else {
				$this->episode = $filename;
			}
		} else {
			$this->episode = $filename;
		}

		if( (Int) $this->episode < 10 ) {
			$this->episode = '0'.ltrim( (String) $this->episode, '0' );
		}
	}

	/**
	 * Get file description from file name
	 * @param <array> $filename
	 * @return <string> $description
	 */
	public function getFileDesc( $filename ) {
		$this->description = "";
		if( $this->pattern == "pattern1" ) {
			$this->description = $filename;
		} else if( $this->pattern == "pattern2" ) {
			if( count( $filename ) > 2 ) {
				for( $i = 2; $i < count( $filename ); $i++ ){
					$this->description .= ' '.$filename[$i];
				}
			}
		} else {
			$this->description = "";
		}

		if( $this->description != "" ) {
			$this->description = rtrim( ' ( '.ltrim(
					$this->description, '('), ')' ).' )';
		}

		$this->description = ucwords( $this->description );
	}


	/**
	 * Generate new filename
	 * @param <string> $filename
	 * @return <string> $newFilename
	 */
	public function getNewFilename( $filename ) {
		for( $i = 0; $i < count( $this->fileList ); $i++ ) {

			$oldFilename = $this->fileList[$i]['filename']['original'];
			$newFilename = $this->sanitizeFilename( strtolower( $oldFilename ) );

			$this->fileList[$i]['filename']['edited'] = $newFilename;
		}

		$this->isDone = true;
	}

	/**
	 * Get all files under the directory
	 * @param <string> $dir
	 */
	public function getAllFiles( ) {
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$extList = '{'.implode(',', $this->supportedExt['VIDEO'] ).'}';
		$id = 0;

		foreach( glob( $this->directory.$extList, GLOB_BRACE ) as $file ) {
			$tempFile = array();
			$tempFile['id']   = 'file'.$id;
			$tempFile['ext']  = $this->getFileExtention( $file );
			$tempFile['dir']  = $this->directory;
			$tempFile['filename']['original'] = basename(
					$file, '.'.$tempFile['ext'] );

			array_push( $this->fileList, $tempFile );
			$id++;
		}

		finfo_close($finfo);

		if( count( $this->fileList ) == 0 ) {
			Utils::createMsg( 'ERROR_MSG_0003' );
			return;
		}

		$this->getNewFilename();
	}

	/**
	 * Checks if directory is valid
	 * @param <string> $dir
	 * @return boolean
	 */
	public function isDir() {
		if( ! is_dir( $this->directory ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param <object> $request
	 */
	public function getFiles( $request ) {
		$this->setDirectory( $request['data']['directory'] );

		if( $this->isDir() ) {
			$this->setDefinePath();
			$this->setData();
			$this->setTitle( $request['data']['title'] );
			$this->setSeason( $request['data']['season'] );
			$this->setPattern( $request['data']['pattern'] );
			$this->getAllFiles();

			if( $this->isDone ) {
				Utils::createMsg( '', $this->fileList );
			}
		} else {
			Utils::createMsg( 'ERROR_MSG_0002' );
		}
	}

	/**
	 * Rename files
	 * @param <object> $request
	 */
	public function renameFiles( $request ) {
		$this->setDirectory( $request['data'][0]['dir'] );

		if( $this->isDir() ) {
			try {
				// make directory re-writable
				mkdir( dirname( $this->directory ), 0777, true );

				foreach ( $request['data'] as $row ) {
					// source file
					$srcfile = $row['dir'].$row['filename']['original'].'.'.$row['ext'];
					// destination file
					$dstfile = $row['dir'].$row['filename']['edited'].'.'.$row['ext'];
					// rename
					rename($srcfile, $dstfile);
				}

				Utils::setResultMsg();
				Utils::createMsg( '', array( 'msg'=>'Done' ) );
			} catch ( Exception $e ) {
				Utils::createMsg( $e );
			}
		}

	}

	/**
	 * Initialize process
	 * @param <object> $request
	 */
	public function init( $request ) {
		if( $this->responseName == "getFiles" ) {
			$this->getFiles( $request );
		} elseif ( $this->responseName == "renameFiles" ) {
			$this->renameFiles( $request );
		}
	}

	/**
	 * @param <string> $dir
	 * @return mixed
	 */
	public static function sanitizeDir( $dir ) {
		return rtrim( str_replace('\\', '/', $dir), '/' ).'/';
	}
}

if( Utils::isValidRequest() ) {
	$renameFile = new RenameFile;
	$renameFile->setResponseName( $_POST['responseName'] );
	$renameFile->init( $_POST );
} else {
	Utils::createMsg( 'ERROR_MSG_0004' );
}

?>
