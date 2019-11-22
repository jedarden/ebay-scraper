<?PHP
include('Config.php');

//Get the data from the database. 
$SQL = 'SELECT * FROM "EbaySpy"';

if(get($SQL, $Result) > 0)
{
	foreach($Result as $OneResult)
	{
		$Month = date('Y-m', $OneResult['SaleDate']);
		
		$Output[$Month]['Month'] = $Month;
		$Output[$Month]['TotalSales'] += $OneResult['SalePrice'];
		$Output[$Month]['TotalProducts'] ++;
	}
}

//Aggregate it by month. 
ksort($Output);

//Display a table. 
echo '<pre>'.print_r($Output, true).'</pre>';
?>