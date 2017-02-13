<html lang="en" dir="ltr">
<head>
<title>SneakUp.Net</title>
<meta name="viewport" content="width=1751, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
                function hideURLbar(){ window.scrollTo(0,1); } </script>
<meta charset="iso-8859-1">
<script src="js/jquery.min.js"></script>
<link rel="stylesheet" href="css/morris.css">
<link rel="stylesheet" href="styles/layout2.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body>
<div class="wrapper row1">
  <header id="header" class="clear">
    <div id="hgroup">
      <h1><a href="#">SneakUp.Net</h1>
      <h2> Simple terminal/CMD applications </h2>
    </div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="http://www.github.comp<dylan157">Github</a></li>
        <li><a href="shoutouts.html">Shoutouts</a></li>
        <li class="last"><a href="about.html">About</a></li>
      </ul>
    </nav>
  </header>
</div>

<div class="wrapper row1">
  <div id="container" class="clear">
    <!-- Slider -->
    <section id="slider" class="clear">
      <figure>
<?php
echo '<link rel="stylesheet" type="text/css" href="stylesheet.css"></head>';
$po = $_POST["in_post"];
$es = $_POST["in_estate"];
$dt = $_POST["in_date"];
if ((strlen($po)==6)&&(!preg_match('/\s/',$po))){$po = substr($po, 0, 3)." ".substr($po, 3, 3);echo $po;}

if ((strlen($po)==7)&&(!preg_match('/\s/',$po))){$po = substr($po, 0, 4)." ".substr($po, 4, 3);echo $po;}

$p_c = explode(' ', $po);

echo "          <h2>Search the UK House Price Index</a></h2>";
echo "           <p>postcode: EG 'mk4 3' will find all properties that start with 'mk4 3'. Enter full postcode for more specific/faster search results.</a></p>";
echo "                 <form action='search.php' method='post'>";
echo "                 Postcode: &nbsp <input name='in_post' value = '".$po."'>";
echo "                 &nbsp or Estate: &nbsp <input name='in_estate' value = '".$es."'>";
echo "                 &nbsp From Year: &nbsp <input name='in_date' value = '".$dt."'>";
echo "                 <input type='submit'>";
echo "                 </form>";
echo "         </figcaption>";
echo "       </figure>";
echo "     </section>";
echo "   </div>";
echo " </div>";
echo " <div>";


if ( ($dt == "" and $es == "" and $po == "") or ($dt == "EG 2016" and $es == "" and $po == "EG RM13 9AS")){echo "<h1>Enter Something</h1>"; exit();}
if ($po == "EG RM13 9AS"){
	$po = "";
}if ($dt == "EG 2016"){
	$dt = "";
}

if (preg_match('/[^a-z0-9 _]+/i', $dt)) {
        echo "Invalid search term!";
        exit();
}
elseif (strlen($dt)>= 5){
        echo "Invalid search term!";
        exit();

}

//Postcode checker -------------------------------------------------------
if (preg_match('/[^a-z0-9 _]+/i', $po)) {
        echo "Invalid search term!";
	exit();
}
elseif (strlen($po)>= 9){
        echo "Invalid search term!";
        exit();}
else{
if ((strlen($po)==8)or(strlen($p_c[0])==3 && strlen($po)==7)){$gopo = "= '".strtoupper($po)."'";}
else{$gopo = "like '".strtoupper($po)."%'";}


if (strlen($dt)>0 ){$query = "SELECT price_paid, transfer_date, postcode, property_type, old_new, duration, paon, saon, street, estate, city_town, district, county FROM add_property where postcode " . $gopo . "and transfer_date > '".$dt."-01-01' and price_paid <> '0'  order by transfer_date desc limit 2000";
}else{
$query = "SELECT price_paid, transfer_date, postcode, property_type, old_new, duration, paon, saon, street, estate, city_town, district, county FROM add_property where postcode " . $gopo . " and price_paid <> '0' order by transfer_date desc limit 2000";
}}


//estate checker ---------------------------------------------------------
if (strlen($es)==0){
	echo "";
}
elseif (preg_match('/[^a-z0-9 _]+/i', $es)) {
        echo "Invalid search term!";
        exit();
}
else{
$gopo = "= '".strtoupper($es)."'";

if (strlen($dt)>0 ){$query = "SELECT price_paid, transfer_date, postcode, property_type, old_new, duration, paon, saon, street, estate, city_town, district, county FROM add_property where estate " . $gopo . "and transfer_date > '".$dt."-01-01' and price_paid <> '0' order by transfer_date desc limit 2000";
}else{$query = "SELECT price_paid, transfer_date, postcode, property_type, old_new, duration, paon, saon, street, estate, city_town, district, county FROM add_property where estate " . $gopo . " and price_paid <> '0' order by transfer_date desc limit 2000";}}

$dbconn = pg_connect("")
    or die('Could not connect: ' . pg_last_error());

