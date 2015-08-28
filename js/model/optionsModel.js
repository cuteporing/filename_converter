var OPTIONS = function ( responseName ) {

	this.responseName   = null;
	this.url            = null;
	this.data           = [];
		
	this.setResponseName( responseName );
};

// set URL
OPTIONS.prototype.setURL = function( url ) {
	this.url = url;
}
// set URL
OPTIONS.prototype.getURL = function( ) {
	return this.url;
}

// get responseName
OPTIONS.prototype.getResponseName = function() {
	return this.responseName;
}

// set responseName
OPTIONS.prototype.setResponseName = function( responseName ) {
	this.responseName = responseName;
}

// set directory
OPTIONS.prototype.setData = function( result ) {
	this.data = result;
}