<?php
class RenameFile {
	var $definePath      = null;
	var $masterData      = null;
	var $illegalFilename = null;
	var $supportedExt    = null;
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

		Logger::debug( 'RESPONSE NAME ( '.$this->responseName.' )' );
		Logger::debug( '------------------------------------------' );
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
		$this->directory = Utils::sanitizeDir( $directory );
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
		Logger::debug( "Creating a new filename and removing unnecessary description..." );
		$newFilename = array();

		switch ( $this->pattern ) {
			case "pattern1":
				$filename = explode( ' - ', $filename );
				$this->getEpisode( $filename[0] );
				$this->getFileDesc( $filename[1] );
				break;
			case "pattern2":
				if( strpos( $filename, "." ) ) {
					$filename = str_replace( ' ', '.', $filename );
					$filename = explode( '.', $filename);

					$this->getEpisode( $filename[1] );
					$this->getFileDesc( $filename[2] );
				} else {
					( strpos( $filename, " - " ) ) ?
						$filename = explode( ' - ', $filename )
					: $filename = explode( ' ', $filename );

					$this->getEpisode( $filename[1] );
					$this->getFileDesc( $filename[1] );
				}
				break;
			case "pattern3":
				$filename = explode( '-', $filename );
				$this->getEpisode( $filename[1] );
				$this->getFileDesc( $filename[1] );
				break;
		}

		$title = ( is_null( $this->title ) || $this->title == "" )?
			"" : $this->title;

		$tempFilename  = ' ';
		$tempFilename .= $this->season.' - ';
		$tempFilename .= $this->episode;
		$tempFilename .= $this->description;

		$newFilename = preg_replace('!\s+!', ' ', $title.$tempFilename );

		Logger::debug( 'New filename: '.$newFilename );

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
		Logger::info( "Get new filename" );
		for( $i = 0; $i < count( $this->fileList ); $i++ ) {

			$oldFilename = $this->fileList[$i]['filename']['original'];
			$newFilename = $this->sanitizeFilename(
				preg_replace('!\s+!', ' ', strtolower( $oldFilename ) ) );

			$this->fileList[$i]['filename']['edited'] = $newFilename;
		}

		$this->isDone = true;
	}

	/**
	 * Get all files under the directory
	 * @param <string> $dir
	 */
	public function getAllFiles( ) {
		Logger::info( 'Get all files in the directory ');

		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$extList = '{'.implode(',', $this->supportedExt['VIDEO'] ).'}';

		foreach( glob( $this->directory.$extList, GLOB_BRACE ) as $file ) {
			$tempFile = array();
			$tempFile['ext']  = $this->getFileExtention( $file );
			$tempFile['dir']  = $this->directory;
			$tempFile['filename']['original'] = basename(
					$file, '.'.$tempFile['ext'] );

			array_push( $this->fileList, $tempFile );
		}
		finfo_close($finfo);

		if( count( $this->fileList ) == 0 ) {
			Utils::createMsg( 'ERROR_MSG_0003' );
			return;
		}

		$this->getNewFilename();
	}



	/**
	 * @param <object> $request
	 */
	public function getFiles( $request ) {
		$this->setDirectory( $request['data']['directory'] );

		if( Utils::isDir( $this->directory ) ) {
			$this->setDefinePath();
			$this->setData();
			$this->setTitle( $request['data']['title'] );
			$this->setSeason( $request['data']['season'] );
			$this->setPattern( $request['data']['pattern'] );
			$this->getAllFiles();

			if( $this->isDone ) {
				Logger::info( '...Done.' );
				Utils::createMsg( '', $this->fileList );
			}
		}
	}

	/**
	 * Rename files
	 * @param <object> $request
	 */
	public function renameFiles( $request ) {
		$this->setDirectory( $request['data'][0]['dir'] );

		if( Utils::isDir( $this->directory ) ) {
			try {
				// make directory re-writable
				mkdir( dirname( $this->directory ), 0777, true );

				foreach ( $request['data'] as $row ) {
					Logger::debug( 'Start renaming files...' );
					// source file
					$srcfile = $row['dir'].$row['filename']['original'].'.'.$row['ext'];
					// destination file
					$dstfile = $row['dir'].$row['filename']['edited'].'.'.$row['ext'];
					// rename
					rename($srcfile, $dstfile);

					Logger::debug( "Source file: ".$srcfile );
					Logger::debug( "Destination file: ".$dstfile );
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
		Logger::info( 'Initialize request...' );
		switch ( $this->responseName ) {
			case "getFiles"   : $this->getFiles( $request );    break;
			case "renameFiles": $this->renameFiles( $request ); break;
		}
	}


}
?>
