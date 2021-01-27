<?php
include('../conf.php');

$user = new DataBase();
 //$date = date("Y-m-d");
 $date = "2020-05-27"; //дата платежа
 $srok = "2019-12-20"; //срок оплаты
 echo $date."<br>";
//$user->interval($srok, $date);

//$user->start_date($srok);

function test($t1, $t2){
    $porog = "2020-10-10";
    $date1 = "2020-01-01";
    $test = 0;
    $tt = $t1;
    $array = array("1"=>"10","2"=>"10","3"=>"10","4"=>"20","5"=>"20","6"=>"20","7"=>"20","8"=>"30","9"=>"30","10"=>"30","11"=>"30","12"=>"35","13"=>"35");
    $date = new DateTime($date1);
        foreach($array as $key =>$arr){
            while($date1 < $porog){
                $tt++;
                $test = $tt + $key;
            echo $tt." ".$arr."<br>";
           
            
            $date->add(new DateInterval('P1D')); // P1D means a period of 1 day
            echo $Date2 = $date->format('Y-m-d');
           }

        }
    
    echo $test;
}

test(5, 12);
?>