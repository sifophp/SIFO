<?php
/**
 * SYNTAX:
 *
 * Every Key of the array is a group of JS files. All files inside a group will
 * be merged into a single file with the group name.
 *
 * The number is the Priority or order, the lower, the more soon the JS file
 * appears in the pack.
 *
 * $config['GROUP_NAME'] = array(
 *	LOAD ORDER IN PACK => array(
 *		'name' => 'A NAME TO IDENTIFY THIS JS FILE',
 *		'filename' => 'PATH TO FILENAME, EXCLUDING ROOT PATH'
 */

/* Default group, also known as CORE. */
$config['default'] = array(
	5 => array(
		'name' => 'DOM_ready',
		'filename' => 'libs/Core-js/core/ready.js',
	),
	10 => array(
		'name' => 'labs',
		'filename' => 'libs/Core-js/libs/labjs/LAB.js',
	),
	15 => array(
		'name' => 'device_detection',
		'filename' => 'libs/Core-js/core/device_detection.js',
	),
	20 => array(
		'name' => 'namespace',
		'filename' => 'libs/Core-js/core/namespace.js',
	),
	30 => array(
		'name' => 'utilities_common',
		'filename' => 'libs/Core-js/core/utilities.js',
	),
	100000 => array(
		'name' => 'init',
		'filename' => 'libs/Core-js/core/init.js',
	)
);

$config['desktop'] = array(
	10 => array(
		'name' => 'jquery',
		'filename' => 'libs/Core-js/libs/jquery/jquery-1.8.3.min.js',
	),
	20 => array(
     'name' => 'modernizr',
     'filename' => 'libs/Core-js/libs/modernizr/modernizr-custom.js',
    )
);

$config['mobile'] = array(
    10 => array(
        'name' => 'jquery',
        'filename' => 'libs/Core-js/libs/zepto/zepto.js'
    )
);

/* Polyfills */
$config['polyfills'] = array(
	10 => array(
		'name' => 'polyfiller',
		'filename' => 'libs/Core-js/libs/modernizr/polyfills/polyfiller.js'
	),
	20 => array(
		'name' => 'cssfx',
		'filename' => 'libs/Core-js/libs/modernizr/polyfills/cssfx.js'
	),
	30 => array(
		'name' => 'selectivizr',
		'filename' => 'libs/Core-js/libs/modernizr/polyfills/selectivizr-min.js'
	)
);

/* Graphs libraries */
$config['graphs'] = array(
	10 => array(
		'name' => 'excanvas',
		'filename' => 'libs/Core-js/libs/flot/excanvas.min.js',
	),
	20 => array(
		'name' => 'float',
		'filename' => 'libs/Core-js/libs/flot/jquery.flot.js',
	),
	30 => array(
		'name' => 'jquery_graph_table',
		'filename' => 'libs/Core-js/libs/flot/jquery.graphTable.js',
	),
	40 => array(
		'name' => 'graphs_class',
		'filename' => 'libs/Core-js/classes/graphs.js',
	)
);


/* Modal windows */
$config['modal'] = array(
	10 => array(
		'name' => 'colorbox',
		'filename' => 'libs/Core-js/libs/jquery-colorbox/jquery.colorbox.js',
	),
	20 => array(
		'name' => 'modals_class',
		'filename' => 'libs/Core-js/classes/modals.min.js',
	)
);

/* Tag handling utilites */
$config['tags'] = array(
	10 => array(
		'name' => 'jquery_tag_editor',
		'filename' => 'libs/Core-js/libs/jquery-tageditor/jquery.tag.editor.js',
	),
	20 => array(
		'name' => 'tags_class',
		'filename' => 'libs/Core-js/classes/tags.js',
	)
);

/* Autocompletion */
$config['autocomplete'] = array(
	10 => array(
		'name' => 'jquery_autocomplete',
		'filename' => 'libs/Core-js/libs/jquery-autocomplete/jquery.autocomplete.js',
	),
	20 => array(
		'name' => 'autocomplete_class',
		'filename' => 'libs/Core-js/classes/autocomplete.js',
	)
);


/* Tabbed navigation */
$config['tabs'] = array(
	10 => array(
		'name' => 'tabber',
		'filename' => 'libs/Core-js/libs/tabifier/tabber.js',
	),
	20 => array(
		'name' => 'tabs_class',
		'filename' => 'libs/Core-js/classes/tabs.js',
	)
);

/* User interface */
$config['user_interface'] = array(
	10 => array(
		'name' => 'jquery_ui',
		'filename' => 'libs/Core-js/libs/jquery-ui/js/jquery-ui-1.8.10.custom.min.js',
	),
	20 => array(
		'name' => 'sortable_class',
		'filename' => 'libs/Core-js/classes/sortable.js',
	)
);

/* Serialize */
$config['serialize'] = array(
	10 => array(
		'name' => 'jquery_serialize_tree',
		'filename' => 'libs/Core-js/libs/jquery-serialize/jquery.serializetree.js',
	),
	20 => array(
		'name' => 'serialize_class',
		'filename' => 'libs/Core-js/classes/serialize.js',
	)
);

/* Editable fields */
$config['editable'] = array(
	10 => array(
		'name' => 'jquery_editable',
		'filename' => 'libs/Core-js/libs/jquery-jeditable/jquery.jeditable.js',
	),
	20 => array(
		'name' => 'jquery_editable_color',
		'filename' => 'libs/Core-js/libs/jquery-jeditable/jquery.color.js',
	),
	30 => array(
		'name' => 'editable-class',
		'filename' => 'libs/Core-js/classes/editable.js',
	)
);

/* Prettify: Color source code */
$config['prettify'] = array(
	10 => array(
		'name' => 'prettifier',
		'filename' => 'libs/Core-js/libs/code-prettifier/prettify.js',
	)
);

/* Prettify: Color source code */
$config['pulldown'] = array(
	10 => array(
		'name' => 'pulldown_class',
		'filename' => 'libs/Core-js/classes/pulldown.js',
	)
);