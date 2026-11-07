<?php

namespace App\Tests\Service;

use App\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderTest extends TestCase
{
    private $directories = [];
    private $sources = [];

    protected function tearDown(): void
    {
        foreach ($this->sources as $source) {
            if (is_file($source)) {
                unlink($source);
            }
        }
        foreach ($this->directories as $directory) {
            foreach (glob($directory . '/*') ?: [] as $file) {
                unlink($file);
            }
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
    }

    public function testStoresPdfWithRandomNameInPrivateTarget(): void
    {
        $target = sys_get_temp_dir() . '/sac-uploader-' . bin2hex(random_bytes(8));
        $this->directories[] = $target;
        $source = tempnam(sys_get_temp_dir(), 'sac-upload-');
        $this->sources[] = $source;
        file_put_contents($source, "%PDF-1.4\nfixture\n%%EOF");
        $upload = new UploadedFile($source, '../../carta.pdf', 'application/pdf', null, true);

        $name = (new FileUploader($target))->upload($upload);

        self::assertRegExp('/^[a-f0-9]{32}\.pdf$/', $name);
        self::assertFileExists($target . '/' . $name);
        self::assertFalse(file_exists($target . '/carta.pdf'));
    }

    public function testRejectsNonPdfUpload(): void
    {
        $target = sys_get_temp_dir() . '/sac-uploader-' . bin2hex(random_bytes(8));
        $this->directories[] = $target;
        $source = tempnam(sys_get_temp_dir(), 'sac-upload-');
        $this->sources[] = $source;
        file_put_contents($source, 'plain text');
        $upload = new UploadedFile($source, 'document.txt', 'text/plain', null, true);

        $this->expectException(\InvalidArgumentException::class);
        (new FileUploader($target))->upload($upload);
    }

    public function testRejectsFilesLargerThanTenMegabytes(): void
    {
        $target = sys_get_temp_dir() . '/sac-uploader-' . bin2hex(random_bytes(8));
        $this->directories[] = $target;
        $source = tempnam(sys_get_temp_dir(), 'sac-upload-');
        $this->sources[] = $source;
        $handle = fopen($source, 'wb');
        fwrite($handle, "%PDF-1.4\n");
        ftruncate($handle, FileUploader::MAX_FILE_SIZE + 1);
        fclose($handle);
        $upload = new UploadedFile($source, 'large.pdf', 'application/pdf', null, true);

        $this->expectException(\InvalidArgumentException::class);
        (new FileUploader($target))->upload($upload);
    }

    public function testAcceptsFileAtTenMegabyteBoundary(): void
    {
        $target = sys_get_temp_dir() . '/sac-uploader-' . bin2hex(random_bytes(8));
        $this->directories[] = $target;
        $source = tempnam(sys_get_temp_dir(), 'sac-upload-');
        $this->sources[] = $source;
        $handle = fopen($source, 'wb');
        fwrite($handle, "%PDF-1.4\n");
        ftruncate($handle, FileUploader::MAX_FILE_SIZE);
        fclose($handle);
        $upload = new UploadedFile($source, 'boundary.pdf', 'application/pdf', null, true);

        $name = (new FileUploader($target))->upload($upload);

        self::assertFileExists($target . '/' . $name);
        self::assertSame(FileUploader::MAX_FILE_SIZE, filesize($target . '/' . $name));
    }
}
