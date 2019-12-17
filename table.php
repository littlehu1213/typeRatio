<script type="text/javascript">	
  $(document).ready( function () {
    $('.tablesorter').DataTable({
    	"paging":   false,     
        "info":     false,
        "ordering": false
       
    });
});

</script>

<?php
include './config.php';
//include 'config.php';
$page=isset($_GET['page'])  ? $_GET['page']  : 0;
$staM=isset($_GET['starM'])  ? $_GET['starM']  : "";
$endM=isset($_GET['endM'])  ? $_GET['endM']  : "";
echo $staM;
echo $endM;

$link = db_open();
$sqlstr="";
switch ($page) {
	case 1:
// echo $staM;
// echo $endM;

// 連接資料庫

// 寫出 SQL 語法
$sqlstr="

SELECT* FROM (SELECT  `company`, 2019期繳FYP,2018期繳FYP,
SUM(2019期繳FYP-2018期繳FYP)/2018期繳FYP AS '期繳同期成長率',
2019期繳FYP/2019FYP AS '期繳佔比',


2019躉繳FYP,
SUM(2019躉繳FYP-2018躉繳FYP)/2018躉繳FYP AS '躉繳同期成長率',
2019躉繳FYP/2019FYP AS '躉繳佔比'

FROM(
SELECT `company`,
SUM(IF( `year`=2019 AND`period`='非躉繳' AND `year_type` ='初年度',`premium`,0)) AS '2019期繳FYP',
SUM(IF( `year`=2018 AND`period`='非躉繳' AND `year_type` ='初年度',`premium`,0)) AS '2018期繳FYP',
SUM(IF( `year`=2019 AND`period`='躉繳' AND `year_type` ='初年度',`premium`,0)) AS '2019躉繳FYP',
SUM(IF( `year`=2018 AND`period`='躉繳' AND `year_type` ='初年度',`premium`,0)) AS '2018躉繳FYP',
SUM(IF( `year`=2019 AND `year_type` ='初年度',`premium`,0)) AS '2019FYP'
FROM fyp_asso
WHERE `month`BETWEEN $staM AND $endM
GROUP BY company) A
GROUP BY company
ORDER BY 2019FYP DESC) B

UNION ALL
SELECT  'total', 2019期繳FYP,2018期繳FYP,
SUM(2019期繳FYP-2018期繳FYP)/2018期繳FYP AS '期繳同期成長率',
2019期繳FYP/2019FYP AS '期繳佔比',


2019躉繳FYP,
SUM(2019躉繳FYP-2018躉繳FYP)/2018躉繳FYP AS '躉繳同期成長率',
2019躉繳FYP/2019FYP AS '躉繳佔比'

FROM(
SELECT `company`,
SUM(IF( `year`=2019 AND`period`='非躉繳' AND `year_type` ='初年度',`premium`,0)) AS '2019期繳FYP',
SUM(IF( `year`=2018 AND`period`='非躉繳' AND `year_type` ='初年度',`premium`,0)) AS '2018期繳FYP',
SUM(IF( `year`=2019 AND`period`='躉繳' AND `year_type` ='初年度',`premium`,0)) AS '2019躉繳FYP',
SUM(IF( `year`=2018 AND`period`='躉繳' AND `year_type` ='初年度',`premium`,0)) AS '2018躉繳FYP',
SUM(IF( `year`=2019 AND `year_type` ='初年度',`premium`,0)) AS '2019FYP'
FROM fyp_asso
WHERE `month`BETWEEN $staM AND $endM) A;";
//echo $sqlstr;

$result = mysqli_query($link, $sqlstr) or die(ERROR_QUERY);
$data='';

$data='<div class="table-responsive">
			<table class="table table-bordered tablesorter hover stripe row-border" id="myTable" "width="100%" cellspacing="0">
				<thead style="text-align:center;">
		       		<tr>
		       		
			          <th>壽險公司</th>
			          <th>2019期繳FYP</th>	        
			          <th>期繳同期成長率</th>
			          <th>期繳占比</th>
			          <th>2019躉繳FYP</th>
			          <th>躉繳同期成長率</th>
			          <th>躉繳占比</th>
	        		</tr>
	      		</thead>';
	     
              

while($row=mysqli_fetch_array($result))
{
	$type = $row['company'];
	$FYP1 = number_format($row['2019期繳FYP']/100000);
	//$FYP2 = number_format($row['2018期繳FYP']);
	$ratio1 = number_format($row['期繳同期成長率']*100).'%';
	$ratio2 = number_format($row['期繳佔比']*100).'%';
	$FYP3 = number_format($row['2019躉繳FYP']/100000);
	$ratio3 = number_format($row['躉繳同期成長率']*100).'%';
	$ratio4= number_format($row['躉繳佔比']*100).'%';
   $data.=<<<HEREDOC
   					
		
      <tr align="center">
      	<td>{$type}</td>
      	<td style="text-align:right">{$FYP1}</td>
		
		<td style="text-align:right">{$ratio1}</td>
		<td style="text-align:right">{$ratio2}</td>
		<td style="text-align:right">{$FYP3}</td>
		<td style="text-align:right">{$ratio3}</td>
		<td style="text-align:right">{$ratio4}</td>
      </tr>
     
HEREDOC;
}
$data.='</table>';

db_close($link);
    break;
  	case 2:
  	$sqlstr="

SELECT company,FYP1,商品佔比1,市佔率1,FYP2,商品佔比2,市佔率2,FYP3,商品佔比3,市佔率3,FYP4,商品佔比4,市佔率4,FYP5,商品佔比5,市佔率5
FROM(SELECT company,
SUM(IF(`type`='傳統型壽險' OR `type`='傳統型年金',`premium`,0)) AS 'FYP1',
SUM(IF(`type`='傳統型壽險' OR `type`='傳統型年金',`premium`,0))/SUM(`premium`) AS '商品佔比1',
SUM(IF(`type`='傳統型壽險' OR `type`='傳統型年金',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='傳統型壽險' OR `type`='傳統型年金') AND month BETWEEN $staM AND $endM) AS '市佔率1',

SUM(IF(`type`='利率變動型壽險' OR `type`='萬能人壽壽險',`premium`,0)) AS 'FYP2',
SUM(IF(`type`='利率變動型壽險' OR `type`='萬能人壽壽險',`premium`,0))/SUM(`premium`) AS '商品佔比2',
SUM(IF(`type`='利率變動型壽險' OR `type`='萬能人壽壽險',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='利率變動型壽險' OR `type`='萬能人壽壽險'  )AND month BETWEEN $staM AND $endM ) AS '市佔率2',


SUM(IF(`type`='利率變動型年金',`premium`,0)) AS 'FYP3',
SUM(IF(`type`='利率變動型年金',`premium`,0))/SUM(`premium`) AS '商品佔比3',
SUM(IF(`type`='利率變動型年金',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='利率變動型年金')AND month BETWEEN $staM AND $endM) AS '市佔率3',

SUM(IF(`type`='投資型壽險' OR `type`='投資型年金',`premium`,0)) AS 'FYP4',
SUM(IF(`type`='投資型壽險' OR `type`='投資型年金',`premium`,0))/SUM(`premium`) AS '商品佔比4',
SUM(IF(`type`='投資型壽險' OR `type`='投資型年金',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='投資型壽險' OR `type`='投資型年金')AND month BETWEEN $staM AND $endM ) AS '市佔率4',

SUM(IF(`type`='傷害保險' OR `type`='健康保險',`premium`,0)) AS 'FYP5',
SUM(IF(`type`='傷害保險' OR `type`='健康保險',`premium`,0))/SUM(`premium`) AS '商品佔比5',
SUM(IF(`type`='傷害保險' OR `type`='健康保險',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='傷害保險' OR `type`='健康保險')AND month BETWEEN $staM AND $endM ) AS '市佔率5',

SUM(`premium`) AS 'total'

FROM fyp_asso
WHERE `year`=2019 AND `year_type` ='初年度' AND month BETWEEN $staM AND $endM
GROUP BY `company`
ORDER BY total DESC)A

UNION ALL
SELECT 'total',
SUM(IF(`type`='傳統型壽險' OR `type`='傳統型年金',`premium`,0)) AS 'FYP1',
SUM(IF(`type`='傳統型壽險' OR `type`='傳統型年金',`premium`,0))/SUM(`premium`) AS '商品佔比1',
SUM(IF(`type`='傳統型壽險' OR `type`='傳統型年金',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='傳統型壽險' OR `type`='傳統型年金') AND month BETWEEN $staM AND $endM) AS '市佔率1',

SUM(IF(`type`='利率變動型壽險' OR `type`='萬能人壽壽險',`premium`,0)) AS 'FYP2',
SUM(IF(`type`='利率變動型壽險' OR `type`='萬能人壽壽險',`premium`,0))/SUM(`premium`) AS '商品佔比2',
SUM(IF(`type`='利率變動型壽險' OR `type`='萬能人壽壽險',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='利率變動型壽險' OR `type`='萬能人壽壽險') AND month BETWEEN $staM AND $endM) AS '市佔率2',


SUM(IF(`type`='利率變動型年金',`premium`,0)) AS 'FYP3',
SUM(IF(`type`='利率變動型年金',`premium`,0))/SUM(`premium`) AS '商品佔比3',
SUM(IF(`type`='利率變動型壽險' OR `type`='萬能人壽壽險',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='利率變動型壽險')AND month BETWEEN $staM AND $endM) AS '市佔率3',

SUM(IF(`type`='投資型壽險' OR `type`='投資型年金',`premium`,0)) AS 'FYP4',
SUM(IF(`type`='投資型壽險' OR `type`='投資型年金',`premium`,0))/SUM(`premium`) AS '商品佔比4',
SUM(IF(`type`='投資型壽險' OR `type`='投資型年金',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='投資型壽險' OR `type`='投資型年金') AND month BETWEEN $staM AND $endM) AS '市佔率4',

SUM(IF(`type`='傷害保險' OR `type`='健康保險',`premium`,0)) AS 'FYP5',
SUM(IF(`type`='傷害保險' OR `type`='健康保險',`premium`,0))/SUM(`premium`) AS '商品佔比5',
SUM(IF(`type`='傷害保險' OR `type`='健康保險',`premium`,0))/(select SUM(`premium`) FROM fyp_asso WHERE `year`=2019 AND `year_type` ='初年度' AND(`type`='傷害保險' OR `type`='健康保險')AND month BETWEEN $staM AND $endM ) AS '市佔率5'


FROM fyp_asso
WHERE `year`=2019 AND `year_type` ='初年度' AND month BETWEEN $staM AND $endM";

$result = mysqli_query($link, $sqlstr) or die(ERROR_QUERY);
$data='';

while($row=mysqli_fetch_array($result))
{
	$company = $row['company'];
	$FYP1 = number_format($row['FYP1']/100000);
	$ratio1 = number_format($row['商品佔比1']*100).'%';
	$ratio2 = number_format($row['市佔率1']*100).'%';
	$FYP2 = number_format($row['FYP2']/100000);
	$ratio3 = number_format($row['商品佔比2']*100).'%';
	$ratio4 = number_format($row['市佔率2']*100).'%';
	$FYP3 = number_format($row['FYP3']/100000);
	$ratio5 = number_format($row['商品佔比3']*100).'%';
	$ratio6 = number_format($row['市佔率3']*100).'%';
	$FYP4 = number_format($row['FYP4']/100000);
	$ratio7 = number_format($row['商品佔比4']*100).'%';
	$ratio8 = number_format($row['市佔率4']*100).'%';
	$FYP5 = number_format($row['FYP5']/100000);
	$ratio9 = number_format($row['商品佔比5']*100).'%';
	$ratio10 = number_format($row['市佔率5']*100).'%';
   $data.=<<<HEREDOC
   					
		
      <tr align="center">
      	<td>{$company}</td>
      	<td style="text-align:right">{$FYP1}</td>		
		<td style="text-align:right">{$ratio1}</td>
		<td style="text-align:right">{$ratio2}</td>
		<td style="text-align:right">{$FYP2}</td>
		<td style="text-align:right">{$ratio3}</td>
		<td style="text-align:right">{$ratio4}</td>
		<td style="text-align:right">{$FYP3}</td>
		<td style="text-align:right">{$ratio5}</td>
		<td style="text-align:right">{$ratio6}</td>
		<td style="text-align:right">{$FYP4}</td>
		<td style="text-align:right">{$ratio7}</td>
		<td style="text-align:right">{$ratio8}</td>
		<td style="text-align:right">{$FYP5}</td>
		<td style="text-align:right">{$ratio9}</td>
		<td style="text-align:right">{$ratio10}</td>
      </tr>
     
HEREDOC;
}
break;
  default:echo "沒有選table";
    break;
};



