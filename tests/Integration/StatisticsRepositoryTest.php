<?php

namespace App\Tests\Integration;

use App\Entity\Certificacion;
use App\Entity\Expediente;
use App\Entity\Fondo;
use App\Entity\Investigacion;
use App\Entity\Libro;
use App\Entity\Reprografia;
use App\Entity\Transcripcion;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StatisticsRepositoryTest extends KernelTestCase
{
    public function testMonthlyCountsMatchTheExistingMonthlyResultSets(): void
    {
        self::bootKernel();
        $manager = self::$container->get('doctrine')->getManager();
        $month = new DateTime('first day of this month 00:00:00');
        $previousMonth = (clone $month)->modify('-1 month');

        $monthlyMethods = [
            Investigacion::class => 'obtenerInvestigacionesPorMes',
            Transcripcion::class => 'obtenerTranscripcionesPorMes',
            Reprografia::class => 'obtenerReprografiasPorMes',
            Certificacion::class => 'obtenerCertificacionesPorMes',
        ];

        foreach ($monthlyMethods as $entityClass => $method) {
            $repository = $manager->getRepository($entityClass);
            self::assertSame(count($repository->$method($previousMonth)), $repository->contarPorMes($previousMonth));
            self::assertSame(count($repository->$method($month)), $repository->contarPorMes($month));
        }
    }

    public function testGroupedReportCountsMatchTheExistingPerPairQueries(): void
    {
        self::bootKernel();
        $manager = self::$container->get('doctrine')->getManager();

        foreach ([Expediente::class, Libro::class] as $entityClass) {
            $repository = $manager->getRepository($entityClass);
            $groupedCounts = [];

            foreach ($repository->contarPorFondoYDeposito() as $row) {
                $groupedCounts[$row['fondo_id'] . ':' . $row['deposito_id']] = (int) $row['cantidad'];
            }

            $oldMethod = $entityClass === Expediente::class
                ? 'obtenerExpedientesPorFondoYDeposito'
                : 'obtenerLibrosPorFondoYDeposito';
            $oldTotal = 0;

            foreach ($manager->getRepository(Fondo::class)->findAll() as $fondo) {
                foreach ($fondo->getDepositos() as $deposito) {
                    $key = $fondo->getId() . ':' . $deposito->getId();
                    $oldCount = count($repository->$oldMethod($fondo->getId(), $deposito->getId()));
                    $oldTotal += $oldCount;
                    self::assertSame($oldCount, $groupedCounts[$key] ?? 0);
                }
            }

            self::assertSame($oldTotal, array_sum($groupedCounts));
        }
    }
}
