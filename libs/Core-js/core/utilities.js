// GLOBAL VARIABLES
CORE.globals.isIE6 = false /*@cc_on || @_jscript_version < 5.7 @*/;
CORE.globals.isMSIE = /*@cc_on!@*/false;

// COMMON FUNCTIONS
CORE.utilities.example = function()
{

};
/**
 * Return the time since in the format "X time ago" for a given date string
 * @param {String} sDate The date in MySql format yyyy-mm-dd hh:mm:ss
 * @return The time since
 * @type String
 */
CORE.utilities.getTimeSince = function (sDate) {

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

CORE.utilities.deleteById = function(sTargetId) {
	var oTarget = document.getElementById(sTargetId);
	var oContainer = null;
	if (oTarget){
		oContainer = oTarget.parentNode;
		oContainer.removeChild(oTarget);
	}
};

CORE.utilities.getUrlContent = function(sUrl, fpCallback) {
	var aUrl = sUrl.split('#');
	var sContainerUrl = aUrl[0] + '';
	var sContainerId = aUrl[1] + '';
	var oContainer = null;
	var sTempContainerId = 'getUrlContent';
	var oBody = null;

	if ( document.getElementById(sContainerId) == null){
		$.get(
			sContainerUrl,
			function(sHtml) {

				oBody = document.createElement('div');
				oBody.id = sTempContainerId;
				oBody.innerHTML = sHtml;
				oBody.style.display = 'none';
				document.body.appendChild(oBody);

				oContainer = document.getElementById(sContainerId) ? document.getElementById(sContainerId) : null;

				CORE.utilities.deleteById(sTempContainerId);

				if (oContainer)
				{
					fpCallback( oContainer );
				}
				else
				{
					return false;
				}
			}
		);
	}
};

CORE.utilities.placeUrlContent = function(sUrl, sTargetId, fpCallback) {
	var aUrl = sUrl.split('#');
	var sContainerUrl = aUrl[0] + '';
	var sContainerId = aUrl[1] + '';
	var oContainer = null;
	var sTempContainerId = 'getUrlContent';
	var oBody = null;
	var oTarget = document.getElementById(sTargetId);

	if (oTarget && document.getElementById(sContainerId) == null){
		$.get(
			sContainerUrl,
			function(sHtml) {

				oBody = document.createElement('div');
				oBody.id = sTempContainerId;
				oBody.innerHTML = sHtml;
				oBody.style.display = 'none';
				document.body.appendChild(oBody);

				oContainer = document.getElementById(sContainerId) ? document.getElementById(sContainerId) : null;

				CORE.utilities.deleteById(sTempContainerId);

				if (oContainer)
				{
					oTarget.appendChild(oContainer);
					fpCallback();
				}
				else
				{
					return false;
				}
			}
		);
	}
};

/**
 * Launch one function when doing scroll and the targe references are visible
 *
 * @param aReferences Array of elements to check if are visible
 * @param fpCallback Function to execute when the elements are visible
 */
CORE.utilities.launchCallbackOnScroll = function (aReferences , fpCallback) {
	$(window).scroll(function()
	{
		var nScrollTop = $(window).scrollTop();
		var nViewPort = $(window).height();
		var nPosTopReference = 0;
		var aReferences = aReferences ? aReferences : [];
		var $oReference = null;
		var sReference = null;
		var sKey = '';

		for(sKey in aReferences)
		{
			if(aReferences.hasOwnProperty(sKey))
			{
				sReference = aReferences[sKey];
				$oReference = $(sReference);
				if($oReference.length)
				{
					nPosTopReference = $oReference.offset().top;
					break;
				}
			}
		}
		sReference = $oReference = null;
		if(nScrollTop+nViewPort >= nPosTopReference)
		{
			fpCallback();
			$(window).unbind("scroll");
		}
	});
};

CORE.utilities.isVisible = function(oElement) {

    var nTop = oElement.offsetTop;
    var nLeft = oElement.offsetLeft;
    var nWidth = oElement.offsetWidth;
    var nHeight = oElement.offsetHeight;

    while(oElement.offsetParent) {
        oElement = oElement.offsetParent;
        nTop += oElement.offsetTop;
        nLeft += oElement.offsetLeft;
    }

    return (
        nTop < (window.pageYOffset + window.innerHeight) &&
        nLeft < (window.pageXOffset + window.innerWidth) &&
        (nTop + nHeight) > window.pageYOffset &&
        (nLeft + nWidth) > window.pageXOffset
    );
};

// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function noop() {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

/* COMMON FUNCTIONS */

// Return true if string is an email, else return false.
CORE.utilities.IsEmail = function(sMail) {
    var sAt = "@";
    var sDot = ".";
    var sLat = sMail.indexOf(sAt);
    var sLstr = sMail.length;
    var sLdot = sMail.indexOf(sDot);

    if ( sLat == -1) {
        return false;
    } else if ( sLat == -1 || sLat ==0 || sLat == sLstr ) {
        return false;
    } else if ( sLdot == -1 || sLdot == 0 || sLdot == sLstr) {
        return false;
    } else if ( sMail.indexOf(sAt,( sLat + 1 ) )!=-1) {
        return false;
    } else if ( sMail.substring(sLat-1,sLat) == sDot || sMail.substring(sLat+1,sLat+2) == sDot ) {
        return false;
    } else if ( sMail.indexOf(sDot,(sLat+2)) == -1) {
        return false;
    } else if ( sMail.indexOf(" ") != -1) {
        return false;
    } else {
        return true;
    }
};