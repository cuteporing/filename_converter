$( window ).load( function() {
	initView();

	var masterData    = null;
	var list          = null;
	var popupBoxHeader= $( '#myPopupDialog h1' );
	var popupBoxMsg   = $( '#myPopupDialog p' );
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
	 * SET POPUP BOX CONTENT
	 */
	function setPopupBox( msg, isError ) {
		( isError )?
		  popupBoxHeader.text( "Error" )
		: popupBoxHeader.text( "Success" );
		popupBoxMsg.text( msg );
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
			setPopupBox( "Select a filename pattern", true );
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
			
			// If there is an error
			if( list.getErrorCode() != "" ) {
				setPopupBox( list.getErrorMsg(), true );
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
			$( 'input[name="chkAllFile"]' ).change(function() {
				if( $(this).is(':checked') ) {
					$( 'input[name="chkFile"]' ).each(function(i,k){
						$(k).prop('checked',true).parents( 'tr' )
						.addClass( 'selected' );
					});
				} else {
					$( 'input[name="chkFile"]' ).each(function(i,k){
						$(k).prop('checked',false).parents( 'tr' )
						.removeClass( 'selected' );
					});
				}
			});
			$( 'input[name="chkFile"]' ).change(function() {
				( $(this).prop("checked") )?
				  $(this).parents( 'tr' ).addClass( 'selected' )
				: $(this).parents( 'tr' ).removeClass( 'selected' );
			});
		});
	}
	
	function renameFiles() {
		var data = getSelectedData();
		
		var options = new OPTIONS( "renameFiles" );
		options.setURL( "rename.php" );
		options.setData( data );
		
		var posting = sendPost( options );
		
		posting.done(function( data ) {
			list = new LIST( data );
			
			// If there is an error
			if( list.getErrorCode() != "" ) {
				setPopupBox( list.getErrorMsg(), true );
				popupBox.popup( "open" );
				return;
			}
			
			if ( list.getResponseName() == 'result' ) {
				var data = list.getData();
				setPopupBox( data.msg, false );
				popupBox.popup( "open" );
				changeButtonState( true );
				clear();
				$('#result').html( showList( list ) );
				return;
			}
		});
	}
	
	function getSelectedData() {
		var tempData = [];
		var data     = list.getData();
		$( 'input[name="chkFile"]' ).each(function(i,k){
			if(  $(k).is(':checked') ){
				tempData.push( data[$(k).attr( 'data-value' )] );
			}
		});
		
		return tempData;
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



