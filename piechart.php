
<?php
//include '../config.php';
include 'config.php';
$Y=date('Y');
$mm=date('m');


$endM=isset($_GET['endM'])  ? $_GET['endM']  : $mm-1;
$staM=isset($_GET['starM'])  ? $_GET['starM']  : $mm-1;


// echo $staM;
// echo $endM;


// 連接資料庫
$link = db_open();

// 寫出 SQL 語法
$sqlstr="

SELECT month, type5 AS type,
sum(p) AS p,sum(p)/(SELECT sum(p) FROM market where year2='初年度' AND type5 !='勞退年金' AND month BETWEEN $staM and $endM AND year='2018') AS ratio
 FROM market
where year2='初年度' AND type5 !='勞退年金' 
AND month BETWEEN $staM and $endM AND year='2018' GROUP BY type5 ORDER BY ratio DESC";
//echo $sqlstr;


$result = mysqli_query($link, $sqlstr) or die(ERROR_QUERY);
$data='';
while($row=mysqli_fetch_array($result))
{
	$data.="['".$row['type']."',".$row['p']."],";
	

}


db_close($link);

echo "<script type='text/javascript'>
	google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          $data
        ]);

        var options = {
          title: '',

        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
	}
</script>";


?>
<body>
	<div id="piechart"></div>
</body>


