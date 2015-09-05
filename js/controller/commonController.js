/*********************************************************************************
 ** The contents of this file are subject to file_converter
 * Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: file_converter
 * The Initial Developer of the Original Code is Krishia Valencia.
 * All Rights Reserved.

 ********************************************************************************/

//--------------------------------------------------------------------
// ENABLE/DISABLE CONSOLE LOG
// -------------------------------------------------------------------
var logger = function()
{
	var oldConsoleLog = null;
	var pub = {};

	pub.enableLogger =  function enableLogger() 
		{
			if(oldConsoleLog == null)
				return;

			window['console']['log'] = oldConsoleLog;
		};

	pub.disableLogger = function disableLogger()
		{
			oldConsoleLog = console.log;
			window['console']['log'] = function() {};
		};

	return pub;
}();

//--------------------------------------------------------------------
// AUTO COMPLETE LIST EVENT
//-------------------------------------------------------------------
var autoCompleteList = {
	
	init: function( selector ) {
		console.info( '-- autoCompleteList init' );
		this.evSelectList( selector );
		this.evInptDblClick( selector );
		this.evOutsideClick( selector );
	},

	evSelectList: function( selector ) {
		console.info( '-- evSelectList' );
		var self = this;
		$( document ).on( "click", selector + " li", function() {
			var inpt = $( this ).parent().attr( 'data-input' );
			var selectedItem = $( this ).html();

			$( inpt ).val( selectedItem );
			$("ul:jqmData(role='listview')").children()
				.addClass('ui-screen-hidden');
		});
	},
	
	evInptDblClick: function( selector ) {
		var inpt = $( selector ).attr( 'data-input' );
		
		$( inpt ).dblclick(function() {
			$( selector + " li" ).removeClass( 'ui-screen-hidden' );
		});
	},
	
	evOutsideClick: function( selector ) {
		var inpt = $( selector ).attr( 'data-input' );
		$( ":not("+ selector +" li)" ).click(function(){
			$("ul:jqmData(role='listview')").children()
			.addClass('ui-screen-hidden');
		})
	}
}

//--------------------------------------------------------------------
// COMMON CONTROLLER
//-------------------------------------------------------------------
var common = {
	/**
	 * LOAD JSON DATA
	 * @param url
	 * @param callback
	 */
	loadData: function( url, callback ) {
		console.info( '-- loadData' );
		var isSuccess = false;

		$.ajax({
			'url': url,
			'dataType': "json",
			'success': callback,
			'error': function(xhr, status, error){
				console.error( xhr.status );
			}
		});
	},

	/**
	 * DISABLE CONSOLE LOG IF IT IS SET IN
	 * PRODUCTION ENVIRONMENT
	 */
	setLogger: function() {
		console.info( '-- setLogger' );
		this.loadData( "utils/config.php", function( data ){
			( data.ENVIRONMENT == "Development" )?
				logger.enableLogger() : logger.disableLogger();
		})
	},

	/**
	 * SEND POST
	 * @param options
	 */
	sendPost: function( options ) {
		console.info( '-- sendPost' );
		console.log( options );
		var posting = $.post( options.getURL(), options );
		
		return posting;
	},
	
	evCheckState: function() {
		console.info( '-- evCheckState' );
		$( 'input[name="chkFile"]' ).change(function() {
			( $(this).prop("checked") )?
			  $(this).parents( 'tr' ).addClass( 'selected' )
			: $(this).parents( 'tr' ).removeClass( 'selected' );
		});
	},

	/**
	 * CHECKBOX ALL INITIAL DISPLAY
	 */
	evCheckAllInit: function() {
		console.info( '-- evCheckAllInit' );
		if ( $('input[name="chkFile"]:checked').length > 0 ) {
			$( 'input[name="chkAllFile"]' ).prop( "checked", true );
		} else {
			$( 'input[name="chkAllFile"]' ).prop( "checked", false )
		}
	},
	
	/**
	 * CHECK ALL STATE EVENT
	 */
	evCheckAllState: function() {
		console.info( '-- evCheckAllState' );
		$( 'input[name="chkAllFile"]' ).change(function() {
			if( $(this).is(':checked') ) {
				$( 'input[name="chkFile"]' ).each(function(i,k){
					if( ! $(k).attr( "disabled" ) ){
						$(k).prop('checked',true).parents( 'tr' )
						.addClass( 'selected' );
					}
				});
			} else {
				$( 'input[name="chkFile"]' ).each(function(i,k){
					$(k).prop('checked',false).parents( 'tr' )
					.removeClass( 'selected' );
				});
			}
		});
	},
	
	/**
	 * GET DIRECTORY LIST FROM LOCALSTORAGE
	 * @param id
	 */
	getSavedInfo: function( id ) {
		console.info( '-- getSaveDirectory' );
		var LIST = localStorage.getItem( id );
		
		(typeof LIST === 'undefined' || LIST === null)?
				LIST = [] : LIST = JSON.parse( LIST );

		return LIST;
	},
	
	/**
	 * RENDER LIST VIEW
	 * @param selector
	 * @param data
	 */
	renderListView: function( selector, data ) {
		$( selector ).empty();
		$.each( data , function() {
			$( selector ).append( "<li>" + this + "</li>" );
		});
		$( selector ).listview("refresh");

		$("ul:jqmData(role='listview')").children()
		.addClass('ui-screen-hidden');
		
	},
	
	/**
	 * RENDER SAVED DIRECTORY INTO AUTO-COMPLETE LIST
	 * @param selector
	 */
	renderSaveDirectory: function( selector ) {
		console.info( '-- renderSaveDirectory' );
		var DIR_LIST = this.getSavedInfo( 'DIR_LIST' );

		this.renderListView( selector, DIR_LIST );
		autoCompleteList.init( selector );
	},
	
	/**
	 * RENDER SAVED TITLE INTO AUTO-COMPLETE LIST
	 * @param selector
	 */
	renderSaveTitle: function( selector ) {
		console.info( '-- renderSaveTitle' );
		var TITLE_LIST = this.getSavedInfo( 'TITLE_LIST' );

		this.renderListView( selector, TITLE_LIST );
		autoCompleteList.init( selector );
	},
	
	/**
	 * SAVE SEARCH DIRECTORY TO LOCAL STORAGE
	 * @param directory
	 */
	saveDirectory: function( directory ) {
		console.info( '-- saveDirectory' );
		var DIR_LIST = this.getSavedInfo( 'DIR_LIST' );
		
		directory = directory.replace(/\s\s+/g, ' ');

		if( directory != "" && jQuery.inArray( directory, DIR_LIST ) == -1 )
			DIR_LIST.push( directory );

		localStorage['DIR_LIST']=JSON.stringify( DIR_LIST );
	},
	
	/**
	 * SAVE SEARCH TITLE TO LOCAL STORAGE
	 * @param title
	 */
	saveTitle: function( title ) {
		console.info( '-- saveTitle' );
		var TITLE_LIST = this.getSavedInfo( 'TITLE_LIST' );
		
		title = title.replace(/\s\s+/g, ' ');

		if( title != "" && jQuery.inArray( title, TITLE_LIST ) == -1 )
			TITLE_LIST.push( title );

		localStorage['TITLE_LIST']=JSON.stringify( TITLE_LIST );
	}
};