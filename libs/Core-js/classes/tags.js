Core.classes.tags = {
	oDefault : {
		targetId: 'tags',
		addButton: true,
		addButtonClass: 'add_button',
		addButtonText: 'Add tag',
		autocomplete: false,
		data : null,
		autoOpen: true,
		autoOpenDelay: 100,
		resetButton: false,
		resetButtonClass: 'reset_button',
		resetButtonText: 'Reset tags',
		separator: ',', //The char used as separator for tags.
		items: [], // Any items that is to be added at the start.
		className: 'tagEditor', //Classname used on the tag list.
		confirmRemoval: false, //Set to true to have the user confirm removal of a tag.
		confirmRemovalText: 'Do you really wan to remove the tag?', //Set to a new string that will be displayed in the confirmation-popup when removing a tag.
		completeOnSeparator: false, //Set to true to parse tags as soon as a separator is added.
		completeOnBlur: false, //Set to true to parse tags as soon as the editor loses focus.
		initialParse: true,  //Set to false to stop the editor from parsing text that is already in the textbox when enabled.
		imageTag: false, //Optionally adds an image to the output.
		imageTagUrl: '', //The url of the optional image.
		continuousOutputBuild: true, //Set to true to continuously append data to the hidden field that is to be posted with the form.
		pathCss : ''
	}
};

Core.classes.tags.init = function (oOptions){

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
	if (oSettings.pathCss)	{
		$(document.body).append('<link rel="stylesheet" type="text/css" href="'+ oSettings.pathCss +'" />');
	}
	
	Cc.tags.load(oSettings);
	
};

Core.classes.tags.load = function (oSettings){

	var $Target = $(document.getElementById(oSettings.targetId));
	
	$Target.tagEditor(oSettings);

	//CREATE THE RESET BUTTON IF IS SET (DEFAULT TRUE)
	if (oSettings.resetButton)
	{

		var nResetId = 'reset_tags_' + oSettings.targetId;

		var oResetButton = document.createElement('a');
		oResetButton.setAttribute('href', '#');
		oResetButton.setAttribute('id', nResetId);
		oResetButton.setAttribute('class', oSettings.resetButtonClass );
		oResetButton.innerHTML = oSettings.resetButtonText;

		$Target.after(oResetButton);

		$(document.getElementById(nResetId)).click(function() {

			$Target.tagEditorResetTags();

			return false;

		});
	}

	//CREATE THE ADD BUTTON IF IS SET (DEFAULT TRUE)
	if (oSettings.addButton)
	{
		var nAddId = 'add_tags_' + oSettings.targetId;
		var oAddButton = document.createElement('a');
		oAddButton.setAttribute('href', '#');
		oAddButton.setAttribute('id', nAddId);
		oAddButton.setAttribute('class', oSettings.addButtonClass );
		oAddButton.innerHTML = oSettings.addButtonText;

		$Target.after(oAddButton);

		$(document.getElementById(nAddId)).click(function() {

			$Target.trigger({
				type: 'keypress',
				which: 13
			});

			return false;
			
		});
	}
	if (oSettings.autocomplete ) {
		if (Core.classes.autocomplete.init) {
			Core.classes.autocomplete.init(oSettings);
		}
		else {
			$LAB.script(Core.modules.autocomplete).wait(function(){
				Core.classes.autocomplete.init(oSettings);
			});
			Core.classes.autocomplete.init(oSettings);
		}
	}


};