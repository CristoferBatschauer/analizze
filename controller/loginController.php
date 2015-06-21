<?php
$action = $_GET['action'];
require ('../_config.php');
require ('../class/User.php');
$user = new User();

$ret = $user->login($login, $senha);

if ($ret['result'] == 1)   echo "SUCESS";
else   echo "ERROR";