// Performing SQL query
$result1 = pg_query($query) or die('Query failed: ' . pg_last_error());
$price_date = array();
$avg_price = array();
$years = array();
$count0 = 0;
while ($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
    array_push($price_date, array());
    $count = 0;
    foreach ($line as $col_value) {
        if ($count == 0){array_push($avg_price, $col_value); array_push($price_date[$count0], $col_value); }
        if ($count == 1){array_push($price_date[$count0], substr($col_value, 0, 4)); if( !in_array(substr($col_value, 0, 4) ,$years)){array_push($years, substr($col_value, 0, 4)); } }
	$count += 1;

    }
    $count0 += 1;
}


$years2 = array();
foreach($price_date as $pd){if(!array_key_exists($pd[1], $years2)){ if ($pd[0]>1){$years2[$pd[1]] = array($pd[0]);}} else{ if ($pd[0]>1){ array_push($years2[$pd[1]], $pd[0]);}}}//fucking awesome line! 3 days.
foreach($years2 as $key => $y){if(count($y)< 2){unset($years2[$key]);}}

if(count($years2)>1){


echo"        <div class='main'>";
echo"                 <div class='w3_agile_main_grids'>";
echo"                         <div class='clear'> </div>";
echo"                         <div class='wthree_bars_bottom'>";
echo"                                 <div class='agileinfo_bars_bottom_right'>";
echo"                                        <div class='w3l_area_chart agileits_w3layouts_text'>";
echo"                                                <h1>Average area house price.</h1>";
echo" 						     <h4>Blue(y): Year Average House price. Gray(z): Total Properties sold</h4>";
echo"                                                 <div id='graph'></div>";
echo"                                         </div>";
echo"                                 </div>";
echo"                                 <div class='clear'> </div>";
echo"                 </div>";
echo"                 <script type='text/javascript' src='js/raphael-min.js'></script>";
echo"                 </script>";
echo"                 <script src='js/morris.js'></script>";
echo"                 <script>";
echo"                         Morris.Area({";
echo"                           element: 'graph',";
echo"                           data: [";


foreach($years2 as $key => $y2){$average = 0; foreach($y2 as $prices){ $average += $prices;} echo "{x: '".$key."', y:".round($average/count($y2)).", z:".(count($y2))."},\n";}


echo"                           ],";
echo"                           xkey: 'x',";
echo"                           ykeys: ['y', 'z'],";
echo"                           labels: ['Y', 'Z']";
echo"                         }).on('click', function(i, row){";
echo"                           console.log(i, row);";
echo"                         });";
echo"                 </script>";
echo"         <!-- //area -->";
echo"         </div>";
}else{echo "Not enough variable data for timeline average. Try searching for a specific postcode or removing the last character to search larger area. EG: mk13 2az => mk13 2a (Queries are limited to 2000 results. If all 2000 results are from 2016, a timeline cannot be made.)";}


pg_result_seek( $result1, 0 );
$total = 0;
foreach ($avg_price as $pr){$total += $pr;}
$final_avg = round($total / count($avg_price));
sort($avg_price);

// Printing results in HTML
echo "<table align='center' cellpadding='0' cellspacing='0' class='db-table'>\n";
echo "<tr> \n <th>Price Paid</th> \n <th>Transfer Date</th> \n <th>Postcode</th> \n <th>Property Type</th> \n <th>Old/New</th> \n <th>Duration</th> \n <th>Door Num/Name</th> \n <th> 2nd Door Num/Name</th> \n <th>Street</th> \n <th>Estate</th> \n <th>City</th> \n <th>District</th> \n <th>County</th> \n <th>Google Maps</th></tr>";
$tmp1 = array('D' => 'Detached', 'T' => 'Terraced', 'S' => 'Semi Detached', 'F' => 'Flat', 'O' => 'Other' );
$tmp2 = array('F' => 'FreeHold', 'L' => 'LeaseHold');
$tmp3 = array('Y'=> 'New Build', 'N' => 'Pre_owned');

while ($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
    echo "\t<tr>\n";
    $count = 0;
    $postcode = '';
    $house_no = '';
    $street = '';
    $estate = '';	
    foreach ($line as $col_value) {
        if($count == 0){$col_value = '£'.$col_value;}
	if($count == 3){$col_value = $tmp1[$col_value];}
        if($count == 4){$col_value = $tmp3[$col_value];}
        if($count == 5){$col_value = $tmp2[$col_value];}

	echo "\t\t<td>$col_value</td>\n";
	if($count == 2){$postcode = $col_value;}
	if($count == 6){$house_no = $col_value;}
        if($count == 8){$street = $col_value;}
        if($count == 9){$estate = $col_value;}

	if ($count == 12){echo "\t\t<td><a target = '_blank' href='https://www.google.co.uk/maps/place/".$house_no.'+'.$street.'+'.$estate.'+'.str_replace(' ', '', $postcode)."'> Map</a></td>\n";}
	$count += 1;
    }
    echo "\t</tr>\n";
}


echo "</table>\n";

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
?>
</div>
</body>
</html>

