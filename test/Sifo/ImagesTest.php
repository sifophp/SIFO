<?php

namespace Sifo\Test\Sifo;

use org\bovigo\vfs\vfsStream;
use Sifo\Images;
use PHPUnit\Framework\TestCase;

final class ImagesTest extends TestCase
{
    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $imagesDir;

    protected function setUp(): void
    {
        $this->imagesDir = vfsStream::setup('images');
    }

    public function testDefaultResizeAndSave(): void
    {
        Images::resizeAndSave(
            'test/Sifo/publicdomain.png',
            vfsStream::url('images/output.png'),
            100,
            100
        );

        $this->assertTrue($this->imagesDir->hasChild('images/output.png'));
        $imageSize = getimagesize(vfsStream::url('images/output.png'));
        $this->assertSame(100, $imageSize[0]);
        // There isn't any image transformation, we expect 35px height because it is the relative height for
        // the given image's with 100px width.. See next test ;-D.
        $this->assertSame(35, $imageSize[1]);
    }

    public function testCropResizeAndSave(): void
    {
        Images::resizeAndSave(
            'test/Sifo/publicdomain.png',
            vfsStream::url('images/output.png'),
            100,
            100,
            [
                'x' => 100,
                'y' => 100,
            ]
        );

        $this->assertTrue($this->imagesDir->hasChild('images/output.png'));
        $imageSize = getimagesize(vfsStream::url('images/output.png'));
        $this->assertSame(100, $imageSize[0]);
        $this->assertSame(100, $imageSize[1]);
    }

    public function testCropAndSave(): void
    {
        Images::cropAndSave(
            'test/Sifo/publicdomain.png',
            vfsStream::url('images/output.png'),
            100,
            0,
            100,
            100
        );

        $this->assertTrue($this->imagesDir->hasChild('images/output.png'));
        $imageSize = getimagesize(vfsStream::url('images/output.png'));
        $this->assertSame(100, $imageSize[0]);
        $this->assertSame(100, $imageSize[1]);
    }

    public function testUploadResizeAndSave(): void
    {
        $file = vfsStream::url('images/tmp.png');
        file_put_contents($file, file_get_contents(__DIR__ . '/publicdomain.png'));
        $this->assertTrue($this->imagesDir->hasChild('images/tmp.png'));

        TestImages::uploadResizeAndSave(
            [
                'name' => 'output.png',
                'tmp_name' => $file,
            ],
            vfsStream::url('images/output.png'),
            100,
            100
        );

        $this->assertTrue($this->imagesDir->hasChild('images/output.png'));
        $imageSize = getimagesize(vfsStream::url('images/output.png'));
        $this->assertSame(100, $imageSize[0]);
        $this->assertSame(35, $imageSize[1]);

    }
}
