<?php
// api/index.php - Vercel Serverless Function Front Controller Entrypoint

chdir(dirname(__DIR__));

require_once __DIR__ . '/../public/index.php';
