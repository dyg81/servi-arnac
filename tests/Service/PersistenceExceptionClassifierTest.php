<?php

namespace App\Tests\Service;

use App\Service\PersistenceExceptionClassifier;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PersistenceExceptionClassifierTest extends TestCase
{
    public function testItRecognizesKnownDriverConstraintCodesThroughPreviousExceptions(): void
    {
        $duplicate = new RuntimeException('duplicate', 1062);
        $wrappedDuplicate = new RuntimeException('flush failed', 0, $duplicate);
        $referenced = new RuntimeException('referenced row', 1451);
        $unknown = new RuntimeException('connection failure', 2002);

        self::assertTrue(PersistenceExceptionClassifier::isUniqueConstraintViolation($wrappedDuplicate));
        self::assertFalse(PersistenceExceptionClassifier::isForeignKeyConstraintViolation($wrappedDuplicate));
        self::assertTrue(PersistenceExceptionClassifier::isForeignKeyConstraintViolation($referenced));
        self::assertFalse(PersistenceExceptionClassifier::isUniqueConstraintViolation($unknown));
        self::assertFalse(PersistenceExceptionClassifier::isForeignKeyConstraintViolation($unknown));
    }
}
