<?php
include('../conf.php');

$user = new DataBase();
 //$date = date("Y-m-d");
 $date = "2019-05-27"; //дата платежа
 $srok = "2020-12-20"; //срок оплаты
 echo $date."<br>";
//$user->interval($srok, $date);

//$user->start_date($srok);

function test($t1, $t2){
    $test = 0;
    $array = array("1"=>"10","2"=>"10","3"=>"10","4"=>"20","5"=>"20","6"=>"20","7"=>"20","8"=>"30","9"=>"30","10"=>"30","11"=>"30","12"=>"35","13"=>"35");
   
        foreach($array as $key =>$arr){
            while($t1 < $key){
                $t1++;
                $test = $t1 + $key;
            echo $t1." ".$arr."<br>";
        }
    }
    echo $test;
}

test(5, 12);
?>