<?php

require __DIR__ . '/../vendor/autoload.php';

use Ratchet\App;
use App\Controllers\ChatController;

$app = new App('localhost', 8080);  // Ganti dengan alamat dan port yang diinginkan
$app->route('/chat', new ChatController, ['*']);
$app->run();
