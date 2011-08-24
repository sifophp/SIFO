SIFO Overview
=============
[SIFO] is a well-proben open source PHP5 framework currently running on several 
sites, from small installations to large websites with multiple servers.

SIFO is free and very easy to use. Contains a lot of libraries and classes that
allow you to get your projects running in a breeze.

You can download the code from both [Github] [GH] or [Google Code] [GC]

 [SIFO]: http://sifo.me
 [GH]: https://github.com/alombarte/SIFO "Visit the Github SIFO repository"
 [GC]: http://code.google.com/p/sifo/ "Visit the Google Code SIFO repository"




Architecture
------------
In this section we will describe a little bit the architecture of SIFO.

### MVC pattern ###
SIFO uses the MVC pattern to sepparate your project in 3 different areas:

* Models: Contain the bussiness logic. Where all the data is taken from (e.g: Queries to database)
* Views: HTML templates with a little bit of logic such as iterations. It's just the presentation of your web. [Smarty] by default.
* Controllers: The ones that put it all together with any necessary logic.

  [Smarty]: http://www.smarty.net

If you never used this pattern before, you should start now if you don't want to
become insane once your applications start to grow. Don't feel overwhelmed, it's easy.



### Multiple environments ###
Via a single file, the mighty `config/domains.config.php`, you set-up your application.
In this file you declare the domains your application will listen to and set the
configuration for each one of them. For instance, if you want to code the webpage
**http://sifo.me** you might want to set at least 2 domains: The real production 
domain *sifo.me* and a fake one for local development *sifo.local*

In the `config/domains.config.php` you can set up things like:

* Show/hide the debug (`devel` flag)
* Languages accepted by your application
* Credentials to several services, init commands, master/slave configurations (e.g: mysql)
* Related hostnames, error reporting and other specific PHP tasks.

*Portion of the file*:

	$config['sifo.me'] = array(
		'devel' => false, // No debug in production.
		'instance' => 'sifoweb',
		'language' => 'en_US',
		'database' => array(
			'profile' => 'PRODUCTION' // Master/slave profiled taken from db_profiles.config.php.
			'db_driver' => 'mysqli',
			'db_host' => '127.0.0.1',
			'db_user' => 'root',
			'db_password' => 'root',
			'db_name' => 'mydatabase',
			'db_init_commands' => array( 'SET NAMES utf8' )
		),
		/*  REDIS syntax:
		'database' => array(
			'database' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0
		*/
		),
		'php_ini_sets' => array( // Empty array if don't want changes.
			// Log errors to 'logs' folder:
			'log_errors' => 'On',
			'error_log' => ROOT_PATH . '/logs/sifoweb_errors.log',
		),
		'libraries_profile' => 'stable_libraries'
		);

		$config['sifo.local'] = $config['sifo.me']; // Take all the config from production
		// Redefine some vars for local domain:
		$config['sifo.local']['devel'] = true;
		$config['sifo.local']['static_host'] = 'http://static.sifo.local';
		$config['libraries_profile'] = 'unstable_libraries'


### Routing ###
When you type an URL there is a mapping between what you are typing in the address bar
and the controller that should reply to that request. Is what we call the *router* and
is located under `config/router.config.php`. If your application has urls translated
you can also use the `router_xx_XX.config.php` files, where the xx_XX is the language
and country code of your app. SIFO supports as much languages as you need.

### Powerful Debug ###
The SIFO debug contains a lot of interesting stuff to debug your application:

* Benchmark: Full decomposition and analysis of times of execution per file and method
* Parameters that every Controller received
* Variables every template received (smarty)
* Queries launched to the database
	* Automatic commenting of the queries
	* Nice print of the final SQL launched
	* Different coloring for write/read operations, with times
	* Slow query alert
	* Printed recordset returned in tabular data
	* Host, database, and replica the query went in
* Data stored in Session (and *kill session*)
* Data stored in user Cookies
* Cache control (names of the keys, remaining TTL...)
* Sphinx Debug
* Cache invalidation
	* AUTOMATIC: While browsing
	* Manual: By flag



### Multilanguage ###
Almost every SIFO programmer has multi-lingual projects, because it is very easy.
We even have translation tools based on config files or in database, so you can
pass the URLs to your non-programmers colleagues the URLs for proper translation.

All the smarty templates come with the i18n plugins that allow you to write your
files in a single language. Example: If we had an application with three possible
languages, we could have in the template something like:

	<h1>{t 1=$username}Hello %1, welcome back!{/t}</h1>

	This code would produce a different output depending on the current active language:

	<h1>Hello John, welcome back!</h1>
	<h1>Hola Juan, bienvenido de nuevo!</h1>
	<h1>Hola Joan, benvingut de nou!</h1>

Your application languages are set on the `config/domains.config.php` file.

Sometimes we've been asked, yes, there is support for non-latin alphabets. We do
have several installations in Russian, Arabic, Japanese, Chinese, Hebrew... so yes,
and also design with RTL text.	

### Lazy loading ###
The less classes are in-memory, the better. We stick to the principle of loading
the classe only if they are going to be used. The configuration file `config/libraries.config.php`
defines what classes are loaded per execution, and what versions of the libraries
do you use. You can even create several profiles of libraries: maybe you want to
play with the latest unstable library in local, while keeping the stable one in production.

Example:

	$config['classes_always_preloaded'] = array(
		'Exceptions',
		'Registry',
		'Cache',
		'CantLiveWithoutThisClass'
	);

	// Contains all the libraries available.
	$config['default'] = array(
		'mylibrary' => 'Mylibrary-1.5-stable'
	);
	$config['bleeding_edge_insane'] = array(
		'mylibrary' => 'Mylibrary-2.0alpha-unstable'
	);

At execution time you can always load any needed application via the 
`$this->getClass( 'ClassName' )` function in any point of the code, or if it's
going to be loaded for sure include it in a protected var:

	// Include the Filter class before the Controller is actually executed:
	protected $include_classes = array( 'Filter' );

### Multiple databases flavour ###
From Key-values like Redis to Oracle, Mysql, Postgres, Firebird or even SQLite.
You can choose to use Mysql PDO or any of the ADODB drivers. For redis, Predis
is an excellent solution.

### Static files sepparation ###
You never know what is going to happen. Sepparate your static files in a different
host is always a win-win. 

### Lots of helpful classes ###
Many projects share the same needs. Do you need to send nice emails? Geolocalise
the users? Facebook/Youtube/Twitter integration? Quick searches using Sphinx? Upload
files to Amazon S3? Do not reinvent the wheel, invest your time in something else.

### Extendable ###
What would be the framework if you couldn't add our own classes and libraries?
There is room for entire libraries, simple classes or *helpers*. Before coding
something from scratch, ensure there is not something done already.


### Command Line support ###
Yes, you like to code your cron jobs in PHP, so do we. Take profit of your existing
models. Create your scripts extending from the Command Line.
The CLI tool lets you define the supported parameters of the script and automatically
builds the help for you. If you are interested in receiveing email reporting is also
already done.

### And many more ###
I am just tired of writing, is late night and I think you read enough. If you arrived
here it's time to get a little bit deeper yourself guided by the documentation.


