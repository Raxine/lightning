<?php


$userid = required_param('userid', PARAM_INT);

if($userid) {
    echo 'Profile' . "<br>";
    echo $userid;
} else {
    echo 'Mauvais paramètre';
}