$data2='  <div class="table-responsive">
  <table class="table table-bordered  hover stripe row-border" id="myTable" "width="100%" cellspacing="0">
            <thead>                       
	          	<tr>	          		
	          		<th rowspan="2" >壽險公司</th>
					<th colspan=3>傳統壽險(+年金)</th>
					<th colspan=3>利變壽險(+萬能)</th>
					<th colspan=3>利變年金</th>
					<th colspan=3>投資型</th>
					<th colspan=3>A&H</th>
					
	          	</tr>
	          	<tr>
	          		<th>FYP(億元)</th>
	          		<th>商品佔比</th>
	          		<th>市佔率</th>
	          		<th>FYP(億元)</th>
	          		<th>商品佔比</th>
	          		<th>市佔率</th>
	          		<th>FYP(億元)</th>
	          		<th>商品佔比</th>
	          		<th>市佔率</th>
	          		<th>FYP(億元)</th>
	          		<th>商品佔比</th>
	          		<th>市佔率</th>
	          		<th>FYP(億元)</th>
	          		<th>商品佔比</th>
	          		<th>市佔率</th>
	          	</tr>
	        
	       
            </thead>
            <tbody>
              <tr>
                '.$data.'              
              </tr>             
            </tbody>        
          </table>';
switch ($page) {
  case 1:echo $data;
    break;
  case 2:echo $data2;
    break;
  default:echo "沒有選table";
    break;
};




?>

