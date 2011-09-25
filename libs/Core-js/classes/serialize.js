Core.classes.serialize = {
	oDefault : {
		target: '#serialize',
		attribute : 'id',
		itemClass : '.serialize'
	}
};

Core.classes.serialize.init = function (oOptions){

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

	Core.classes.sortable.load(oSettings);
	
};

Core.classes.serialize.load = function (oSettings){
	var oSerialize = $(oSettings.target).serializeTree(oSettings.attribute,oSettings.itemClass);
	return oSerialize;
};

