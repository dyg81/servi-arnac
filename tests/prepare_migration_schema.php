<?php

$databaseUrl = getenv('DATABASE_URL') ?: ($_SERVER['DATABASE_URL'] ?? '');
if (!preg_match('#/main_test(?:\?|$)#', $databaseUrl)) {
    fwrite(STDERR, "Se requiere DATABASE_URL hacia la base aislada main_test.\n");
    exit(2);
}

require dirname(__DIR__) . '/config/bootstrap.php';

$kernel = new App\Kernel('test', false);
$kernel->boot();
$connection = $kernel->getContainer()->get('doctrine')->getConnection();
$existingTables = $connection->executeQuery('SHOW TABLES');
if (false !== $existingTables->fetchOne()) {
    fwrite(STDERR, "La base main_test no está vacía; reinicia la pila temporal sin -v antes de repetir el runner.\n");
    $kernel->shutdown();
    exit(2);
}

$connection->executeStatement('CREATE TABLE sac_sala_centro_laboral (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
$connection->executeStatement('CREATE TABLE sac_sala_clientes (id INT AUTO_INCREMENT NOT NULL, ocupacion VARCHAR(100) NOT NULL, PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
$connection->executeStatement('CREATE TABLE sac_registro_libros (id INT AUTO_INCREMENT NOT NULL, tomo VARCHAR(5) NOT NULL, PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

$kernel->shutdown();
