$( window ).load( function() {
	initView();

	var masterData    = null;
	var list          = null;
	var popupHeader   = $( '#myPopupDialog h1' );
	var popupMsg      = $( '#myPopupDialog p' );
	var popupBox      = $( '#myPopupDialog' );
	var titleInpt     = $( '#title' );
	var directoryInpt = $( '#directory' );
	var seasonCmb     = $( '#season' );

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
	
		$.each(season, function() {
			seasonCmb.append( $( "<option />" ).val( this )
				.text( 'Season ' + this ) );
		});
		seasonCmb[0].selectedIndex = 0;
		seasonCmb.selectmenu("refresh");
	}
	
	function sendPost( options ) {
		console.log( 'options', options );
		var posting = $.post( options.getURL(), options );
		
		return posting;
	}
	
	function changeButtonState( defaultState ) {
		if ( defaultState ) {
			$('#btnCheck').removeClass( "hide" );
			$('#btnRename').addClass( "hide" );
		} else {
			$('#btnRename').removeClass( "hide" );
			$('#btnCheck').addClass( "hide" );
		}
	}
	
	/**
	 * RESET LIST DATA AND CHANGE BUTTON STATE TO DEFAULT
	 */
	function reset() {
		list = null;
		changeButtonState( true );
	}
	
	/**
	 * CLEAR / RESET VALUES
	 */
	function clear() {
		directoryInpt.val( "" );
		titleInpt.val( "" );
		seasonCmb[0].selectedIndex = 0;
		seasonCmb.selectmenu("refresh");
	}

	/**
	 * GET FILES
	 */
	function getFiles() {
		var data    = {};
		var posting = null;
		var pattern = "";
		
		if( $( 'input[name="filePattern"]:checked' ).length > 0 ) {
			pattern = $( 'input[name="filePattern"]:checked' ).attr( 'id' );
		}else{
			popupHeader.text( "Error" );
			popupMsg.text( "Select a filename pattern" );
			popupBox.bind({
				popupafterclose: function(event, ui) { 
					$( 'input[name="filePattern"]:first' ).focus();
				}
			});
			popupBox.popup( "open" );
			
			return;
		}

		data.directory = directoryInpt.val();
		data.title     = titleInpt.val();
		data.season    = seasonCmb.val();
		data.pattern   = pattern;
		
		var options = new OPTIONS( "getFiles" );
		options.setURL( "rename.php" );
		options.setData( data );
		
		var posting = sendPost( options );

		posting.done(function( data ) {
			list = new LIST( data );
			console.log( 'getFiles', list );
			
			// If there is an error
			if( list.getErrorCode() != "" ) {
				popupHeader.text( "Error" );
				popupMsg.text( list.getErrorMsg() );
				if( list.getErrorCode() == '0002' ){
					popupBox.bind({
						popupafterclose: function(event, ui) { 
							directoryInpt.focus();
						}
					});
				}
				popupBox.popup( "open" );
				return;
			}
			
			changeButtonState();
			$('#result').html( showList( list ) );
			$( 'input[name="chkFile"]' ).change(function() {
				( $(this).prop("checked") )?
				  $(this).parents( 'tr' ).addClass( 'selected' )
				: $(this).parents( 'tr' ).removeClass( 'selected' );
			});
		});
	}
	
	function renameFiles() {
		var data = list.getData();
		
		var options = new OPTIONS( "renameFiles" );
		options.setURL( "rename.php" );
		options.setData( data );
		
		var posting = sendPost( options );
		
		posting.done(function( data ) {
			list = new LIST( data );
			console.log( 'renameFiles', list );
			
			// If there is an error
			if( list.getErrorCode() != "" ) {
				popupHeader.text( "Error" );
				popupMsg.text( list.getErrorMsg() );
				popupBox.popup( "open" );
				return;
			}
			
			if ( list.getResponseName() == 'result' ) {
				var data = list.getData();
				popupHeader.text( "Success" );
				popupMsg.text( data.msg );
				popupBox.popup( "open" );
				changeButtonState( true );
				clear();
				$('#result').html( showList( list ) );
				return;
			}
		});
	}
	
	// --------------------------------------------------------------------
	// EVENTS
	// --------------------------------------------------------------------
	$( "#btnCheck" ).bind( "click", getFiles );
	$( "#btnRename" ).bind( "click", renameFiles );
	$( '#title, #season, #directory, input[name="filePattern"]' ).bind(
		"change", reset );

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
		return new EJS({url: 'js/view/listView.ejs'}).render({
			responseName : list.getResponseName(),
			list         : list.getData() });
	}
});



