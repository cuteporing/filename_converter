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

	var options = new OPTIONS( "aaa" );
	
	options.setURL('rename.php');
	options.setDirectory('C:\Users\USER\Videos\Vids\SERIES\LEVERAGE\Season 4');
	options.setTitle('III');
	
	console.log( 'options', options );
	
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


//	var posting = $.post( url, options );

	

});