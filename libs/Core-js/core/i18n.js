
var _t = _t ? _t : {};

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
