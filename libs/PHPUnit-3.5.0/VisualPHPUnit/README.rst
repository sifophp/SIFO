VisualPHPUnit
=============

VisualPHPUnit is a visual front-end for PHPUnit.  Written in PHP, it aims to make unit testing more appealing. 

Features
--------

VisualPHPUnit provides the following features:

* A stunning front-end which organizes test and suite results
* An option to maintain a history of unit test results through the use of visual logs 
* Enumeration of PHPUnit statistics and messages
* Convenient display of any debug messages written within unit tests
* Sandboxing of PHP errors/exceptions

Screenshot
----------

.. image:: http://echodrop.net/code/vpu/ss2.png

Installation
------------

1. Download and extract the project to a web-accessible directory.
2. Open config.php with your favorite editor.
    a. Change PHPUNIT_INSTALL so that it points to the directory where PHPUnit is installed.
    b. Update TEST_DIRECTORY so that it points to the root directory where your unit tests are stored.
3. Point your browser to the location where you installed VisualPHPUnit!
4. This should be enough for a basic installation.  However, you can set the default options for each test run by modifying a few more lines in config.php. 
    a. Change CREATE_SNAPSHOTS to *true* if you'd like to enable logging.  Logs are stored in the 'history' directory, though you can modify SNAPSHOT_DIRECTORY to point somewhere else if you please.  Please make note of the following:
        i. You will have to give the directory specified in SNAPSHOT_DIRECTORY the appropriate permissions in order to allow PHP to write to it.
        ii. The dropdown list under the 'Archives' section will only display the files within SNAPSHOT_DIRECTORY.
    b. Change SANDBOX_ERRORS to *true* if you'd like VPU to display any PHP errors after the test results.  If so, please make note of the following:
        i. The file specified in SANDBOX_FILENAME will always be empty (VPU wipes it at the end of each test run).  However, PHP still needs to be able to write to it, so ensure that the filename specified with SANDBOX_FILENAME has the appropriate permissions. 
        ii. Specific error types can be ignored using the SANDBOX_IGNORE setting.  Separate multiple error types with a '|' (e.g. 'E_STRICT|E_NOTICE').


Version Information
-------------------

Current stable release is v1.3.2, last updated on 30 April 2011.

Feedback
--------

Feel free to send any feedback you may have regarding this project to NSinopoli@gmail.com. 

Credits
-------

Special thanks to Matt Mueller (http://mattmueller.me/blog/), who came up with the initial concept, wrote the original code (https://github.com/MatthewMueller/PHPUnit-Test-Report), and was kind enough to share it.

Thanks to Mike Zhou, Hang Dao, and Thomas Ingham for their suggestions!
