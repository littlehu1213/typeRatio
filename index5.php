<script type="text/javascript">	
  $(document).ready(function(){
  $("#myTable").tablesorter({
    theme: "blue",
    widgets: ['zebra']
    });
})
</script>
<?php
//include '../config.php';
include 'config.php';
$Y=date('Y');
$mm=date('m');


$endM=isset($_GET['endM'])  ? $_GET['endM']  : $mm-1;
$staM=isset($_GET['starM'])  ? $_GET['starM']  : $mm-1;


// echo $staM;
// echo $endM;

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
sum(p) AS p,sum(p)/(SELECT sum(p) FROM market where year2='初年度' AND type5 !='勞退年金' AND month BETWEEN $staM and $endM AND year='2018') AS ratio
 FROM market
where year2='初年度' AND type5 !='勞退年金' 
AND month BETWEEN $staM and $endM AND year='2018' GROUP BY type5 ORDER BY ratio DESC";
//echo $sqlstr;

$result = mysqli_query($link, $sqlstr) or die(ERROR_QUERY);
$data='';
$data='
<!-- Area Chart Example-->

<div id="piechart">
</div>
</div>

<div class="card mb-3">
	<div class="card-header">
		<i class="fa fa-table"></i>
		  table
	</div>
	<div class="card-body">
    <div class="table-responsive">
	<table class="table table-bordered tablesorter" id="myTable" "width="100%" cellspacing="0">
		<thead>
	        <tr>
	          <th>險種</th>
	          <th>保費</th>
	          <th>佔比</th>
	        </tr>
	      </thead>
';
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

