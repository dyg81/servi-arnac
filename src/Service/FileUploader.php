<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileUploader
{
    public const MAX_FILE_SIZE = 10000000;

    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $mimeType = $file->getMimeType();
        $allowedMimeTypes = ['application/pdf' => 'pdf'];
        if (!isset($allowedMimeTypes[$mimeType]) || $file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('El documento debe ser PDF y no superar 10 MB.');
        }

        $newFilename = bin2hex(random_bytes(16)) . '.' . $allowedMimeTypes[$mimeType];

        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0750, true) && !is_dir($this->targetDirectory)) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento privado de documentos.');
        }

        $file->move($this->getTargetDirectory(), $newFilename);

        return $newFilename;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
