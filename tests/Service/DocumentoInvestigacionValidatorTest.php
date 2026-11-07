<?php

namespace App\Tests\Service;

use App\Entity\Expediente;
use App\Entity\Investigacion;
use App\Entity\Libro;
use App\Service\DocumentoInvestigacionValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DocumentoInvestigacionValidatorTest extends TestCase
{
    public function testReturnsBookBelongingToInvestigation(): void
    {
        $book = new Libro();
        $investigation = $this->createMock(Investigacion::class);
        $investigation->method('getLibros')->willReturn(new ArrayCollection([$book]));
        $validator = $this->validatorReturning(Libro::class, $book);

        self::assertSame($book, $validator->libro(12, $investigation));
    }

    public function testRejectsBookNotBelongingToInvestigation(): void
    {
        $investigation = $this->createMock(Investigacion::class);
        $investigation->method('getLibros')->willReturn(new ArrayCollection());
        $validator = $this->validatorReturning(Libro::class, new Libro());

        $this->expectException(AccessDeniedHttpException::class);
        $validator->libro(12, $investigation);
    }

    public function testRejectsMissingBook(): void
    {
        $investigation = $this->createMock(Investigacion::class);
        $investigation->method('getLibros')->willReturn(new ArrayCollection());
        $validator = $this->validatorReturning(Libro::class, null);

        $this->expectException(AccessDeniedHttpException::class);
        $validator->libro(404, $investigation);
    }

    public function testReturnsExpedienteBelongingToInvestigation(): void
    {
        $expediente = new Expediente();
        $investigation = $this->createMock(Investigacion::class);
        $investigation->method('getExpedientes')->willReturn(new ArrayCollection([$expediente]));
        self::assertSame($expediente, $this->validatorReturning(Expediente::class, $expediente)->expediente(7, $investigation));
    }

    public function testRejectsExpedienteOutsideInvestigation(): void
    {
        $expediente = new Expediente();
        $investigation = $this->createMock(Investigacion::class);
        $investigation->method('getExpedientes')->willReturn(new ArrayCollection());
        $this->expectException(AccessDeniedHttpException::class);
        $this->validatorReturning(Expediente::class, $expediente)->expediente(7, $investigation);
    }

    private function validatorReturning(string $entityClass, $entity): DocumentoInvestigacionValidator
    {
        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $repository->method('find')->willReturn($entity);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with($entityClass)->willReturn($repository);

        return new DocumentoInvestigacionValidator($entityManager);
    }
}
