// CSS3MultiColumn - a javascript implementation of the CSS3 multi-column module
// v1.02 beta - Jan 08 2008
// Copyright (c) 2005 Cdric Savarese <pro@4213miles.com>
// This software is licensed under the CC-GNU LGPL <http://creativecommons.org/licenses/LGPL/2.1/>

// For additional information, see : http://www.csscripting.com/

// Supported Properties: 
// column-count 
// column-width	
// column-gap
// column-rule

// Unsupported Properties: 
// column-rule-width (use column-rule instead)
// column-rule-style (use column-rule instead)
// column-rule-color (use column-rule instead)
// column-span
// column-width-policy
// column-space-distribution


function CSS3MultiColumn() {
	//alert('Development Version');
	var cssCache = new Object();
	var splitableTags = new Array('P','DIV', 'SPAN', 'BLOCKQUOTE','ADDRESS','PRE', 'A', 'EM', 'I', 'STRONG', 'B', 'CITE', 'OL', 'UL', 'LI' );
	var pseudoCSSRules = new Object();
	var ut = new CSS3Utility();

	var debug = ut.debug;
	if(document.location.search.match('mode=debug')) var isDebug = true;
	else var isDebug = false;
		
	var bestSplitPoint = null; 
	var secondSplitPoint = null;
	var secondSplitBottom = 0;
	var documentReady = false;
	
	// INITIALIZATION
	ut.XBrowserAddEventHandler(window,'load',function() { documentReady = true;  processElements(); } );
	loadStylesheets();
	
	// CSS PARSING
	// --------------------------------------------------------------------------------------
	// loadStylesheets: 
	// Loop through the stylesheets collection and load the css text into the cssCache object	
	function loadStylesheets() {
		if(document.styleSheets) {	// Firefox & IE
			// initialize cache
			for(var i=0;i < document.styleSheets.length;i++) {			
				cssCache[document.styleSheets[i].href] = false;
			}
			// load css in the cache			
			for(var i=0;i < document.styleSheets.length;i++) {						
				loadCssCache(document.styleSheets[i], 'parseStylesheets');
			}
		} else if (document.getElementsByTagName) { // OPERA
			var Lt = document.getElementsByTagName('link');
			// initialize cache
			for(var i= 0; i<Lt.length; i++) {
				cssCache[Lt[i].href] = false;
			}
			// load css in the cache	
			for(var i= 0; i<Lt.length; i++) {
				loadCssCache(Lt[i], 'parseStylesheets');
			}
			//var St = document.getElementsByTagName('style');
		}
	}

	// loadCssCache
	// Asynchronous function. Call the 'callback' function when done.
	function loadCssCache(s, callback) {
		if (s.href && s.cssText) {
			cssCache[s.href] = s.cssText;
			eval(callback)();
		}
		if (s.href && typeof XMLHttpRequest!='undefined') {	
			var xmlhttp = new XMLHttpRequest();
 			//if(xmlhttp.abort) xmlhttp.abort();
			xmlhttp.onreadystatechange = function() {
				if(xmlhttp.readyState == 4) {
					if(typeof xmlhttp.status == 'undefined' || xmlhttp.status == 200 || xmlhttp.status == 304 ) {
						cssCache[s.href] = xmlhttp.responseText;								
						eval(callback)();
					}
				}
			}
			xmlhttp.open("GET", s.href, true); //synchrone transaction crashes Opera 8.01
			xmlhttp.send(null);
		}
	}
	
	// parseStylesheets:
	// Iterates the cssCache object and send the serialized css to the mini-parser.
	function parseStylesheets() {		
		var allDone = true;
		for(var i in cssCache) {
			if(cssCache[i]!=false) parseStylesheet(cssCache[i]);
			else allDone = false;
		}		
		if(allDone) {			
			processElements();
		}
	}

	// parseStylesheet:
	// Loads the pseudoCSSRules object with the values for column-count, column-width, column-gap... 
	function parseStylesheet(cssText) {
									
 		// Retrieving column-count property
		var cc = new ut.getPseudoCssRules('column-count',cssText);
		for(var i=0; cc && i<cc.cssRules.length;i++) {
			if(!pseudoCSSRules[cc.cssRules[i].selectorText]) 
				pseudoCSSRules[cc.cssRules[i].selectorText] = new Object();
			pseudoCSSRules[cc.cssRules[i].selectorText]['column-count'] = cc.cssRules[i].value;
		}	
		// Retrieving column-width property
		cc = new ut.getPseudoCssRules('column-width',cssText);				
		for(var i=0; cc && i<cc.cssRules.length;i++) {
			if(!pseudoCSSRules[cc.cssRules[i].selectorText]) 
				pseudoCSSRules[cc.cssRules[i].selectorText] = new Object();
			pseudoCSSRules[cc.cssRules[i].selectorText]['column-width'] = cc.cssRules[i].value;
		}
		// Retrieving column-gap property
		cc = new ut.getPseudoCssRules('column-gap',cssText);
		for(var i=0; cc && i<cc.cssRules.length;i++) {
			if(!pseudoCSSRules[cc.cssRules[i].selectorText]) 
				pseudoCSSRules[cc.cssRules[i].selectorText] = new Object();
			pseudoCSSRules[cc.cssRules[i].selectorText]['column-gap'] = cc.cssRules[i].value;
		}			
		// Retrieving column-rule property
		cc = new ut.getPseudoCssRules('column-rule',cssText);
		for(var i=0; cc && i<cc.cssRules.length;i++) {
			if(!pseudoCSSRules[cc.cssRules[i].selectorText]) 
				pseudoCSSRules[cc.cssRules[i].selectorText] = new Object();
			pseudoCSSRules[cc.cssRules[i].selectorText]['column-rule'] = cc.cssRules[i].value;
		}			
	}
	
 	// COLUMN PROCESSING 
	function processElements() {
		// wait for page to finish loading
		if(!documentReady) return;
		
		for(var i in pseudoCSSRules) {
			debug(i + ' cc:' + pseudoCSSRules[i]['column-count'] + ' cw:' + pseudoCSSRules[i]['column-width'] + ' cr:' + pseudoCSSRules[i]['column-rule'] + ' cg:' + pseudoCSSRules[i]['column-gap']);			
			var affectedElements = ut.cssQuery(i);			
			for(var j=0;j<affectedElements.length;j++) {
				//debug("affected element: " + affectedElements[j].tagName + ' [' + affectedElements[j].id + ' / ' + affectedElements[j].className + ']');																			 
				processElement(affectedElements[j], pseudoCSSRules[i]['column-count'], pseudoCSSRules[i]['column-width'], pseudoCSSRules[i]['column-gap'], pseudoCSSRules[i]['column-rule']);
			}
		}
	}
	
	function processElement(affectedElement, column_count, column_width, column_gap, column_rule ) {
		//affectedElement.style.visibility = 'hidden';
		var widthUnit;
		var width;
		var column_rule_width = 0;
		
		// Get available width
		// see http://www.csscripting.com/css-multi-column/dom-width-height.php
		// offsetWidth & scrollWidth are the only consistent values across browsers.
		// offsetWidth includes border, padding and scroll bars
		// scrollWidth includes border and padding
		// clientWidth when available includes padding only.
		// see http://msdn.microsoft.com/workshop/author/om/measuring.asp
		
		if(affectedElement.clientWidth && affectedElement.clientWidth != 0) {			
			var padding;
			if(affectedElement.currentStyle) {
				padding = parseInt(affectedElement.currentStyle.paddingLeft.replace(/[\D]*/gi,"")) + parseInt(affectedElement.currentStyle.paddingRight.replace(/[\D]*/gi,""))  
			} else if (document.defaultView && document.defaultView.getComputedStyle) {
				padding = parseInt(document.defaultView.getComputedStyle(affectedElement,"").getPropertyValue("padding-left").replace(/[\D]*/gi,"")) + parseInt(document.defaultView.getComputedStyle(affectedElement,"").getPropertyValue("padding-left").replace(/[\D]*/gi,""))  
				//padding = parseInt(window.getComputedStyle(affectedElement,"").getPropertyValue("padding-left").replace(/[\D]*/gi,"")) + parseInt(window.getComputedStyle(affectedElement,"").getPropertyValue("padding-left").replace(/[\D]*/gi,""))  
			} 
			
			if (isNaN(padding)) padding = 0;  
			width = (affectedElement.clientWidth - padding).toString() + "px";
		}
		else if(affectedElement.scrollWidth) {
			var borderWidth;
			var padding;
			
			if(affectedElement.currentStyle) {
				padding = parseInt(affectedElement.currentStyle.paddingLeft.replace(/[\D]*/gi,"")) + parseInt(affectedElement.currentStyle.paddingRight.replace(/[\D]*/gi,""))  
			} else if (document.defaultView && document.defaultView.getComputedStyle) {				
				padding = parseInt(document.defaultView.getComputedStyle(affectedElement,"").getPropertyValue("padding-left").replace(/[\D]*/gi,"")) + parseInt(document.defaultView.getComputedStyle(affectedElement,"").getPropertyValue("padding-left").replace(/[\D]*/gi,""))  
			}
			
			if (isNaN(padding)) padding = 0;  
				
			if(affectedElement.currentStyle) {
				borderWidth = parseInt(affectedElement.currentStyle.borderLeftWidth.replace(/[\D]*/gi,"")) + parseInt(affectedElement.currentStyle.borderRightWidth.replace(/[\D]*/gi,""))  
			} else if (document.defaultView && document.defaultView.getComputedStyle) {
				borderWidth = parseInt(document.defaultView.getComputedStyle(affectedElement,"").getPropertyValue("border-left-width").replace(/[\D]*/gi,"")) + parseInt(document.defaultView.getComputedStyle(affectedElement,"").getPropertyValue("border-right-width").replace(/[\D]*/gi,""))  
			}
			if (isNaN(borderWidth)) borderWidth = 0;
			
			width = (affectedElement.scrollWidth - padding - borderWidth).toString() + "px";			
		}
		else width = "99%"; // ever used? 

		var availableWidth = parseInt(width.replace(/[\D]*/gi,""));			

		// Get width unit
		if(!column_width || column_width == 'auto') 
		   	widthUnit = width.replace(/[\d]*/gi,"");
		else
			widthUnit = column_width.replace(/[\d]*/gi,"");
		if(!widthUnit) 
			widthUnit = "px";
		
		if(!column_gap) { // Compute column spacing (column_gap)
			if(widthUnit=="%") 
				column_gap = 1; //%;
			else
				column_gap = 15; //px;
		} else {
			column_gap = parseInt(column_gap.replace(/[\D]*/gi,""));
		}
		if(column_rule && column_rule != 'none') {
			column_gap = Math.floor(column_gap/2);
			// we add half the original column_gap to the column_rule_width to fix the column_width count below.
			column_rule_width = column_gap + parseInt(column_rule.substring(column_rule.search(/\d/),column_rule.search(/\D/)));
		}		
		if(!column_width || column_width == 'auto') {// Compute columns' width 
			column_width = (availableWidth-((column_gap+column_rule_width)*(column_count-1))) / column_count;
		} else {
			column_width = parseInt(column_width.replace(/[\D]*/gi,""))
			if(!column_count || column_count == 'auto') {// Compute column count
				column_count = Math.floor(availableWidth / (column_width + column_gap));
			}
		}
		
		column_width -= 1; 
		
		// Create a wrapper
		var wrapper = document.createElement('div'); //affectedElement.tagName
		var pn = affectedElement.parentNode;  
		wrapper = pn.insertBefore(wrapper, affectedElement);
		var elem =  pn.removeChild(affectedElement);
		elem = wrapper.appendChild(elem);
		//wrapper.style.border = "1px solid #F00";
		wrapper.className = elem.className;
		elem.className = "";
		// since all columns will be left-floating we need to clear the floats after them.
		//wrapper.style.overflow = 'auto';

		// Assign the content element a random Id ?
		elem.id = ut.randomId();

		// Adjust content's width and float the element 
		elem.style.width = column_width.toString() + widthUnit;
		//elem.style.padding = "0";
		//elem.style.margin = "0"; 
		
		if(typeof elem.style.styleFloat != 'undefined')
			elem.style.styleFloat  = "left"; 
		if(typeof elem.style.cssFloat != 'undefined') 
			elem.style.cssFloat  = "left"; 

		// Compute Desired Height
		var newHeight = Math.floor(elem.offsetHeight / column_count)+14;
		if(!wrapper.id) wrapper.id = ut.randomId();
		
		// Find split points (j is the max # of attempts to find a good height with no unsplittable element on the split point.
		var j=1;
		for(var i=1; i < column_count && elem && j < (column_count + 5) ; i++) {
			bestSplitPoint = null;
			secondSplitPoint = null;
			secondSplitBottom = 0;
			findSplitPoint(elem, newHeight*i, wrapper);			
			
			if(isDebug) bestSplitPoint.style.border = "1px solid #00FF00";

			if(bestSplitPoint && !isElementSplitable(bestSplitPoint)) {
					
					newHeight = getElementRelativeTop(bestSplitPoint, wrapper) + bestSplitPoint.offsetHeight + 10;
					i=1; // reset the height. Try again.
					debug('reset new Height = '+newHeight + ' relativetop=' + getElementRelativeTop(bestSplitPoint, wrapper) + ' offsetHeight= ' + bestSplitPoint.offsetHeight );
			}			
			else if (!bestSplitPoint) {
				debug("No split point found with " + newHeight); 
			}
			
			j++;
		}
		
		//wrapper.style.minHeight = newHeight + 'px';
		//if(document.all && !window.opera)
			//wrapper.style.height = newHeight + 'px';
		debug('<table><tr><td>Avail. Width</td><td>'+availableWidth+'</td><td>Units</td><td>'+widthUnit+'</td></tr><tr><td>column_width</td><td>'+column_width+'</td><td>column_count</td><td>'+column_count+'</td></tr><tr><td>column_gap</td><td>'+column_gap+'</td><td>column_rule</td><td>'+column_rule+'</td></tr><tr><td>New Height</td><td>' + newHeight + '</td><td></td><td></td></tr></table>'  );
 		
		for(var i=1; i < column_count && elem; i++) {
			// Find the split point (a child element, sitting on the column split point)
			bestSplitPoint = null;
			secondSplitPoint = null;
			secondSplitBottom = 0;
			
			findSplitPoint(elem, newHeight, wrapper);
			if(bestSplitPoint && isElementSplitable(bestSplitPoint) && elem.id != bestSplitPoint.id) {
				var splitE = bestSplitPoint;				
				if(isDebug) secondSplitPoint.style.border = "1px dotted #00F";
			}
			else {
				var splitE = secondSplitPoint;
			}
			if(!splitE) {
				debug("<hr />No split point found for " + elem.tagName + ' ' + newHeight);
				return;
			}
			
			// DEBUG ONLY: SHOW SPLIT ELEMENT
			//debug("split top=" + getElementRelativeTop(splitE, wrapper));
			if(isDebug) splitE.style.border = "1px solid #F00";
			// END DEBUG ONLY: SHOW SPLIT ELEMENT
			
			// Create New Column	
			var newCol = elem.cloneNode(false);
			newCol.id = ut.randomId();
			
			// Insert new column in the document
			elem.parentNode.insertBefore(newCol, elem.nextSibling);

			// Add the column_gap
			newCol.style.paddingLeft = column_gap + widthUnit;
						
			// Add the column_rule
			if(column_rule && column_rule != 'none') {				
				newCol.style.borderLeft = column_rule;
				elem.style.paddingRight = column_gap + widthUnit;				
			}
			if(document.all && !window.opera)
				elem.style.height = newHeight+'px';
			elem.style.minHeight = newHeight+'px';

			// Move all elements after the element to be splitted (splitE) to the new column
			var insertPoint = createNodeAncestors(splitE,elem, newCol, 'append');

			var refElement = splitE;			
			while(refElement && refElement.id != elem.id ) {
				var littleSib = refElement.nextSibling;
				while(littleSib) {
					moveNode(littleSib, elem, newCol);
					littleSib = refElement.nextSibling;				
				}
				refElement = refElement.parentNode; 
			}

			var strippedLine = splitElement(splitE, newHeight - getElementRelativeTop(splitE, wrapper), elem, newCol);			

			// cleaning emptied elements
			var pn = splitE.parentNode;			
			while(pn && pn.id != elem.id) {
				var n = pn.firstChild;
				while(n) {					
					if((n.nodeType==1 && n.childNodes.length == 0) || 
						(n.nodeType==3 && n.nodeValue.replace(/[\u0020\u0009\u000A]*/,'') == "")) {
						pn.removeChild(n);
						n = pn.firstChild;
					} else {
						n = n.nextSibling;
					}
				}
				pn = pn.parentNode;
			}	
			
			// if text-align is justified, insert &nbsp; to force the justify	
			if(strippedLine) {
				splitE = elem.lastChild;
				if(splitE && (document.defaultView  && document.defaultView.getComputedStyle(splitE,'').getPropertyValue('text-align')=='justify') ||
				   (splitE.currentStyle && splitE.currentStyle.textAlign == 'justify')) {
					  var txtFiller = document.createTextNode(' ' + strippedLine.replace(/./g,"\u00a0")); // &nbsp;
					  var filler = document.createElement('span');				  
					  splitE.appendChild(filler); 		
					  filler.style.lineHeight="1px";
					  filler.appendChild(txtFiller);
				} 
			}
			// move on to split the newly added column
			elem = newCol;
		}
		if(elem) {//mainly to set the column rule at the right height.
			if(document.all && !window.opera)
				elem.style.height = newHeight+'px';
			elem.style.minHeight = newHeight+'px';  
		}
		
		var clearFloatDiv = document.createElement('div');
		clearFloatDiv.style.clear = "left";  // < bug in Safari 1.3 ? (duplicates content)
		clearFloatDiv.appendChild(document.createTextNode(' '));
		wrapper.appendChild(clearFloatDiv);
		if(navigator.userAgent.toLowerCase().indexOf('safari') + 1)
			wrapper.innerHTML+=' '; // forces redraw in safari and fixes bug above.
		
		//wrapper.style.visibility = 'visible'; 				
	}
	
	// Find the deepest splitable element that sits on the split point.
	function findSplitPoint(n, newHeight, wrapper) {		
		if (n.nodeType==1) {
			var top = getElementRelativeTop(n, wrapper);
			var bot = top+n.offsetHeight;
			if(top < newHeight && bot > newHeight) {
				bestSplitPoint = n;
				if(isElementSplitable(n)) {
					for(var i=0;i<n.childNodes.length;i++) {
						findSplitPoint(n.childNodes[i], newHeight, wrapper);
					}
				}
				return;
			} 
			if(bot <= newHeight && bot >= secondSplitBottom) {
				secondSplitBottom = bot;
				secondSplitPoint = n;
			}
		}
		return;
	}
	
	function isElementSplitable(n) {
		if(n.tagName) {
			var tagName = n.tagName.toUpperCase();			
			for(var i=0;i<splitableTags.length;i++)
				if(tagName==splitableTags[i]) return true;
		}
		return false;
	}
		
	function splitElement(n, targetHeight, col1, col2) {
		
		var cn = n.lastChild;
		while(cn) {
			// if the child node is a text node 			
			if(cn.nodeType==3) {				
				var strippedText = "dummmy";
				var allStrippedText = "";
				// the +2 is for tweaking.. allowing lines to fit more easily
				while(n.offsetHeight > targetHeight+2 && strippedText!="") {
					// remove lines of text until the splittable element reaches the targeted height or we run out of text.
					strippedText = stripOneLine(cn);
					allStrippedText = strippedText + allStrippedText;
				}
				if(allStrippedText!="") {					
					var insertPoint = createNodeAncestors(cn,col1,col2,'insertBefore');
					insertPoint.insertBefore(document.createTextNode(allStrippedText), insertPoint.firstChild);
				} 
				if(cn.nodeValue=="") {
					cn.parentNode.removeChild(cn);
				}
				else 
					break;
			}
			else {
				// move element
				var insertPoint = createNodeAncestors(cn,col1,col2,'insertBefore');
				insertPoint.insertBefore(cn.parentNode.removeChild(cn), insertPoint.firstChild);
			}
			cn = n.lastChild;
		}
		return strippedText; // returns the last line of text removed (used later for forcing the justification)
	}
	

	// stripOneLine()
	// This function removes exactly one line to
	// any element containing text
	// and returns the removed text as a string.
	function stripOneLine (n) {
		// get the text node
		while(n && n.nodeType != 3) 
			n = n.firstChild;
		if(!n) return;
	
		// get the height of the element
		var e = n.parentNode;
		var h = e.offsetHeight;
		
		if(!h) {
			//debug('no height for: ' + e.tagName);
			return "";
		}
	
		// get the text as a string
		var str = n.nodeValue;
		
		// remove a word from the end of the string
		// until the height of the element changes 
		// (ie. a line has been removed)
		var wIdx= n.nodeValue.lastIndexOf(' ');
		while(wIdx!=-1 && e.offsetHeight == h) {			
			n.nodeValue = n.nodeValue.substr(0,	wIdx);
			wIdx = n.nodeValue.lastIndexOf(' ');
			if(wIdx==-1) wIdx = n.nodeValue.lastIndexOf('\n');
			//debug(e.offsetHeight + ' ' + h + ' text=' + n.nodeValue + ' wIdx= ' + wIdx);
		} 
		
		if(e.offsetHeight == h)
			n.nodeValue = "";
		// returns the removed text

		return str.substr(n.nodeValue.length);
	}
	
	// method= 'append'/'insertBefore', relative to col2
	function createNodeAncestors(n,col1,col2,method) {
		var ancestors = new Array;
		var insertNode = col2;
		var pn = n.parentNode;
		while(pn && pn.id != col1.id) {
			ancestors[ancestors.length] = pn;
			if(!pn.id) pn.id = ut.randomId();
			pn = pn.parentNode;
		}		
		
		for (var i=ancestors.length-1; i >= 0; i--) {
			
			for(var j=0; j < insertNode.childNodes.length && (insertNode.childNodes[j].nodeType==3 || !insertNode.childNodes[j].className.match(ancestors[i].id+'-css3mc')); j++);

			if(j==insertNode.childNodes.length) { 					
				// Ancestor node not found, needs to be created.				
				if(method=='append')
					insertNode = insertNode.appendChild(document.createElement(ancestors[i].tagName));
				else
					insertNode = insertNode.insertBefore(document.createElement(ancestors[i].tagName),insertNode.firstChild);
				insertNode.className = ancestors[i].className+ ' ' + ancestors[i].id + '-css3mc';
				insertNode.style.marginTop = "0";
				insertNode.style.paddingTop = "0";
				if(insertNode.tagName.toUpperCase() == 'OL' && n.nodeType == 1 && n.tagName.toUpperCase() =='LI') {
					var prevsib = n.previousSibling;
					var count=0;
					while(prevsib) {
						if(prevsib.nodeType==1 && prevsib.tagName.toUpperCase() == 'LI') 
							count++;
						prevsib = prevsib.previousSibling;
					}
					insertNode.setAttribute('start', count);
				}
			} else {
				insertNode = insertNode.childNodes[j];
				if(insertNode.tagName.toUpperCase() == 'OL' && (insertNode.start==-1 || insertNode.start==1) && n.nodeType == 1 && n.tagName.toUpperCase() =='LI') {
					// happens if the tag was created while processing a text node.
					var prevsib = n.previousSibling;
					var count=0;
					while(prevsib) {
						if(prevsib.nodeType==1 && prevsib.tagName.toUpperCase() == 'LI') 
							count++;
						prevsib = prevsib.previousSibling;
					}
					insertNode.setAttribute('start', count);
				}
			}
		}
		return insertNode;
	}
	
	function moveNode(n,col1,col2) {		
		var insertNode=createNodeAncestors(n,col1,col2, 'append');
		var movedNode = insertNode.appendChild(n.parentNode.removeChild(n));
		if(insertNode.id == col2.id && movedNode.nodeType ==1 ) {
			movedNode.style.paddingTop = "0px";
			movedNode.style.marginTop = "0px";
		}
		return movedNode;
	}
	
	
	function getElementRelativeTop(obj, refObj) {
		var cur = 0;
		if(obj.offsetParent) {		
			while(obj.offsetParent) {
				cur+=obj.offsetTop;
				obj = obj.offsetParent;
			}
		}
		var cur2 = 0;
		if(refObj.offsetParent) {		
			while(refObj.offsetParent) {
				cur2+=refObj.offsetTop;
				refObj = refObj.offsetParent;
			}
		}
		return cur-cur2; // + document.body.offsetTop;
	}
	
}

