<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

define('ROOT_DIR', basename(dirname(__FILE__)));

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
