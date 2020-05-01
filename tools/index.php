<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
//echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";
//session_start();
//$_SESSION['browser'] = $_SERVER['HTTP_USER_AGENT'];
//print_r($_SERVER);
$options = ['customize_logo_hover_title'=>'Value In Array'];
//$options = [];
//$options = null;
if (empty($options['customize_logo_hover_title']) == false) {
    echo $options["customize_logo_hover_title"];
} else {
    echo "Hello Text";
}