<?php

function trivial_autoload($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if(is_file($file)) require_once($file);
}

spl_autoload_register('trivial_autoload', true, false);