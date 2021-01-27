<?php
include('conf.php');
$dolg = new DataBase();
$sql = "SELECT * FROM street, sector WHERE street.id = sector.street AND sector.status = 1 AND sector.id LIKE '001'";
$dolgs = $dolg->select_sql($sql);
echo '<table class="table table-sm table-striped table-reflow">';


foreach($dolgs as $d){
    echo '<thead class="thead">';
    echo '<tr><th style="background: #ccc;">Участок '.$d['id'].'</th><th colspan="5" style="background: #ccc;">'.$d['name'].'</th></tr>';
    //echo '<tr><th>Наименование</th><th>Сумма к оплате</th></tr>';
   echo '</thead>';
    $opl = $dolg->one('plot', 'oplats1', $d['id']);
    foreach($opl as $o){
        //echo '<tbody>';
        echo '<tr><th>'.$o['name'].'</th><th>Сумма к оплате '.$o['pay'].'</th><th>Оплатить до '.$o['date_opl'].'</th>
        <th>Просрочено дней</th><th>Реффинансирование</th><th>Пени</th></tr>';
      //  $pay = "SELECT * FROM `payment` WHERE plot LIKE '".$d['id']."' AND name = '".$o['name']."'";
      //  echo $pay;
      $pay = "SELECT oplats.plot as o_plot, oplats.name as o_name, 
      oplats.area as o_area, oplats.pay as o_pay, oplats.date_opl as o_date, cast(payment.date as Date) as Date, payment.* FROM oplats 
      LEFT JOIN payment USING(plot, name) WHERE `oplats`.`plot` LIKE '".$d['id']."' AND oplats.name = '".$o['name']."' ORDER BY `oplats`.`name` ASC ";
        $pays = $dolg->select_sql($pay);
       
      foreach($pays as $p){
         
          $my_diff = $dolg->col_days($o['date_opl'], $p['date']);
       
        echo '<tr><td color="red">Оплата </td><td>'.$p['pay'].'</td><td>Дата оплаты: '.$p['date'].'</td>
        <td>'.round($my_diff, 0).'</td><td></td></tr>';
      }  
      $sqls = "SELECT sum(pay) as pays FROM payment WHERE plot = '".$d['id']."' AND name = '".$o['name']."' GROUP BY name";
      $itog = $dolg->one_param($sqls);
      $ds = $dolg->sum_dolg($o['pay'], $itog);
      
      echo '<tr><td color="red">Итого оплачено </td><td>'.$itog.'</td><td></td></tr>';
      echo '<tr><td color="red">Долг </td><td>'.$ds.'</td><td></td></tr>';
    }
    
}

echo '</table>';

?>