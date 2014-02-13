<?php
    
    $path = $_GET['path'];

    header("Content-Type: text/plain");
    
    echo(file_get_contents($path));