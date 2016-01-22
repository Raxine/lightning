<?php

unset($CFG);
global $CFG, $DB, $OUTPUT;
$CFG = new stdClass();

$CFG->dbhost = 'localhost';
$CFG->dbname = 'framework';
$CFG->dbuser = 'root';
$CFG->dbpass = '';

$CFG->wwwroot = 'http://localhost/framework_php';
$CFG->dirroot = 'D:\\03_WORK\\wamp\\www\\framework_php';

$CFG->dirmodel = $CFG->dirroot . '\\models\\';
$CFG->dircont = $CFG->dirroot . '\\controllers\\';
$CFG->libdir = $CFG->dirroot . '\\lib\\';
$CFG->dirrend = $CFG->dirroot . '\\renderers\\';
$CFG->dirviews = $CFG->dirroot . '\\views\\';
$CFG->dirlang = $CFG->dirroot . '\\lang\\';

$CFG->timeoutsession = 3600;

//Models
require_once($CFG->dirmodel . 'database.php');
require_once($CFG->dirmodel . 'model.php');
require_once($CFG->dirmodel . 'string.php');

//Lib
require_once($CFG->libdir . 'utility/lib.php');

//Controllers
require_once($CFG->dircont . 'modelcontroller.php');

//renderers
require_once($CFG->dirrend . 'core_renderer.php');

//upgrade
require_once($CFG->libdir . 'upgrade.php');

//
$DB = new Database();
$OUTPUT = new core_renderer();
$STRING = new String();
