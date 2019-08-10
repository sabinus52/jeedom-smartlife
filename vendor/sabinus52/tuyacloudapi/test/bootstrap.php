<?php

require __DIR__.'/../vendor/autoload.php';
includeAll(__DIR__.'/../src');


function includeAll($dir) {
    $f = scandir($dir);
    foreach ($f as $f2) {
        if ($f2 == "." || $f2 == "..") continue;
        if (is_dir($dir . DIRECTORY_SEPARATOR . $f2)) {
            includeAll($dir . DIRECTORY_SEPARATOR . $f2);
        } else {
            if (substr($f2, -4) == ".php") {
                include_once($dir . DIRECTORY_SEPARATOR . $f2);
            }
        }
    }
}
