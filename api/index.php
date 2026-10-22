<?php
// Point d'entrée Vercel : renvoie toutes les requêtes vers le front controller Symfony
chdir(dirname(__DIR__));
$_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__) . '/public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
require dirname(__DIR__) . '/public/index.php';