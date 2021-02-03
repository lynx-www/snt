<?php
$puth =  $_SERVER['DOCUMENT_ROOT'].'/snt';

//include_once('../model/model.php');
include_once($puth.'/include.php');
$user = new db();
$date = new Date();
echo $date->my_date();
$sql = "SELECT * FROM peny";
$select = $user->select($sql);
var_dump($select);

