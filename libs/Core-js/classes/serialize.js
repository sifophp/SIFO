CORE.classes.serialize = {
	oDefault : {
		target: '#serialize',
		attribute : 'id',
		itemClass : '.serialize'
	}
};

CORE.classes.serialize.init = function (oOptions){

	var Cm = CORE.modules; 
	var Cc = CORE.classes;
	var Cg = CORE.globals;

	var oSettings = this.oDefault;
	var key = '';

	for ( key in oOptions )	{
            if(oOptions.hasOwnProperty(key)) {
                oSettings[key] = oOptions[key];
            }
	}

	CORE.classes.sortable.load(oSettings);
	
};

CORE.classes.serialize.load = function (oSettings){
	var oSerialize = $(oSettings.target).serializeTree(oSettings.attribute,oSettings.itemClass);
	return oSerialize;
};

