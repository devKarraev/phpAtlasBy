<?php

require 'vendor/autoload.php';

use Packages\DotEnv\DotEnv;

// Load env variables from .env file
(new DotEnv(__DIR__ . '/.env'))->load();
