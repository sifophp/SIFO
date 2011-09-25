// GLOBAL VARIABLES
Core.globals.isIE6 = false /*@cc_on || @_jscript_version < 5.7 @*/;
Core.globals.isMSIE = /*@cc_on!@*/false;

// COMMON FUNCTIONS
Core.utilities.example = function()
{
	
};
/**
 * Return the time since in the format "X time ago" for a given date string
 * @param {String} sDate The date in MySql format yyyy-mm-dd hh:mm:ss
 * @return The time since
 * @type String
 */
Core.utilities.getTimeSince = function (sDate) {

	var nParsedDate = Date.parse(sDate.replace(/-/g, " "));
	var nSeconds = -1;
        var nInterval = -1;

	if(isNaN(nParsedDate) === false)
	{
		nSeconds = Math.floor((new Date() - nParsedDate) / 1000);

		// x years ago.
		nInterval = Math.floor(nSeconds / 31536000);
		if (nInterval > 1) {
			//return Namespace.utilities.i18n.getText("%1% years ago").replace("%1%", nInterval);
                       return (nInterval + " years ago");
		}

		// x month ago.
		nInterval = Math.floor(nSeconds / 2592000);
		if (nInterval > 1) {
			//return Namespace.utilities.i18n.getText("%1% months ago").replace("%1%", nInterval);
                        return (nInterval + " months ago");
		}

		// x days ago.
		nInterval = Math.floor(nSeconds / 86400);
		if (nInterval > 1) {
			//return Namespace.utilities.i18n.getText("%1% days ago").replace("%1%", nInterval);
                        return (nInterval + " days ago");
		}

		// x hours ago
		nInterval = Math.floor(nSeconds / 3600);
		if (nInterval > 1) {
			//return Namespace.utilities.i18n.getText("%1% hours ago").replace("%1%", nInterval);
                        return (nInterval + " hours ago");
		}

		// x minutes ago
		nInterval = Math.floor(nSeconds / 60);
		if (nInterval > 1) {
			//return Namespace.utilities.i18n.getText("%1% minutes ago").replace("%1%", nInterval);
                        return (nInterval + " minutes ago");
		}

		// x seconds ago
		//return Namespace.utilities.i18n.getText("%1% seconds ago").replace("%1%", nInterval);
                return (nInterval + " seconds ago");
	}
	else
	{
		return sDate;
	}
};