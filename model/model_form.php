<?php

class Form{

    public function form($plot, $date){
        
        
        $today = date('Y-m-d');
        if((!isset($date)) || $date == ''){
            $date = $today;
        }
        else{
            
        }
        return $date;
    }
}

?>