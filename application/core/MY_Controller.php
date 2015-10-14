<?php


$dir = __DIR__.'/controllers';
//Require once all controllers in sub-folder  "controllers"
foreach (glob($dir . '/*.php') as $controller) {
    require_once $controller;
}
