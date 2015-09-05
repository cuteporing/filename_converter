/*********************************************************************************
 ** The contents of this file are subject to file_converter
 * Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: file_converter
 * The Initial Developer of the Original Code is Krishia Valencia.
 * All Rights Reserved.

 ********************************************************************************/

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