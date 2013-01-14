$LAB.setOptions(
{
	AlwaysPreserveOrder: false,
	UsePreloading: true,
	UseLocalXHR: true,
	UseCachePreload: true,
	AllowDuplicates: false,
	AppendTo: "head",
	BasePath: ""
});

CORE.behaviour.modules.isset = function( sId ) {

};


$(document).ready(function() {
	var sId = '',
		oModules = document.querySelectorAll("[id]"),
		sLoadedModules = 'LAUNCH MODULES: ';

	// polyfill for querySelector
	if (!document.querySelectorAll) {
		document.querySelectorAll = function(selector) {
	        var doc = document,
	            head = doc.documentElement.firstChild,
	            styleTag = doc.createElement('STYLE');
	        head.appendChild(styleTag);
	        doc.__qsaels = [];

	        styleTag.styleSheet.cssText = selector + "{x:expression(document.__qsaels.push(this))}";
	        window.scrollBy(0, 0);

	        return doc.__qsaels;
	    }
	}

	// To Execute behaviours based on modular elements

	for ( var nCounter = 0; nCounter < oModules.length; nCounter++  ) {
		sId = oModules[nCounter].id;

		if ( CORE.behaviour.modules[ sId ] !== undefined )
		{
			CORE.behaviour.modules[ sId ].init();
			sLoadedModules += sId + ' | ';
		}
	}

	if ( console ) {
		console.log( sLoadedModules + '\n' + 'TOTAL ID ELEMENTS:' + nCounter );
	}

	// Always execute the common behaviour (if exist)
	if ( CORE.behaviour.common !== undefined ) {
		CORE.behaviour.common();
	}

	// To execute behaviours based on pages
	if (typeof  CORE.behaviour.page[document.body.id] != "undefined")
	{
        CP[document.body.id]();
	} else {
		CORE.behaviour.page.unset();
	}


});