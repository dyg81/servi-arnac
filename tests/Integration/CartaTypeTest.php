<?php

namespace App\Tests\Integration;

use App\Entity\Carta;
use App\Form\CartaType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\File;

class CartaTypeTest extends KernelTestCase
{
    public function testDocumentFieldRequiresPdfAndLimitsSizeToTenMegabytes(): void
    {
        self::bootKernel();
        $form = self::$container->get('form.factory')->create(CartaType::class, new Carta());
        $constraints = $form->get('documento_file')->getConfig()->getOption('constraints');
        $fileConstraint = null;

        foreach ($constraints as $constraint) {
            if ($constraint instanceof File) {
                $fileConstraint = $constraint;
                break;
            }
        }

        self::assertNotNull($fileConstraint);
        self::assertSame(FileUploader::MAX_FILE_SIZE, $fileConstraint->maxSize);
        self::assertSame(['application/pdf'], $fileConstraint->mimeTypes);
    }
}
