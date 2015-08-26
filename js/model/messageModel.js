
var OPTIONS = function ( responseName ) {
	this.responseName = null;
	this.url          = null;
	this.data         = [];
	
	this.setResponseName( responseName );
};

OPTIONS.prototype.getResponseName = function() {
	return this.responseName;
}

OPTIONS.prototype.getTitle = function() {
	return this.title;
}

OPTIONS.prototype.getSeason = function() {
	return this.season;
}

OPTIONS.prototype.setResponseName = function( responseName ) {
	this.responseName = responseName;
}

OPTIONS.prototype.setURL = function( url ) {
	this.url = url;
}

OPTIONS.prototype.setDirectory = function( directory ) {
	this.data.directory = directory;
}

OPTIONS.prototype.setTitle = function( title ) {
	this.data.title = title;
}

OPTIONS.prototype.setSeason = function( season ) {
	this.data.season = season;
}

