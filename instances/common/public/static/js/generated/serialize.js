/* BEGIN jquery_serialize_tree */

/**
 * @author Ralph Voigt (info -at- drakedata.com)
 * @version 1.1
 * @date 29.01.2010
 *
 * @name serializeTree
 * @type jQuery
 * @homepage http://plugins.jquery.com/project/serializeTree/
 * @desc Recursive function to serialize ordered or unordered lists of arbitrary depth and complexity. The resulting array will keep the order of the tree and is suitable to be posted away.
 * @example $("#myltree").serializeTree("id","myArray",".elementsToExclude")
 * @param String attribute The attribute of the li-elements to be serialised
 * @param String levelString The Array to store data in
 * @param String exclude li-Elements to exclude from being serialised (optional)
 * @return String The array to be sent to the server via post
 *          Boolean false if the passed variable is not a list or empty
 * @cat Plugin
 */
 
(function( $ ){
	jQuery.fn.serializeTree = function (attribute, levelString, exclude) {
		var dataString = '';
		var elems;
		if (exclude==undefined) elems = this.children();
		else elems = this.children().not(exclude);
		if( elems.length > 0) {
			elems.each(function() {
				var curLi = $(this);
				var toAdd = '';
				if( curLi.find('ul').length > 0) {
					levelString += '['+curLi.attr(attribute)+']';
					toAdd = $('ul:first', curLi).serializeTree(attribute, levelString, exclude);
					levelString = levelString.replace(/\[[^\]\[]*\]$/, '');
				} else if( curLi.find('ol').length > 0) {
					levelString += '['+curLi.attr(attribute)+']';
					toAdd = $('ol:first', curLi).serializeTree(attribute, levelString, exclude);
					levelString = levelString.replace(/\[[^\]\[]*\]$/, '');
				} else {
					dataString += '&'+levelString+'[]='+curLi.attr(attribute);
				}
				if(toAdd) dataString += toAdd;
			});
		} else {
			dataString += '&'+levelString+'['+this.attr(attribute)+']=';
		}
		if(dataString) return dataString;
		else return false;
	};
})( jQuery );


/* END jquery_serialize_tree *//* BEGIN serialize_class */

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



/* END serialize_class */