<?php

include realpath( __DIR__ . '/../../../src/Sifo' ) . '/Autoloader.php';
use Sifo\Autoloader;

/**
 * Test class for Autoloader.
 */
class AutoloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Autoloader
     */
    protected $object;
    /**
     * Real path to folder src/.
     */
    private $src_path;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->src_path = realpath( __DIR__ . '/../../../src' );
        $this->object = new Autoloader();
    }

    /**
     * Test object correct creation.
     */
    public function testObjectCreation()
    {
        $this->assertTrue( $this->object instanceof Autoloader );
        $this->assertRegExp( '@/src$@', $this->object->getBasePath() );
    }

    public function testGetBasePath()
    {
        $this->assertEquals( $this->src_path, $this->object->getBasePath() );
    }

    public function testGetFilePath()
    {
        $this->assertEquals( $this->object->getFilePath( 'Sifo\Metadata' ), $this->src_path . '/Sifo/Metadata.php' );
        $this->assertEquals( $this->object->getFilePath( '\Sifo\Metadata' ), $this->src_path . '/Sifo/Metadata.php' );
    }

    public function testVariations()
    {

        $autoloader = new Autoloader( 'Vendor', $this->src_path );
        $this->assertEquals(
            $autoloader->getFilePath( 'Vendor\Foo\Bar_Vaz' ),
            $this->src_path . '/Vendor/Foo/Bar/Vaz.php'
        );

        $autoloader = new Autoloader( 'Vendor', $this->src_path );
        $this->assertEquals(
            $autoloader->getFilePath( '\Vendor\Foo\Bar_Vaz' ),
            $this->src_path . '/Vendor/Foo/Bar/Vaz.php'
        );

        $autoloader = new Autoloader( 'vendor', $this->src_path );
        $this->assertEquals(
            $autoloader->getFilePath( '\vendor\namespace\package\Class_Name' ),
            $this->src_path . '/vendor/namespace/package/Class/Name.php'
        );
        $this->assertEquals(
            $autoloader->getFilePath( '\vendor\namespace\package_name\Class_Name' ),
            $this->src_path . '/vendor/namespace/package_name/Class/Name.php'
        );

    }

    public function testAutoload()
    {
        $this->assertNull( $this->object->autoload( 'Sifo\Metadata' ) );
        $this->assertNull( $this->object->autoload( 'Sifo\Controller' ) );
    }

    /**
     * Tests that an exception is thrown when an invalid class is requested.
     *
     * @expectedException \OutOfRangeException
     */
    public function testInvalidAutoload()
    {
        $this->object->autoload( 'Sifo\UNEXISTING' );
    }

    public function testRegister()
    {
        $autoload_functions_before = spl_autoload_functions();
        $this->object->register();
        $autoload_functions_after = spl_autoload_functions();

        // Autoload incremented in 1:
        $this->assertEquals( count( $autoload_functions_after ), count( $autoload_functions_before ) + 1 );

        // Autoload prepended (appears in the first place):
        $this->assertTrue( $autoload_functions_after[0][0] instanceof Autoloader );
        $this->assertEquals( $autoload_functions_after[0][1], 'autoload' );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object = null;
    }


}

