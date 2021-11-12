<?php

require 'bootstrap.php';

use App\Controllers\IndexController;

$token = getenv('TOKEN');
$indexController = new IndexController($token);
$indexController->execute();
