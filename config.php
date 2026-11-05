<?php
declare(strict_types=1);
return [
 'app_name' => 'FACULDADE | Gestão Acadêmica',
 'base_url' => 'http://localhost/faculdade_php', // Ajuste se renomear a pasta no www
 'db' => [
   'host' => getenv('FAC_DB_HOST') ?: '127.0.0.1',
   'port' => getenv('FAC_DB_PORT') ?: '3306',
   'name' => getenv('FAC_DB_NAME') ?: 'faculdade',
   'user' => getenv('FAC_DB_USER') ?: 'root',
   'pass' => getenv('FAC_DB_PASS') ?: '',
 ],
];
