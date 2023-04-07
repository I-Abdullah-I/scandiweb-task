<?php

spl_autoload_register(function($classname){
    $path = __DIR__ . "/../" . str_replace("\\", "/", $classname) . ".php";

    require $path;
});
