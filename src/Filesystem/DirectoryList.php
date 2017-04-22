<?php

namespace Sifo\Filesystem;

use Sifo\Filesystem;

/**
 * Basic listings of directories using SPL.
 *
 * For complex and very specific needs you should use and extend your own iterators.
 * This class replaces "Dir" although there is no backwards compatibility.
 *
 * ------
 * Examples of usage:
 *
 * $dir = new \Sifo\Filesystem\DirectoryList();
 *
 * // Recursive list of all files including dirs:
 * $iterator = $dir->getRecursiveList( $path );
 *
 * // Recursive list of PHP and HTML files (hiding dirs)
 * $iterator = $dir->getRecursiveList( $path, false, array( 'php', 'html' ) );
 *
 * // List of immediate files and dirs (non recursive):
 * $iterator = $dir->getList( $path );
 *
 *
 * Then you can iterate the result $iterator in a foreach ( $path => $DirectoryIterator )
 * where $DirectoryIterator has many interesting methods such as:
 * ->getATime()
 * ->getBasename ( $ending_part_to_remove )
 * ->getCTime()
 * ->getExtension()
 * ->getFilename()
 * ->getGroup()
 * ->getInode()
 * ->getMTime()
 * ->getOwner()
 * ->getPath()
 * ->getPathname()
 * ->getPerms()
 * ->getSize()
 * ->getType()
 * ->isDir()
 * ->isDot()
 * ->isExecutable()
 * ->isFile()
 * ->isLink()
 * ->isReadable()
 * ->isWritable()
 * ->valid()
 *
 * @see http://www.php.net/manual/en/spl.iterators.php
 */
class DirectoryList
{

    /**
     * Returns the list of *immediate* files [isFile()] and folders [isDir()] on the given path.
     *
     * Non-recursive function.
     *
     * @param string $path Directory where you want to start parsing.
     * @param array  $valid_extensions
     *
     * @return \IteratorIterator
     */
    public function getList($path, $valid_extensions = array())
    {
        // Using RecursiveDirectoryIterator because compared to DirectoryIterator this one removes dot folders:
        $recursive_dir_iterator = new \RecursiveDirectoryIterator($path);
        $recursive_dir_iterator->setFlags(\RecursiveDirectoryIterator::SKIP_DOTS);

        if (!empty($valid_extensions))
        {
            return new Filesystem\FilterFilesByExtensionIterator(
                new \IteratorIterator($recursive_dir_iterator), $valid_extensions
            );
        }
        else
        {

            return new \IteratorIterator($recursive_dir_iterator);
        }
    }

    /**
     * Recursive listing of directory, files and dirs. If a set of extensions is
     * passed only matching files will be returned (do not pass the dot in the extensions)
     *
     * @param string $path             Directory where you want to start parsing.
     * @param bool   $accept_directories
     * @param array  $valid_extensions List of accepted extensions in the list, empty array for no filtering.
     *
     * @return \RecursiveIteratorIterator|\Sifo\Filesystem\FilterFilesByExtensionIterator ratorIterator of RecursiveDirectoryIterator
     */
    public function getRecursiveList($path, $accept_directories = true, $valid_extensions = array())
    {
        $mode                   = $this->_getIteratorMode($accept_directories);
        $recursive_dir_iterator = new \RecursiveDirectoryIterator($path);
        $recursive_dir_iterator->setFlags(\RecursiveDirectoryIterator::SKIP_DOTS);

        if (!empty($valid_extensions))
        {
            return new Filesystem\FilterFilesByExtensionIterator(
                new \RecursiveIteratorIterator(
                    $recursive_dir_iterator, $mode, \RecursiveIteratorIterator::CATCH_GET_CHILD
                ), $valid_extensions
            );
        }
        else
        {
            return new \RecursiveIteratorIterator($recursive_dir_iterator, $mode, \RecursiveIteratorIterator::CATCH_GET_CHILD);
        }
    }

    private function _getIteratorMode($accept_directories)
    {
        /**
         * Available modes for RecursiveIteratorIterator:
         *
         * RecursiveIteratorIterator::LEAVES_ONLY (don't show folders, default)
         * RecursiveIteratorIterator::SELF_FIRST (show parent folders before children)
         * RecursiveIteratorIterator::CHILD_FIRST (show parent folders after children, unusual)
         *
         * Flags (set in flags, not in mode):
         * RecursiveIteratorIterator::CATCH_GET_CHILD = Catches exceptions in getChildren() call
         */
        return ($accept_directories ? \RecursiveIteratorIterator::SELF_FIRST : \RecursiveIteratorIterator::LEAVES_ONLY);
    }

}
