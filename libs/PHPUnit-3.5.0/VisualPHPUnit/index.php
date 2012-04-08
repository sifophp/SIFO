<?php

/* VisualPHPUnit
 *
 * Copyright (c) 2011, Nick Sinopoli <nsinopoli@gmail.com>.
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
 *   * Neither the name of Nick Sinopoli nor the names of his
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
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

    // AJAX calls
    if ( isset($_GET['dir']) ) {
        if ( !file_exists($_GET['dir']) ) {
            if ( $_GET['type'] == 'dir' ) {
                echo 'Directory does not exist!';
            } else {
                echo 'File does not exist!';
            }
        } elseif ( !is_writable($_GET['dir']) ) {
            if ( $_GET['type'] == 'dir' ) {
                echo 'Directory is not writable! (Check permissions.)';
            } else {
                echo 'File is not writable! (Check permissions.)';
            }
        } else {
            echo 'OK';
        }
        exit;
    }

	require 'config.php';
	
	define( 'ROOT_PATH', realpath( dirname( __FILE__ ) . '/../../..' ) );
	
	require_once ROOT_PATH . '/instances/Bootstrap.php';

	\Sifo\Bootstrap::$instance = 'common';

    if ( empty($_POST) && !isset( $_GET['file'] ) ) {
        $results = array();
        $handler = opendir(SNAPSHOT_DIRECTORY);
        while ( $file = readdir($handler) ) {
            if ( $file != "." && $file != ".." ) {
                $results[] = $file;
            }
        }
        closedir($handler);
        arsort($results);

        include 'ui/index.html';
        exit; 
    }

	if ( !empty( $_GET['file'] ) )
	{
		$_POST['view_snapshot'] = 0;
		$_POST['view_snapshot'] = 0;
		$_POST['create_snapshots'] = 0;
		$_POST['snapshot_directory'] = '';
		$_POST['sandbox_errors'] = 0;
		$_POST['sandbox_filename'] = '';
		$_POST['test_files'] = $_GET['file'];
	}

    if ( $_POST['view_snapshot'] == 1 ) {
        $dir = realpath(SNAPSHOT_DIRECTORY) . '/';
        $snapshot = realpath($dir . trim(strval(filter_var($_POST['select_snapshot'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES))));
        include $snapshot;
        exit;
    }

    // Sanitize all the $_POST data
    $create_snapshots = (boolean) filter_var($_POST['create_snapshots'], FILTER_SANITIZE_NUMBER_INT);
    $snapshot_directory = trim(strval(filter_var($_POST['snapshot_directory'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
    $sandbox_errors = (boolean) filter_var($_POST['sandbox_errors'], FILTER_SANITIZE_NUMBER_INT);
    $sandbox_filename = trim(strval(filter_var($_POST['sandbox_filename'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
    if ( isset($_POST['sandbox_ignore']) ) {
        $sandbox_ignore = array();
        foreach ( $_POST['sandbox_ignore'] as $ignore ) {
            $sandbox_ignore[] = trim(strval(filter_var($ignore, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
        }
        $sandbox_ignore = implode('|', $sandbox_ignore);
    } else {
        $sandbox_ignore = '';
    }
	
    $test_files = trim(strval(filter_var($_POST['test_files'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
    $tests = explode('|', $test_files); 

    require 'VPU.php';
	require 'CoverageAnalysis.php';

    ob_start(); 

    $vpu = new VPU();

    if ( $sandbox_errors ) {
        set_error_handler(array($vpu, 'handle_errors'));
    }

    $results = $vpu->run($tests);

    include 'ui/header.html';
    echo $vpu->to_HTML($results['tests'], $sandbox_errors);
	echo $vpu->coverageReport( $results );

    include 'ui/footer.html';

    if ( $create_snapshots ) {
        $snapshot = ob_get_contents(); 
        $vpu->create_snapshot($snapshot, $snapshot_directory);
    }

?>
