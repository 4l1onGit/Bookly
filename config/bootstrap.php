<?php

use Symfony\Component\Dotenv\Dotenv;

$envFile = dirname(__DIR__) . '/.env';

if (file_exists($envFile)) {
    (new Dotenv())->bootEnv($envFile);
}