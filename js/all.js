$( document ).ready( function() {
	var masterData = null;

	var defineData = (function() {
		$.ajax({
			'global': false,
			'url': "/filename_converter/define.json",
			'dataType': "json",
			'success': function (data) {
				masterData = data;
				populateSeasonList();
			}
		});
		return;
	})();
	
	// Add Season on options
	function populateSeasonList() {
		var season  = masterData.SEASON_ROMAN;
		var options = $("#season");

		$.each(season, function() {
			options.append( $( "<option />" ).val( this )
					.text( 'Season ' + this ) );
		});

		options.val( $("#season option:first").val() );
	}

	// Remove Illegal filename
//	function sanitizeFilename( filename ) {
//		var illegalFilename = masterData.ILLEGAL_FILENAME;
//		var newFilename = [];
//		for ( var i = 0; i < filename.length; i++ ) {
//			if ( $.inArray( filename[i].toUpperCase(), illegalFilename) == -1 ) {
//				newFilename.push( filename[i] );
//			}
//		}
//
//		return newFilename;
//	}

	// Capitalize every first letter of the string
	function ucFirstAllWords( str ) {
		var pieces = str.split(" ");
		for ( var i = 0; i < pieces.length; i++ ) {
			var j = pieces[i].charAt(0).toUpperCase();
			pieces[i] = j + pieces[i].substr(1);
		}
		console.log( 'pieces', pieces );
		return pieces.join(" ");
	}
	
	$('#btnConvert').on('click', function (e) {
		var filename    = $('#filename').val().split('.');
		var hasCheck    = $("#hasSeason").is(':checked') ? true : false;
		var season      = $('#season').val();

		var newFilename = "", title = "", description = "";

// 		filename.splice( 1, 1 );
		filename = sanitizeFilename( filename );

		for ( var i = 0; i < filename.length; i++ ) {
			if ( i == 0 ) {
				title = ucFirstAllWords( filename[i].toLowerCase() );
				console.log( 'title', title );
			} else if ( i == 1 ) {
				newFilename += ' ';
				newFilename += season + ' - ';

				if ( filename[i].indexOf("E") != -1 ){
					tempFilename = filename[i].split("E");
					newFilename += tempFilename[1];
				}else {
					newFilename += filename[i];
				}

			} else {
				if ( filename.length > 2 && i == 2 ) {
					description += ' ( ' + filename[i];
				} else if ( filename.length > 2 && i == filename.length-1 ) {
					description += ' ' + filename[i] + ' )';
				} else {
					description += ' ' + filename[i];
				}
			}
		}
		
		newFilename += ucFirstAllWords( description.toLowerCase() );
		
		newFilename = title + newFilename;
		
		$('#result').text( newFilename );
	} );
});