// =====================================================================================
// Utility Class Constructor skeleton
function CSS3Utility() {
	// Event Handler utility list
	this.handlerList = new Array(); 
}


// Public Methods
// ==============

// querying of a DOM document using CSS selectors (a getElementsByTagName on steroids)
// see http://dean.edwards.name/my/cssQuery.js.html
/*
    License: http://creativecommons.org/licenses/by/1.0/
    Author:  Dean Edwards/2004
    Web:     http://dean.edwards.name/
*/
CSS3Utility.prototype.cssQuery = function() { 

    var version = "1.0.1"; // timestamp: 2004/05/25

    // constants
    var STANDARD_SELECT = /^[^>\+~\s]/;
    var STREAM = /[\s>\+~:@#\.]|[^\s>\+~:@#\.]+/g;
    var NAMESPACE = /\|/;
    var IMPLIED_SELECTOR = /([\s>\+~\,]|^)([\.:#@])/g;
    var ASTERISK ="$1*$2";
    var WHITESPACE = /^\s+|\s*([\+\,>\s;:])\s*|\s+$/g;
    var TRIM = "$1";
    var NODE_ELEMENT = 1;
    var NODE_TEXT = 3;
    var NODE_DOCUMENT = 9;

    // sniff for explorer (cos of one little bug)
    var isMSIE = /MSIE/.test(navigator.appVersion), isXML;

    // cache results for faster processing
    var cssCache = {};

    // this is the query function
    function cssQuery(selector, from) {
        if (!selector) return [];
        var useCache = arguments.callee.caching && !from;
        from = (from) ? (from.constructor == Array) ? from : [from] : [document];
        isXML = false;//checkXML(from[0]);
        // process comma separated selectors
        var selectors = parseSelector(selector).split(",");
        var match = [];
        for (var i in selectors) {
            // convert the selector to a stream
            selector = toStream(selectors[i]);
            // process the stream
            var j = 0, token, filter, cacheSelector = "", filtered = from;
            while (j < selector.length) {
                token = selector[j++];
                filter = selector[j++];
                cacheSelector += token + filter;
                // process a token/filter pair
                filtered = (useCache && cssCache[cacheSelector]) ? cssCache[cacheSelector] : select(filtered, token, filter);
                if (useCache) cssCache[cacheSelector] = filtered;
            }
            match = match.concat(filtered);
        }
        // return the filtered selection
        return match;
    };
    cssQuery.caching = false;
    cssQuery.reset = function() {
        cssCache = {};
    };
    cssQuery.toString = function () {
        return "function cssQuery() {\n  [version " + version + "]\n}";
    };

    var checkXML = (isMSIE) ? function(node) {
        if (node.nodeType != NODE_DOCUMENT) node = node.document;
        return node.mimeType == "XML Document";
    } : function(node) {
        if (node.nodeType == NODE_DOCUMENT) node = node.documentElement;
        return node.localName != "HTML";
    };

    function parseSelector(selector) {
        return selector
        // trim whitespace
        .replace(WHITESPACE, TRIM)
        // encode attribute selectors
        .replace(attributeSelector.ALL, attributeSelector.ID)
        // e.g. ".class1" --> "*.class1"
        .replace(IMPLIED_SELECTOR, ASTERISK);
    };

    // convert css selectors to a stream of tokens and filters
    //  it's not a real stream. it's just an array of strings.
    function toStream(selector) {
        if (STANDARD_SELECT.test(selector)) selector = " " + selector;
        return selector.match(STREAM) || [];
    };

    var pseudoClasses = { // static
        // CSS1
        "link": function(element) {
            for (var i = 0; i < document.links; i++) {
                if (document.links[i] == element) return true;
            }
        },
        "visited": function(element) {
            // can't do this without jiggery-pokery
        },
        // CSS2
        "first-child": function(element) {
            return !previousElement(element);
        },
        // CSS3
        "last-child": function(element) {
            return !nextElement(element);
        },
        "root": function(element) {
            var document = element.ownerDocument || element.document;
            return Boolean(element == document.documentElement);
        },
        "empty": function(element) {
            for (var i = 0; i < element.childNodes.length; i++) {
                if (isElement(element.childNodes[i]) || element.childNodes[i].nodeType == NODE_TEXT) return false;
            }
            return true;
        }
        // add your own...
    };

    var QUOTED = /([\'\"])[^\1]*\1/;
    function quote(value) {return (QUOTED.test(value)) ? value : "'" + value + "'"};
    function unquote(value) {return (QUOTED.test(value)) ? value.slice(1, -1) : value};

    var attributeSelectors = [];

    function attributeSelector(attribute, compare, value) {
        // properties
        this.id = attributeSelectors.length;
        // build the test expression
        var test = "element.";
        switch (attribute.toLowerCase()) {
            case "id":
                test += "id";
                break;
            case "class":
                test += "className";
                break;
            default:
                test += "getAttribute('" + attribute + "')";
        }
        // continue building the test expression
        switch (compare) {
            case "=":
                test += "==" + quote(value);
                break;
            case "~=":
                test = "/(^|\\s)" + unquote(value) + "(\\s|$)/.test(" + test + ")";
                break;
            case "|=":
                test = "/(^|-)" + unquote(value) + "(-|$)/.test(" + test + ")";
                break;
        }
        push(attributeSelectors, new Function("element", "return " + test));
    };
    attributeSelector.prototype.toString = function() {
        return attributeSelector.PREFIX + this.id;
    };
    // constants
    attributeSelector.PREFIX = "@";
    attributeSelector.ALL = /\[([^~|=\]]+)([~|]?=?)([^\]]+)?\]/g;
    // class methods
    attributeSelector.ID = function(match, attribute, compare, value) {
        return new attributeSelector(attribute, compare, value);
    };

    // select a set of matching elements.
    // "from" is an array of elements.
    // "token" is a character representing the type of filter
    //  e.g. ">" means child selector
    // "filter" represents the tag name, id or class name that is being selected
    // the function returns an array of matching elements
    function select(from, token, filter) {
        //alert("token="+token+",filter="+filter);
        var namespace = "";
        if (NAMESPACE.test(filter)) {
            filter = filter.split("|");
            namespace = filter[0];
            filter = filter[1];
        }
        var filtered = [], i;
        switch (token) {
            case " ": // descendant
                for (i in from) {
					if(typeof from[i]=='function') continue;
                    var subset = getElementsByTagNameNS(from[i], filter, namespace);
                    for (var j = 0; j < subset.length; j++) {
                        if (isElement(subset[j]) && (!namespace || compareNamespace(subset[j], namespace)))
                            push(filtered, subset[j]);
                    }
                }
                break;
            case ">": // child
                for (i in from) {
                    var subset = from[i].childNodes;
                    for (var j = 0; j < subset.length; j++)
                        if (compareTagName(subset[j], filter, namespace)) push(filtered, subset[j]);
                }
                break;
            case "+": // adjacent (direct)
                for (i in from) {
                    var adjacent = nextElement(from[i]);
                    if (adjacent && compareTagName(adjacent, filter, namespace)) push(filtered, adjacent);
                }
                break;
            case "~": // adjacent (indirect)
                for (i in from) {
                    var adjacent = from[i];
                    while (adjacent = nextElement(adjacent)) {
                        if (adjacent && compareTagName(adjacent, filter, namespace)) push(filtered, adjacent);
                    }
                }
                break;
            case ".": // class
                filter = new RegExp("(^|\\s)" + filter + "(\\s|$)");
                for (i in from) if (filter.test(from[i].className)) push(filtered, from[i]);
                break;
            case "#": // id
                for (i in from) if (from[i].id == filter) push(filtered, from[i]);
                break;
            case "@": // attribute selector
                filter = attributeSelectors[filter];
                for (i in from) if (filter(from[i])) push(filtered, from[i]);
                break;
            case ":": // pseudo-class (static)
                filter = pseudoClasses[filter];
                for (i in from) if (filter(from[i])) push(filtered, from[i]);
                break;
        }
        return filtered;
    };

    var getElementsByTagNameNS = (isMSIE) ? function(from, tagName) {
        return (tagName == "*" && from.all) ? from.all : from.getElementsByTagName(tagName);
    } : function(from, tagName, namespace) {
        return (namespace) ? from.getElementsByTagNameNS("*", tagName) : from.getElementsByTagName(tagName);
    };

    function compareTagName(element, tagName, namespace) {
        if (namespace && !compareNamespace(element, namespace)) return false;
        return (tagName == "*") ? isElement(element) : (isXML) ? (element.tagName == tagName) : (element.tagName == tagName.toUpperCase());
    };

    var PREFIX = (isMSIE) ? "scopeName" : "prefix";
    function compareNamespace(element, namespace) {
        return element[PREFIX] == namespace;
    };

    // return the previous element to the supplied element
    //  previousSibling is not good enough as it might return a text or comment node
    function previousElement(element) {
        while ((element = element.previousSibling) && !isElement(element)) continue;
        return element;
    };

    // return the next element to the supplied element
    function nextElement(element) {
        while ((element = element.nextSibling) && !isElement(element)) continue;
        return element;
    };

    function isElement(node) {
        return Boolean(node.nodeType == NODE_ELEMENT && node.tagName != "!");
    };

    // use a baby push function because IE5.0 doesn't support Array.push
    function push(array, item) {
        array[array.length] = item;
    };

    // fix IE5.0 String.replace
    if ("i".replace(/i/,function(){return""})) {
        // preserve String.replace
        var string_replace = String.prototype.replace;
        // create String.replace for handling functions
        var function_replace = function(regexp, replacement) {
            var match, newString = "", string = this;
            while ((match = regexp.exec(string))) {
                // five string replacement arguments is sufficent for cssQuery
                newString += string.slice(0, match.index) + replacement(match[0], match[1], match[2], match[3], match[4]);
                string = string.slice(match.lastIndex);
            }
            return newString + string;
        };
        // replace String.replace
        String.prototype.replace = function (regexp, replacement) {
            this.replace = (typeof replacement == "function") ? function_replace : string_replace;
            return this.replace(regexp, replacement);
        };
    }

    return cssQuery;
}();

// Cross-Browser event handler.
CSS3Utility.prototype.XBrowserAddEventHandler = function(target,eventName,handlerName) {      
	if(!target) return;
	if (target.addEventListener) { 
		target.addEventListener(eventName, function(e){eval(handlerName)(e);}, false);
	} else if (target.attachEvent) { 
		target.attachEvent("on" + eventName, function(e){eval(handlerName)(e);});
		} else { 
		// THIS CODE NOT TESTED 
		var originalHandler = target["on" + eventName]; 
		if (originalHandler) { 
		  target["on" + eventName] = function(e){originalHandler(e);eval(handlerName)(e);}; 
		} else { 
		  target["on" + eventName] = eval(handlerName); 
		} 
	} 
	// Keep track of added handlers.
	var l = this.handlerList.length;
	this.handlerList[l] = new Array(2);
	this.handlerList[l][0] = target.id;  
	this.handlerList[l][1] = eventName;  	
	// see http://weblogs.asp.net/asmith/archive/2003/10/06/30744.aspx
	// for a complete XBrowserAddEventHandler 
}



// getPseudoCssRules()
// Constructor for a pseudo-css rule object 
// (an unsupported property, thus not present in the DOM rules collection)

// Constructor parameters
// ----------------------
// the css property name
// the stylesheet (as a text stream)

// Object properties: 
// ------------------
// selector (string)
// property (string)
// value (string)
CSS3Utility.prototype.getPseudoCssRules = function(propertyName, serializedStylesheet) {
	this.cssRules = new Array();
	var valuePattern = propertyName.replace("-","\-")+"[\\s]*:[\\s]*([^;}]*)[;}]";
	var selectorPattern = "$";
	var regx = new RegExp(valuePattern,"g");
	var regxMatch = regx.exec(serializedStylesheet);
	var j=0;
	
	while(regxMatch){
		var str = serializedStylesheet.substr(0,serializedStylesheet.substr(0,serializedStylesheet.indexOf(regxMatch[0])).lastIndexOf('{'));
		var selectorText = str.substr(str.lastIndexOf('}')+1).replace(/^\s*|\s*$/g,"");
		// ignore commented rule !!  
		this.cssRules[j] = new Object();
		this.cssRules[j].selectorText = selectorText;
		this.cssRules[j].property = propertyName;
		this.cssRules[j].value = regxMatch[1].replace(/(\r?\n)*/g,"");  // suppress line breaks
		j++;
		regxMatch = regx.exec(serializedStylesheet);
	}	
}


// Generates a random ID
CSS3Utility.prototype.randomId = function () {
	var rId = "";
	for (var i=0; i<6;i++)
		rId += String.fromCharCode(97 + Math.floor((Math.random()*24)))
	return rId;
}

CSS3Utility.prototype.debug = function(text) { 
	var debugOutput = document.getElementById('debugOutput'); // Debug Output
	if(typeof debugOutput != "undefined" && debugOutput) {
		//debugOutput.appendChild(document.createElement('hr')); 
		//debugOutput.appendChild(document.createTextNode(text)); 
		debugOutput.innerHTML+= text;
	}
}


 
// Object Instance
var css3MC = new CSS3MultiColumn();
