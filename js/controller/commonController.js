//--------------------------------------------------------------------
// DISABLE LOGGER
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
// COMMON CONTROLLER
//-------------------------------------------------------------------
var common = {
	/**
	 * LOAD JSON DATA
	 * @param url
	 * @param callback
	 */
	loadData: function( url, callback ) {
		$.ajax({
			'global': false,
			'url': url,
			'dataType': "json",
			'success': callback
		});
	},

	/**
	 * DISABLE CONSOLE LOG IF IT IS SET IN
	 * PRODUCTION ENVIRONMENT
	 */
	setLogger: function() {
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
		console.log( options );
		var posting = $.post( options.getURL(), options );
		
		return posting;
	},
	
	evCheckState: function() {
		$( 'input[name="chkFile"]' ).change(function() {
			( $(this).prop("checked") )?
			  $(this).parents( 'tr' ).addClass( 'selected' )
			: $(this).parents( 'tr' ).removeClass( 'selected' );
		});
	},
	
	evCheckAllState: function() {
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
	}
};