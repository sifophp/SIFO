<?php
/**
 * LICENSE
 *
 * Copyright 2010 Albert Lombarte
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Sifo;

/**
 * Old-school Directory class.
 *
 * TODO: Migrate to SPL when PHP 5.3 is found easily on shared hostings.
 * Directory interaction for those not being able to use RecursiveDirectoryIterator
 * @deprecated Use Directory.php for new implementations.
 */
class Dir
{
	/**
	 * List of files and dires skipped in scans.
	 *
	 * @var array
	 */
	protected $ignored_files = array( '.', '..', '.svn', '.git', '.DS_Store', 'Thumbs.db', '_smarty' );

	/**
	 * Returns an array containing a list of files found recursively for a given path.
	 *
	 * Every item in the array has a subarray containing the following keys:
	 * "name" => Filename or Dirname
	 * "relative" => Path relative to the starting point
	 * "absolute" => Absolute path
	 * "folder" => Parent folder
	 *
	 * @param string $base_path Path to starting directory.
	 * @param string $relative_path Relative path added to base_path. If you have /path/to/dir and specify "dir" as relative_path then will list files in /path/to as dir/file1, dir/file2 ...
	 */
	public function getFileListRecursive( $base_path, $relative_path = "" )
	{
		// If base path ends in / remove it.
		if ( substr( $base_path, -1, 1 ) == "/" )
		{
			$base_path = substr( $base_path, 0, -1 );
		}
		// If relative path starts in / remove it.
		if ( substr( $relative_path, 0, 1 ) == "/" )
		{
			$relative_path = substr( $relative_path, 1 );
		}

		$path = $base_path . "/" . $relative_path;

		if ( !is_dir( $path ) )
		{
			return false;
		}

		$list = array();

		$directory = opendir( "$path" );

		while ( $file = readdir( $directory ) )
		{
			if ( !in_array( $file, $this->ignored_files ) )
			{
				$f = $path . "/" . $file;
				$f = preg_replace( '/(\/){2,}/', '/', $f ); // Replace double slashes.
				if ( is_file( $f ) )
				{
					$list[] = array( "filename" => $file, "relative" => $relative_path . "/$file", "absolute" => $f, "folder"=> $relative_path );
				}

				if ( is_dir( $f ) ) // Ignore _smarty dir
				{
					$list = array_merge( $list, $this->getFileListRecursive( $base_path, $relative_path . "/$file" ) ); // Recursive call.
				}
			}
		}
		closedir( $directory );
		sort( $list );
		return $list ;
	}

	/**
	 * Get subdirs
	 *
	 * @param string $path
	 * @param string $relative_path
	 * @return array
	 */
	public function getDirs( $path )
	{
		if ( !is_dir( $path ) )
		{
			return false;
		}

		$list = array();

		$directory = opendir( "$path" );

		while ( $file = readdir( $directory ) )
		{
			if ( !in_array( $file, $this->ignored_files ) )
			{
				$f = $path . "/" . $file;
				$f = preg_replace( '/(\/){2,}/', '/', $f ); // Replace double slashes.

				if ( is_dir( $f ) )
				{
					$list[] = $file;
				}
			}
		}
		closedir( $directory );
		sort( $list );

		return $list;
	}

	public function setIgnore( Array $ignored_files )
	{
		$this->ignored_files = $ignored_files;
	}

}