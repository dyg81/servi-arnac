<?php

namespace App\Service;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Throwable;

/**
 * Classifies the database errors that CRUD actions translate into user messages.
 */
final class PersistenceExceptionClassifier
{
    public static function isUniqueConstraintViolation(Throwable $exception): bool
    {
        return self::matches($exception, UniqueConstraintViolationException::class, ['1062', '23505']);
    }

    public static function isForeignKeyConstraintViolation(Throwable $exception): bool
    {
        return self::matches($exception, ForeignKeyConstraintViolationException::class, ['1451', '1452', '23503']);
    }

    private static function matches(Throwable $exception, string $exceptionClass, array $driverCodes): bool
    {
        for ($current = $exception; $current !== null; $current = $current->getPrevious()) {
            if ($current instanceof $exceptionClass) {
                return true;
            }

            if (method_exists($current, 'getErrorCode') && in_array((string) $current->getErrorCode(), $driverCodes, true)) {
                return true;
            }

            if (in_array((string) $current->getCode(), $driverCodes, true)) {
                return true;
            }
        }

        return false;
    }
}
