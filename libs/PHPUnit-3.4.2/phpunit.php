#!/usr/bin/env php
<?php
/* PHPUnit
 *
 * Copyright (c) 2002-2009, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRIC
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * $Id: phpunit.php 5078 2009-08-10 07:58:18Z sb $
 */


// Define the root path:
define( 'ROOT_PATH', realpath( dirname( __FILE__ ) . '/../..' ) );
require_once ROOT_PATH . '/instances/Bootstrap.php';

require_once ROOT_PATH . '/libs/SEOframework/Exceptions.php';
require_once ROOT_PATH . '/libs/SEOframework/Registry.php';
require_once ROOT_PATH . '/libs/SEOframework/Filter.php';
require_once ROOT_PATH . '/libs/SEOframework/Config.php';
require_once ROOT_PATH . '/libs/SEOframework/Domains.php';
require_once ROOT_PATH . '/libs/SEOframework/Urls.php';
require_once ROOT_PATH . '/libs/SEOframework/Router.php';
require_once ROOT_PATH . '/libs/SEOframework/Database.php';
require_once ROOT_PATH . '/libs/SEOframework/Controller.php';
require_once ROOT_PATH . '/libs/SEOframework/Model.php';
require_once ROOT_PATH . '/libs/SEOframework/View.php';
require_once ROOT_PATH . '/libs/SEOframework/I18N.php';
require_once ROOT_PATH . '/libs/SEOframework/Crypt.php';
require_once ROOT_PATH . '/libs/SEOframework/Cookie.php';
require_once ROOT_PATH . '/libs/SEOframework/Session.php';
require_once ROOT_PATH . '/libs/SEOframework/Cache.php';
require_once ROOT_PATH . '/libs/SEOframework/FlashMessages.php';
require_once ROOT_PATH . '/libs/SEOframework/Mail.php';
require_once ROOT_PATH . '/libs/SEOframework/Benchmark.php';
require_once ROOT_PATH . '/libs/SEOframework/Client.php';

// Default values:
Bootstrap::$instance = 'default';
$_SERVER['HTTP_HOST'] = 'unit.test';
$_SERVER['REQUEST_URI'] = '/';

//if (strpos('/usr/bin/php', '@php_bin') === 0) {
    set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
//}

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

require 'PHPUnit/TextUI/Command.php';

define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

PHPUnit_TextUI_Command::main();
?>
