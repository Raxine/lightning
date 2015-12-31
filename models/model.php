<?php

class Model {

    protected $id;

    /**
     * 
     * @return $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @global type $DB
     * @param int $id
     */
    public function load($id) {
        global $DB;
        $this->id = $id;
        $class = strtolower(get_called_class());
        $dbInfos = $DB->getRecord($class, array('id' => $this->id));
        foreach ($dbInfos as $attribute => $value) {
            $this->{$attribute} = $value;
        }
    }

    /**
     * 
     * @global type $DB
     * @return $id
     */
    public function save() {
        global $DB;
        $class = strtolower(get_called_class());
        $vars = get_object_vars($this);
        $this->id = $DB->createOrUpdate($class, $vars);
        return $this->id;
    }

    /**
     * 
     * @param type $id
     */
    public static function get($id) {
        $class = strtolower(get_called_class());
        $object = new $class;
        $object->load($id);
        return $object;
    }

    /**
     * 
     * @global type $CFG
     * @param int $id
     * @param string $class
     * @param string $action
     */
    public static function writeToLog($id, $class, $action) {
        global $CFG;
        $file = $CFG->dirroot . '\log.txt';
        date_default_timezone_set('Europe/Paris');
        $datetime = date("Y-m-d H:i:s");
        $message = $datetime . ': user[' . $id . '] => ' . $class . ' => ' . $action . "\n";
        file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
    }

}
