$( window ).load( function() {
	initView();

	var masterData = null;
	var list       = null;

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
	
	/**
	 * ADD SEASON ON OPTIONS
	 */
	function populateSeasonList() {
		var season  = masterData.SEASON_ROMAN;
		var combo = $("#season");
	
		$.each(season, function() {
			combo.append( $( "<option />" ).val( this )
				.text( 'Season ' + this ) );
		});
		combo.val( $("#season option:first").val() );
	}
	
	$( '#btnCheck' ).click(function(e) {
		getFiles();
	})

	/**
	 * GET FILES
	 */
	function getFiles() {
		var data = {};

		data.directory = $( '#directory' ).val();
		data.title     = $( '#title' ).val();
		data.season    = $( '#season' ).val();
		
		var options = new OPTIONS( "getFiles" );
		options.setURL( "rename.php" );
		options.setData( data );
		
		var posting = $.post( options.getURL(), options );
		
		posting.done(function( data ) {
			list = new LIST( data );
			console.log( 'getFiles', list );
			
			// If there is an error
			if( list.getErrorCode() != "" ) {
				$( '#myPopupDialog h1' ).text( "Error" );
				$( '#myPopupDialog p' ).text( list.getErrorMsg() );
				$( '#myPopupDialog' ).popup( "open" );
				return;
			}
			
			$('#btnCheck').attr("id", "btnConvert").text("Convert");
			$('#result').html( showList( list ) );
		});
	}
	// --------------------------------------------------------------------
	// EJS (VIEW)
	// --------------------------------------------------------------------
	function initView() {
		var page = new EJS({url: 'js/view/indexView.ejs'}).render({
				conditionView: initConditionHeader(),
				popupMsgView : popupMsgView()
		});

		$('body').append( page );
		$('#btnIndexView').click();
	}

	function initConditionHeader() {
		return new EJS({url: 'js/view/conditionView.ejs'}).render({});
	}
	
	function popupMsgView() {
		return new EJS({url: 'js/view/popupMsgView.ejs'}).render({});
	}
	
	function showList( list ) {
		return new EJS({url: 'js/view/listView.ejs'}).render({ list: list.getData() });
	}
});



