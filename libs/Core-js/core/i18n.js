
// var _t = {} is stated in every messages_xx_XX.js file.

var I18N = {};
$.extend ( I18N, {
	translate : function( msgid )
	{
		var msgstr = _t[msgid];
		if( typeof( msgstr ) == 'undefined' )
		{
			return msgid;
		}

		return msgstr;
	}
} );

_ = I18N.translate;
