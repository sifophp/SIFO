$LAB.setOptions(
	{
		AlwaysPreserveOrder: false,
		UsePreloading: true,
		UseLocalXHR: true,
		UseCachePreload: true,
		AllowDuplicates: false,
		AppendTo: "head",
		BasePath: ""
	});

CORE.behaviour.modules.isset = function( sId ) {

};


onDomReady(function(){
	var sConsoleMessage = 'DEVICE: ';
	// polyfill for querySelector
	if (!document.querySelectorAll) {
		document.querySelectorAll = function(selector) {
			var doc = document,
				head = doc.documentElement.firstChild,
				styleTag = doc.createElement('STYLE');
			head.appendChild(styleTag);
			doc.__qsaels = [];

			styleTag.styleSheet.cssText = selector + "{x:expression(document.__qsaels.push(this))}";
			window.scrollBy(0, 0);

			return doc.__qsaels;
		}
	}


	if( isMobile.any() ) {
		$LAB.script(basePathConfig.mobile).wait( function() {
			sConsoleMessage += 'Mobile'  + '\n';
			loadModules();
		});
	} else {
		$LAB.script(basePathConfig.desktop).wait(function() {
			sConsoleMessage += 'Desktop'  + '\n';
			loadModules();
		}).wait( function() {

				var bLoadPolyfills = bPolyfills ? bPolyfills : false;



				if ( bLoadPolyfills ) {

					$LAB.script(basePathConfig.polyfills).wait( function() {

						$.webshims.setOptions({
						    basePath: sHostStatic  + "/js/libs/webshims/shims/",
							waitReady: false
						});

						$.webshims.polyfill();

					});


				}
		});
	}

	function loadModules() {
		var sId = '',
			oModules = document.querySelectorAll("[id]");

		sConsoleMessage += 'LAUNCH MODULES: ';

		// To Execute behaviours based on modular elements
		for ( var nCounter = 0; nCounter < oModules.length; nCounter++  ) {

			sId = oModules[nCounter].id;

			if ( CORE.behaviour.modules[ sId ] !== undefined )
			{
				CORE.behaviour.modules[ sId ].init();
				sConsoleMessage += sId + ' | ';
			}
		}

		// Returns performance and development info
		if ( console ) {
			console.log( sConsoleMessage + '\n' + 'TOTAL ID ELEMENTS:' + nCounter );
		}

		// Always execute the common behaviour (if exist)
		if ( CORE.behaviour.common !== undefined ) {
			CORE.behaviour.common();
		}

		// To execute behaviours based on pages
		if ( typeof CORE.behaviour.page[document.body.id] != "undefined" )
		{
			CORE.behaviour.page[document.body.id]();
		}
	}




});