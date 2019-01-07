<script type="text/javascript">	
  $(document).ready( function () {
    $('.tablesorter').DataTable({
    	"paging":   false,     
        "info":     false
    });
});

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
$data2='';
$data2='

    <div class="table-responsive">
	<table class="table table-bordered tablesorter hover stripe row-border" id="myTable" "width="100%" cellspacing="0">
		<thead>
	        <tr style="text-align:center;">
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

   $data2.=<<<HEREDOC
   					
		
      <tr align="center">
      	<td>{$type}</td>
      	<td style="text-align:right">{$p}</td>
		<td style="text-align:right">{$ratio}</td>
      </tr>
     
HEREDOC;
}
$data2.='</table>';
echo $data2;

?>

