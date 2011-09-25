Core.classes.tabs = {
	oDefault : {
		targetClass: 'tabber',
		classLive : 'tabberlive', // Rename classMain to classMainLive after tabifying  (so a different style can be applied)
		classTab : 'tabbertab', // Class of each DIV that contains a tab
		classDefault: 'tabbertabdefault', //Class to indicate which tab should be active on startup
		classNav: 'tabbernav', // Class for the navigation UL
		classHide: 'tabbertabhide', // When a tab is to be hidden
		classActive: 'tabberactive', // Class to set the navigation LI when the tab is active
		title: ['h2','h3','h4','h5','h6'], // Elements that might contain the title for the tab, only used if a title is not specified in the TITLE attribute of DIV classTab
		titleHTML: true, // Should we strip out the HTML from the innerHTML of the title elements? This should usually be true
		removeTitle : true, // If the user specified the tab names using a TITLE attribute on the DIV, then the browser will display a tooltip whenever the mouse is over the DIV
		addLinkId : false, // If you want to add an id to each link set this to true
		pathCss : ''
	}
};

Core.classes.tabs.init = function (oOptions){

	var Cm = Core.modules; 
	var Cc = Core.classes;
	var Cg = Core.globals;

	var oSettings = this.oDefault;
	var key = '';

	for ( key in oOptions )	{
            if(oOptions.hasOwnProperty(key)) {
                oSettings[key] = oOptions[key];
            }
	}

	// Load the CSS
	if (oSettings.pathCss)
	{
		$(document.body).append('<link rel="stylesheet" type="text/css" href="'+ oSettings.pathCss +'" />');	
	}

	Core.classes.tabs.load(oSettings);
	
};

Core.classes.tabs.load = function (oSettings){


	tabberAutomatic({
		classMain: oSettings.targetClass,
		classMainLive : oSettings.classLive, // Rename classMain to classMainLive after tabifying  (so a different style can be applied)
		classTab : oSettings.classTab, // Class of each DIV that contains a tab
		classTabDefault: oSettings.classDefault, //Class to indicate which tab should be active on startup
		classNav: oSettings.classNav, // Class for the navigation UL
		classTabHide: oSettings.classHide, // When a tab is to be hidden
		classNavActive: oSettings.classActive, // Class to set the navigation LI when the tab is active
		titleElements: oSettings.title, // Elements that might contain the title for the tab, only used if a title is not specified in the TITLE attribute of DIV classTab
		titleElementsStripHTML: oSettings.titleHTML, // Should we strip out the HTML from the innerHTML of the title elements? This should usually be true
		removeTitle : true, // If the user specified the tab names using a TITLE attribute on the DIV, then the browser will display a tooltip whenever the mouse is over the DIV
		addLinkId : false // If you want to add an id to each link set this to true
	});


};

