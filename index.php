<?php

if (!file_exists('./config.php')) {
    header('Location: install.php');
    die;
}

require_once('config.php');

session_start();

$base_url = $_SERVER['REQUEST_URI'];
$roads = array();
$roadsArray = explode('/', $base_url);
foreach ($roadsArray as $road) {
    if (!empty($road)) {
        $roads[] = $road;
    }
}

array_shift($roads);

if(empty($roads)) {
    require $CFG->dirviews . '/index.php';
} else {
    if ((count($roads) > 0)) {
        $lastValue = end($roads);
        $lastKey = key($roads);
        
        $filename = $CFG->dirviews;
        
        if($_SERVER['QUERY_STRING'] == "") {
            if (is_numeric($lastValue)) {
                $id = $roads[$lastKey];
            } else {
                $file = $lastValue;
            }
        } else {
            $httpparams = explode('?', $lastValue);
            $file = $httpparams[0];

            if(count($httpparams) > 0) {
                $allparams = explode('&', $httpparams[1]);
                foreach ($allparams as $value) {
                    $params = explode('=', $value);
                    $$params[0] = $params[1];
                }
            }
        }
        
        for ($i = 0; $i < (count($roads) - 1); $i++) {
            $filename .= $roads[$i] . '/';
        }
        
        $filename = substr($filename, 0, -1);
        if(!empty($file)) {
            $filename .= '/' . $file . '.php';
        } else {
            $filename .= '.php';
        }
        
        if (file_exists($filename)) {
            require $filename;
        } else {
            require $CFG->dirviews . '/error404.php';
        }
    }
}