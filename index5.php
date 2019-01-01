<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<title></title>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

</head>
<body>

<?php
//include '../config.php';
include 'config.php';
$Y=date('Y');
$mm=date('m');

//$staM=isset($_POST['starM']) ? $_POST['starM'] : 1;
//$endM=isset($_POST['endM'])  ? $_POST['endM']  : $mm-1;
$staM=isset($_GET['starM'])  ? $_GET['starM'] : $mm-1;
$endM=isset($_GET['endM'])    ? $_GET['endM']   : $mm-1;


$m1="";$m2="";
	
for($i=1;$i<$mm;$i++){
		
		if($i!=$staM){
			$m1.="<option value=$i>".$i."</option>";
		}
		else
			$m1.="<option selected=true value=$i>".$i."</option>";
}

for($i=1;$i<$mm;$i++){

		if($i!=$endM){
			$m2.="<option value=$i>".$i."</option>";
		}
		else
			$m2.="<option selected=true value=$i>".$i."</option>";
}





// 連接資料庫
$link = db_open();

// 寫出 SQL 語法
$sqlstr="

SELECT month, type5 AS type,
sum(p) AS p,sum(p)/(SELECT sum(p) FROM market where year2='初年度' AND type5 !='勞退年金' AND month BETWEEN $staM and $endM) AS ratio
FROM market
where year2='初年度' AND type5 !='勞退年金' 
AND month BETWEEN $staM and $endM GROUP BY type5 ORDER BY ratio DESC";
//echo $sqlstr;

$result = mysqli_query($link, $sqlstr) or die(ERROR_QUERY);
$data='';
$data='<h2></h2>
	<table class="table table-bordered table-hover">
		<tr align="center">
			<th>險種</th>
			<th>保費</th>
			<th>占比</th>
		</tr>';
while($row=mysqli_fetch_array($result))
{
	$type = $row['type'];
	$p = number_format($row['p']);
	$ratio = number_format($row['ratio']*100,1).'%';

   $data.=<<<HEREDOC
   					
		
      <tr align="center">
      	<td>{$type}</td>
      	<td style="text-align:right">{$p}</td>
		<td style="text-align:right">{$ratio}</td>
      </tr>
     
HEREDOC;

}
$data.='</table>';

$result = mysqli_query($link, $sqlstr) or die(ERROR_QUERY);
$data2='';
while($row=mysqli_fetch_array($result))
{
	$data2.="['".$row['type']."',".$row['p']."],";
	

}



db_close($link);
echo $data;
echo "<script type='text/javascript'>
	google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          $data2
        ]);

        var options = {
          title: '',

        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
	}
</script>";
?>
<div id="piechart"></div>

</body>

</html>
