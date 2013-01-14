// Set constant classes.
var PullDownClasses =
{
	sPullDownClass:		'pulldown',
	sJsPullDownClass:	'js-pulldown',
	sOpenClass:			'open',
	sActiveClass:		'active'
};

/**
 * Generic PullDown.
 * @class PullDown
 * @author Francisco Ruiz Lloret
 * @version 1.0
 */
var PullDown = function()
{
	/**
	 * Container ID.
	 * @member PullDown.prototype
	 * @type String
	 */
	this.sContainerId = '';

	/**
	 * Target HTML tag by default.
	 * @member PullDown.prototype
	 * @type String
	 */
	this.sTargetTag = 'span';

	/**
	 * Menu HTML tag by default.
	 * @member PullDown.prototype
	 * @type String
	 */
	this.sMenuTag = 'ul';

	/**
	 * Menu item HTML tag by default.
	 * @member PullDown.prototype
	 * @type String
	 */
	this.sMenuItemTag = 'li';

	/**
	 * Container DOM element.
	 * @member PullDown.prototype
	 * @type Object
	 */
	this.oContainer = null;

	/**
	 * Target DOM element.
	 * @member PullDown.prototype
	 * @type Object
	 */
	this.oTarget = null;

	/**
	 * Menu DOM element.
	 * @member PullDown.prototype
	 * @type Object
	 */
	this.oMenu = null;

	/**
	 * Active item DOM element.
	 * @member PullDown.prototype
	 * @type Object
	 */
	this.oActive = null;
};

/**
 * Set the container ID of the pull-down.
 * @member PullDown.prototype
 * @param {String} sContainerId
 * @return this
 * @type Object
 */
PullDown.prototype.setContainerId = function(sContainerId)
{
	this.sContainerId = sContainerId;
	return this;
};

/**
 * Toggle pull-down.
 * @member PullDown.prototype
 * @param {Boolean} bShow
 */
PullDown.prototype.toggle = function(bShow)
{
	if (bShow)
	{
		this.oMenu.style.display = 'block';
		if (this.oTarget.className.indexOf(PullDownClasses.sOpenClass) === -1)
		{
			this.oTarget.className += ' ' + PullDownClasses.sOpenClass;
		}
	}
	else
	{
		this.oMenu.style.display = 'none';
		this.oTarget.className = this.oTarget.className.replace(PullDownClasses.sOpenClass, '');
	}
};

/**
 * Function passed to the click event on the target.
 * @member PullDown.prototype
 * @return false
 * @type Boolean
 */
PullDown.prototype.fpTargetClick = function(eEvent)
{
	if (this.oMenu.style.display === 'block')
	{
		this.toggle(false);
	}
	else
	{
		this.toggle(true);
	}

	eEvent.preventDefault();
};

/**
 * Function passed to the click event on any element out of the target.
 * @member PullDown.prototype
 */
PullDown.prototype.fpOutOfTargetClick = function(oEvent)
{
	var oElement = oEvent.target,
		sId = this.sContainerId;

	while ( oElement && oElement !== document && oElement.id !== sId )
	{
		oElement = oElement.parentNode;
	}

	if (oElement.id !== this.sContainerId && this.oMenu.style.display === 'block')
	{
		this.toggle(false);
	}

	oElement = null;
};

/**
 * Set the behaviours of DOM elements.
 * @member PullDown.prototype
 */
PullDown.prototype.setBehaviours = function()
{
	$(this.oTarget).bind('click', $.proxy(this.fpTargetClick, this));
	$(document).bind('click', $.proxy(this.fpOutOfTargetClick, this));
};

/**
 * Initializer method.
 * @member PullDown.prototype
 */
PullDown.prototype.init = function()
{
	this.oContainer = document.getElementById(this.sContainerId);

	if (this.oContainer)
	{
		// Init DOM selections.
		this.oTarget	= this.oContainer.getElementsByTagName(this.sTargetTag)[0];
		this.oMenu		= this.oContainer.getElementsByTagName(this.sMenuTag)[0];
		this.oActive	= $(this.oMenu.getElementsByTagName(this.sMenuItemTag)).filter('.' + PullDownClasses.sActiveClass)[0];

		if (this.oContainer.className.indexOf(PullDownClasses.sJsPullDownClass) !== -1)
		{
			this.oContainer.className += ' ' + PullDownClasses.sPullDownClass;
			this.oContainer.className = this.oContainer.className.replace(PullDownClasses.sJsPullDownClass, '');
		}

		// Hide the pull-down menu by default.
		this.oMenu.style.display = 'none';

		if (this.oActive)
		{
			this.oTarget.innerHTML = this.oActive.innerHTML;
		}

		this.setBehaviours();
	}
};