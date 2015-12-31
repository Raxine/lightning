<?php

class ModelController {

    /**
     * 
     * @global type $DB
     * @return Object[]
     */
    public static function getAll($class) {
        global $DB;
        $array = array();
        $entrys = $DB->getRecords($class);
        foreach ($entrys as $entry) {
            $object = ucfirst($class);
            $array[$entry->id] = $object::get($entry->id);
        }
        return $array;
    }

    /**
     * 
     * @global type $DB
     * @param type $class
     * @return attributes[]
     */
    public static function getAllAttributes($class) {
        global $DB;
        $results = $DB->getTableColumns($class);
        return $results;
    }

    /**
     * 
     * @global type $CFG
     * @param type string
     * @return $string[]
     */
    public static function getTranslate($class, $str = '') {
        global $CFG;
        $string = array();
        if (!file_exists($CFG->dirroot . "/lang/" . $class . "_strings.php")) {
            print_r('No file!');
        } else {
            include($CFG->dirroot . "/lang/" . $class . "_strings.php");
            if (empty($str)) {
                return $string;
            } else {
                return $string[$str];
            }
        }
    }
}
