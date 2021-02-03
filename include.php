<?php
$root = $_SERVER['DOCUMENT_ROOT'];
//include_once('include.php');
/*include("/snt/model/db.php");
include("model/model.php");
include('model/model_date.php');
*/
// вывести корень сайта
echo 'Корень сайта '.$_SERVER['DOCUMENT_ROOT'];

require_once "model/model.php";
require_once "model/db.php";
require_once "model/model_date.php";