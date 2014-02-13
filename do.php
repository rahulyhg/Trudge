<?php

    $action = $_POST['do'];
    $path   = $_POST['path'];
    $value  = $_POST['value'];
    
    switch($action) {
        case 'save':
            file_put_contents($path, $value);
            break;
        case 'mkdir':
            mkdir($path . $value);
            break;
    }