<?php
require_once __DIR__ . '/core/Bootstrap.php';

$router = new Router();
require_once __DIR__ . '/routes/web.php';
$router->dispatch();
