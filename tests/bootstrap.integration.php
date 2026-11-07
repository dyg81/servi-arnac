<?php

$databaseUrl = getenv('DATABASE_URL');
if (!$databaseUrl || !preg_match('#/(?:[a-z0-9_]*test[a-z0-9_]*)(?:\?|$)#i', $databaseUrl)) {
    fwrite(STDERR, "La suite de integración requiere DATABASE_URL con un nombre de base que incluya 'test'.\n");
    exit(2);
}

require __DIR__ . '/bootstrap.php';
