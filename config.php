<?php

unset($CFG);
global $CFG, $DB;
$CFG = new stdClass();

$CFG->dbhost = 'localhost';
$CFG->dbname = 'framework';
$CFG->dbuser = 'root';
$CFG->dbpass = '';

$CFG->wwwroot = 'http://localhost/framework_php';
$CFG->dirroot = 'D:\03_WORK\wamp\www\framework_php';

$CFG->dirmodelroot = $CFG->dirroot . '/models/';
$CFG->dircontroot = $CFG->dirroot . '/controllers/';
$CFG->dirrendroot = $CFG->dirroot . '/renderers/';
$CFG->dirviewsroot = $CFG->dirroot . '/views/';

$CFG->timeout = 3600;

require_once('database.php');


//models
require_once($CFG->dirmodelroot . 'model.php');
require_once($CFG->dirmodelroot . 'user.php');

//controllers
require_once($CFG->dircontroot . 'modelcontroller.php');

//renderers
require_once($CFG->dirrendroot . 'core_renderer.php');

$DB = new Database();
$OUTPUT = new core_renderer();