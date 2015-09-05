/*********************************************************************************
 ** The contents of this file are subject to file_converter
 * Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: file_converter
 * The Initial Developer of the Original Code is Krishia Valencia.
 * All Rights Reserved.

 ********************************************************************************/

$( window ).load( function() {

	var initView = (function() {
		common.setLogger();

		// --------------------------------------------------------------------
		// EJS (VIEW)
		// --------------------------------------------------------------------
		var page = new EJS({url: 'js/view/indexView.ejs'}).render({
			headerView   : initHeaderView(),
			conditionView: initConditionHeader(),
			popupMsgView : initPopupMsgView()
		});
		
		function initHeaderView() {
			console.info( '-- headerView' );
			return new EJS({url: 'js/view/headerView.ejs'}).render({});
		}
		
		function initConditionHeader() {
			console.info( '-- initConditionHeader' );
			return new EJS({url: 'js/view/conditionView.ejs'}).render({});
		}
		
		function initPopupMsgView() {
			console.info( '-- popupMsgView' );
			return new EJS({url: 'js/view/popupMsgView.ejs'}).render({});
		}
		
		function showList( list ) {
			console.info( '-- showList' );
			return new EJS({url: 'js/view/listView.ejs'}).render({
				responseName : list.getResponseName(),
				list         : list.getData() });
		}
		
		$('body').append( page );
		$('#btnIndexView').click();

		// --------------------------------------------------------------------
		var list          = null;
		var popupBoxHeader= $( '#myPopupDialog h1' );
		var popupBoxMsg   = $( '#myPopupDialog p' );
		var popupBox      = $( '#myPopupDialog' );
		var titleInpt     = $( 'input[name="title"]' );
		var directoryInpt = $( 'input[name="directory"]' );
		var seasonCmb     = $( 'select[name="season"]' );
		var patternRad    = $( 'input[name="filePattern"]' );

		var defineData = (function() {
			console.info( '-- defineData' );
			common.loadData( "define.json", function( data ){
				populateSeasonList( data );
				common.renderSaveTitle( '#titleList' );
				common.renderSaveDirectory( '#dirList' );
			})
			return;
		})();
		
		/**
		 * ADD SEASON ON OPTIONS
		 * @param data
		 */
		function populateSeasonList( data ) {
			console.info( '-- populateSeasonList' );
			var season  = data.SEASON_ROMAN;
		
			$.each(season, function() {
				seasonCmb.append( $( "<option />" ).val( this )
					.text( 'Season ' + this ) );
			});
			seasonCmb[0].selectedIndex = 0;
			seasonCmb.selectmenu("refresh");
		}

		/**
		 * BUTTON CHECK AND RENAME CHANGE STATE
		 * @param defaultState
		 */
		function changeButtonState( defaultState ) {
			console.info( '-- changeButtonState' );
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
			console.info( '-- reset' );
			list = null;
			changeButtonState( true );
		}
		
		/**
		 * CLEAR / RESET VALUES
		 */
		function clear() {
			console.info( '-- clear' );
			directoryInpt.val( "" );
			titleInpt.val( "" );
			seasonCmb[0].selectedIndex = 0;
			seasonCmb.selectmenu("refresh");
		}
		
		/**
		 * SET POPUP BOX CONTENT
		 */
		function setPopupBox( msg, isError ) {
			console.info( '-- setPopupBox' );
			( isError )?
			  popupBoxHeader.text( "Error" )
			: popupBoxHeader.text( "Success" );
			popupBoxMsg.text( msg );
		}

		/**
		 * GET FILES
		 */
		function getFiles() {
			console.info( '-- getFiles' );
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
			options.setURL( "utils/process.php" );
			options.setData( data );
			
			common.saveTitle( data.title );
			common.saveDirectory( data.directory );
			common.renderSaveTitle( '#titleList' );
			common.renderSaveDirectory( '#dirList' );
			
			var posting = common.sendPost( options );

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
				
				$('#result').html( showList( list ) );

				common.evCheckAllState();
				common.evCheckAllInit();
				common.evCheckState();
				changeButtonState();
			});
		}
		
		function renameFiles() {
			console.info( '-- renameFiles' );
			var data = getSelectedData();
			
			var options = new OPTIONS( "renameFiles" );
			options.setURL( "utils/process.php" );
			options.setData( data );
			
			var posting = common.sendPost( options );
			
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
			console.info( '-- getSelectedData' );
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
		titleInpt.bind( "change", reset );
		directoryInpt.bind( "change", reset );
		seasonCmb.bind( "change", reset );
		patternRad.bind( "change", reset );

//		    $('#filterBasic-input input[data-type="search"]').on('keydown', function(e) {
//		        var code = (e.keyCode ? e.keyCode : e.which);
//		         if (code == 13) { //Enter keycode
//		             // this handles the enter key
//		         }
//		    });
//		    $("#filterBasic-input form").submit(function() {
//		        // this will handle both the enter key and go button on device
//		        alert('enter key or device go button pressed');
//		    });


		return;
	})();

});