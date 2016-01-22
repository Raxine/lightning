<?php

require_once('lib/installlib.php');

$CFG = new stdClass();
$CFG->wwwroot = install_guess_wwwroot();
$CFG->dirroot = dirname(__FILE__);
$CFG->dirlang = dirname(__FILE__) . '/lang/';

if (!empty($_POST['submit'])) {
    if (!empty($_POST['dbhost']) && !empty($_POST['dbname']) && !empty($_POST['dbuser'])) {

        $dbconfig = new stdClass();
        $dbconfig->dbhost = trim($_POST['dbhost']);
        $dbconfig->dbname = trim($_POST['dbname']);
        $dbconfig->dbuser = trim($_POST['dbuser']);
        $dbconfig->dbpass = trim($_POST['dbpass']);
        
        $cfg = new stdClass();
        $cfg->wwwroot = trim($CFG->wwwroot);
        $cfg->dirroot = trim($CFG->dirroot);
        
        $writeconfigphp = install_generate_configphp($dbconfig, $cfg);
        
        if($writeconfigphp) {
            $writeconfigdb = create_dbconfig_table();
            
            if($writeconfigdb) {
                header('Location: ' . $cfg->wwwroot);
                die;
            }
        }
    } else {
        $error_msg = 'Tous les champs requis ne sont pas remplis !';
    }
}

?>

<h2><?php echo get_string('welcome_install'); ?></h2>

<form action="" method="POST">
    <label for="wwwroot"><?php echo get_string('wwwroot_install'); ?></label>
    <input type="text" name="wwwroot" disabled="disabled" value="<?php echo $CFG->wwwroot ?>">
    
    <label for="dirroot"><?php echo get_string('dirroot_install'); ?></label>
    <input type="text" name="dirroot" disabled="disabled" value="<?php echo $CFG->dirroot ?>">
    
    <label for="dbhost"><?php echo get_string('dbhost_install'); ?></label>
    <input type="text" name="dbhost" required>
    
    <label for="dbname"><?php echo get_string('dbname_install'); ?></label>
    <input type="text" name="dbname" required>
    
    <label for="dbuser"><?php echo get_string('dbuser_install'); ?></label>
    <input type="text" name="dbuser" required>
    
    <label for="dbpass"><?php echo get_string('dbpass_install'); ?></label>
    <input type="text" name="dbpass">
    
    <input type="submit" name="submit" value="<?php echo get_string('submit_install'); ?>" />
</form>