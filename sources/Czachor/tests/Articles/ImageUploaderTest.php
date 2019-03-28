<?php
declare(strict_types=1);

namespace Czachor\Tests\Articles;

use Czachor\Files\ImageUploader;
use PHPUnit\Framework\TestCase;

final class ImageUploaderTest extends TestCase
{
    public function testUpload(): void
    {
        $test_file = __DIR__ . '/test-image.png'; // test file, must exists

        $obj_uploader = new ImageUploader('data:' . file_get_contents($test_file), 'test-image-uploaded');
        $obj_uploader->setFileDestination(__DIR__ . '/');
        $obj_uploader->setConvertToJpeg(true); // for the sake of clarity, default is true
        $result = $obj_uploader->upload(true);

        $this->assertTrue($result);
        $this->assertEquals('test-image-uploaded.jpg', $obj_uploader->getUploadedFileName());
        $this->assertFileExists($obj_uploader->getUploadedFilePathname());

        $this->assertTrue($obj_uploader->delete());
    }
}
