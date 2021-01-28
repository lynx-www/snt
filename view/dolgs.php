<?php
include('conf.php');
$dolg = new DataBase();
$sql = "SELECT * FROM street, sector WHERE street.id = sector.street AND sector.status = 1 AND plot LIKE '002'";
$dolgs = $dolg->select_sql($sql);
$itog_dolg = 0;
$itog_peny = 0;
echo '<table class="table table-sm table-striped table-reflow">';


foreach($dolgs as $d){
    echo '<thead class="thead">';
    echo '<tr><th style="background: #ccc;">Участок '.$d['id'].'</th><th colspan="5" style="background: #ccc;">'.$d['name'].'</th></tr>';
    //echo '<tr><th>Наименование</th><th>Сумма к оплате</th></tr>';
   echo '</thead>';
    $opl = $dolg->one('plot', 'oplats', $d['id']);
    foreach($opl as $o){
        //echo '<tbody>';
        echo '<tr><th>'.$o['name'].'</th>
        <th>Сумма к оплате '.$o['pay'].'</th>
        <th>Оплатить до '.$o['date_opl'].'</th>
        <th>Просрочено дней</th><th>Пени</th></tr>';
      $pay = "SELECT oplats.plot as o_plot, oplats.name as o_name, 
      oplats.area as o_area, oplats.pay as o_pay, oplats.date_opl as o_date, cast(payment.date as Date) as Date, payment.* FROM oplats 
      LEFT JOIN payment USING(plot, name) WHERE `oplats`.`plot` LIKE '".$d['id']."' AND oplats.name = '".$o['name']."' ORDER BY `oplats`.`name` ASC ";
        $pays = $dolg->select_sql($pay);
       
      foreach($pays as $p){
         
          $my_diff = $dolg->col_days($o['date_opl'], $p['date']);

          if($my_diff > 0){

          $peny = $dolg->itog_peny($d['id'], $o['name']);

          }
          else {$peny = 0; }

       //  $itog_peny = $itog_peny + $peny;
        echo '<tr><td color="red">Оплата </td><td>'.$p['pay'].'</td><td>Дата оплаты: '.$p['date'].'</td>
        <td>'.round($my_diff, 0).'</td><td>'.round($peny, 2).'</td></tr>';
      }  
      $sqlv = "SELECT sum(pay) as pays FROM payment WHERE plot = '".$d['id']."' AND name = '".$o['name']."' GROUP BY name";
      $itog = $dolg->one_param($sqlv);
      $ds = $dolg->sum_dolg($o['pay'], $itog);
      
      echo '<tr><td color="red">Итого оплачено </td><td>'.$itog.'</td><td></td></tr>';
      echo '<tr><td color="red">Долг </td><td>'.$ds.'</td><td></td></tr>';
      $itog_dolg = $ds + $itog_dolg;
    }
    
}
$sum_peny = $dolg->sum_peny($d['id']);
echo '<tr><th>Общая сумма долга на '.date('d-m-Y').' </th><th>'.$itog_dolg.'</th>
<th>Сумма пеней </th><th>'.round($sum_peny, 2).'</th></tr>';
echo '</table>';
// Считает пени только по первому платежу
?>