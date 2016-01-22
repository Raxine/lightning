<?php

/**
 * 
 * @global type $CFG
 * @param string $str
 * @param string $lang
 * @param string $component
 * @return string
 */
function get_string($str, $lang = 'en', $component = 'core') {
    global $CFG;
    $string = array();
    if (!file_exists($CFG->dirlang . $lang . '/' . $component . '.php')) {
        print_object('File does not exist');
    } else {
        include($CFG->dirlang . $lang . '/' . $component . '.php');
        if (empty($str)) {
            print_object('String can\'t be empty');
        } else {
            return $string[$str];
        }
    }
}