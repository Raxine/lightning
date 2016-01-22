<?php

require_once('utility/lib.php');

/**
 * 
 * @return string
 */
function install_guess_wwwroot() {
    $wwwroot = '';
    if (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') {
        $wwwroot .= 'http://';
    } else {
        $wwwroot .= 'https://';
    }
    $hostport = explode(':', $_SERVER['HTTP_HOST']);
    $wwwroot .= reset($hostport);
    if ($_SERVER['SERVER_PORT'] != 80 and $_SERVER['SERVER_PORT'] != '443') {
        $wwwroot .= ':' . $_SERVER['SERVER_PORT'];
    }
    $wwwroot .= $_SERVER['SCRIPT_NAME'];

    list($wwwroot, $xtra) = explode('/install.php', $wwwroot);

    return $wwwroot;
}

/**
 * 
 * @param object $dbconfigs
 * @param object $cfg
 * @return string
 */
function install_generate_configphp($dbconfigs, $cfg) {
    $configphp = '<?php' . PHP_EOL . PHP_EOL;

    $configphp .= 'unset($CFG);' . PHP_EOL;
    $configphp .= 'global $CFG, $DB, $OUTPUT;' . PHP_EOL;
    $configphp .= '$CFG = new stdClass();' . PHP_EOL; // prevent PHP5 strict warnings
    
    $configphp .= PHP_EOL;
    
    foreach ($dbconfigs as $key => $value) {
        $configphp .= '$CFG->' . $key . ' = ' . var_export($value, true) . ';' . PHP_EOL;
    }
    $configphp .= PHP_EOL;

    $configphp .= '$CFG->wwwroot = ' . var_export($cfg->wwwroot, true) . ';' . PHP_EOL ;
    $configphp .= '$CFG->dirroot = ' . var_export($cfg->dirroot, true) . ';' . PHP_EOL;
    
    $configphp .= PHP_EOL;
    
    $configphp .= '$CFG->dirmodel = ' . '$CFG->dirroot . ' . var_export('\\models\\', true) . ';' . PHP_EOL ;
    $configphp .= '$CFG->dircont = ' . '$CFG->dirroot . ' . var_export('\\controllers\\', true) . ';' . PHP_EOL ;
    $configphp .= '$CFG->libdir = ' . '$CFG->dirroot . ' . var_export('\\lib\\', true) . ';' . PHP_EOL ;
    $configphp .= '$CFG->dirrend = ' . '$CFG->dirroot . ' . var_export('\\renderers\\', true) . ';' . PHP_EOL ;
    $configphp .= '$CFG->dirviews = ' . '$CFG->dirroot . ' . var_export('\\views\\', true) . ';' . PHP_EOL ;
    $configphp .= '$CFG->dirlang = ' . '$CFG->dirroot . ' . var_export('\\lang\\', true) . ';' . PHP_EOL ;
    
    $configphp .= PHP_EOL;
    
    $configphp .= '$CFG->timeoutsession = 3600;' . PHP_EOL ;
    
    $configphp .= PHP_EOL;
    
    $configphp .= '//Models' . PHP_EOL;
    
    $configphp .= 'require_once($CFG->dirmodel . ' . var_export('database.php', true) .');' . PHP_EOL ;
    $configphp .= 'require_once($CFG->dirmodel . ' . var_export('model.php', true) .');' . PHP_EOL ;
    $configphp .= 'require_once($CFG->libdir . ' . var_export('utility/lib.php', true) .');' . PHP_EOL ;
    
    $configphp .= PHP_EOL;
    
    $configphp .= '//Controllers' . PHP_EOL;
    $configphp .= 'require_once($CFG->dircont . ' . var_export('modelcontroller.php', true) .');' . PHP_EOL ;
    
    $configphp .= PHP_EOL;
    
    $configphp .= '//renderers' . PHP_EOL;
    $configphp .= 'require_once($CFG->dirrend . ' . var_export('core_renderer.php', true) .');' . PHP_EOL ;
    
    $configphp .= PHP_EOL;
    
    $configphp .= '//upgrade' . PHP_EOL;
    $configphp .= 'require_once($CFG->libdir . ' . var_export('upgrade.php', true) .');' . PHP_EOL ;
    
    $configphp .= PHP_EOL;
    
    $configphp .= '$DB = new Database();' . PHP_EOL;
    $configphp .= '$OUTPUT = new core_renderer();' . PHP_EOL;
    
    $print = file_put_contents('config.php', $configphp);
    
    if($print !== false) {
        return true;
    }
    
    return false;
}

function create_dbconfig_table() {
    $dir = dirname( dirname(__FILE__) );
    require_once($dir . '/config.php');
    
    $table = 'config';
    $params = array(
            array('id', 'int(11)', 'NOT NULL', 'AUTO_INCREMENT'),
            array('name', 'varchar(255)', 'DEFAULT NULL'),
            array('value', 'varchar(255)', 'DEFAULT NULL'),
            array('PRIMARY KEY (id)'),
            array('UNIQUE KEY name (name)')
        );
    
    $newTable = $DB->createTable($table, $params);
    
    if($newTable) {
        
        setConfig('toto', 'titi');
        return true;
    }
}

function setConfig($name, $value) {
    global $DB;
    $DB->createOrUpdate('config', array('name' => $name, 'value' => $value));
}