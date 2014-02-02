<?php

/**
 * LICENSE
 *
 * Copyright 2014 Albert Lombarte
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Sifo;

class Autoloader
{
    /**
     * The initial directory where all files will be taken from. That should point to the src/ directory.
     */
    private $base_path;
    private $vendor;
    private $namespaceLength;

    /**
     * The Autoloader will resolve a full-qualified class name to its physical path.
     *
     * @param null $vendor
     * @param null $base_path This is the starting path to resolve namespaces, defaults to the src/ folder.
     */
    public function __construct( $vendor = null, $base_path = null )
    {
        $this->base_path = ( null === $base_path ? realpath( __DIR__ . '/../' ) : $base_path );
        $this->vendor = ( null === $vendor ? __NAMESPACE__ : ltrim( $vendor, '\\' ) );
        $this->namespace_separator = '\\';

        $this->namespaceLength = strlen( $this->vendor );
    }

    /**
     * Registers the autoloader class with the PHP SPL autoloader.
     *
     * @param bool $prepend Prepend this autoloader to the stack.
     */
    public function register( $prepend = true )
    {
        spl_autoload_register( array( $this, 'autoload' ), true, $prepend );
    }

    /**
     * Path where the namespace resolution starts from.
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->base_path;
    }

    /**
     * Loads a fully qualified class name from disk.
     *
     * @param string $class_name
     * @throws \OutOfRangeException When the requested class name does not resolve.
     */
    public function autoload( $class_name )
    {
        $filePath = $this->getFilePath( $class_name );

        if ( $filePath && is_file( $filePath ) )
        {
            require $filePath;
        } else
        {
            throw new \OutOfRangeException( "Requested class name $class_name does not resolve to a physical path" );
        }
    }

    public function getFilePath( $class_name )
    {
        $class_name = ltrim( $class_name, '\\' );
        if ( 0 === strpos( $class_name, $this->vendor ) )
        {
            $fileName = '';
            $namespace = '';
            if ( false !== ( $lastNsPos = strripos( $class_name, $this->namespace_separator ) ) )
            {

                $namespace = substr( $class_name, 0, $lastNsPos );
                $class_name = substr( $class_name, $lastNsPos + 1 );
                $fileName = str_replace(
                        $this->namespace_separator,
                        DIRECTORY_SEPARATOR,
                        $namespace
                    ) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';
            $filePath = $this->base_path . DIRECTORY_SEPARATOR . $fileName;

            return $filePath;
        }

        return false;

    }
}