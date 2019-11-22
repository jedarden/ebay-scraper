<?PHP
ini_set('memory_limit','32M');
include('Config.php');

$SeedPage = 'https://www.ebay.com/sch/m.html?_nkw=&_armrs=1&_from=&LH_Complete=1&_ssn=treasurehunter121680&rt=nc';

function ProcessPage($URL)
{
	$Page = get_url_contents($URL);

	$RawList = fetchBetween('<ul id="ListViewInner">', '<div id="PaginationAndExpansionsContainer">', $Page);

	$Items = explode('<li id="', $RawList);
	array_shift($Items);

	$NextPage = fetchBetween('href="', '">', fetchBetween('Next page of results', '</a>', $Page));
	
	if($NextPage !== 'javascript:;')
	{
		$Output = ProcessPage($NextPage);
	}	
	
	foreach($Items as $OneItem)
	{
		$OneOutput = array();
		$OneOutput['ID'] = fetchBetween('listingId="', '"', $OneItem);
		$OneOutput['Title'] = str_replace(array('<strong>', '</strong>'), '', fetchBetween('">', '</a>', fetchBetween('<h3 class="lvtitle"><a', '</h3>', $OneItem)));
		$OneOutput['Date'] = fetchBetween('pan>', '<', fetchBetween('<span class="tme"', '/span>', $OneItem));
		$OneOutput['UnixDate'] = strtotime($OneOutput['Date']);
		$OneOutput['Price'] = str_replace(',', '', fetchBetween('$', '<', fetchBetween('<li class="lvprice prc">', 'span>', $OneItem)));
		$OneOutput['SellerName'] = fetchBetween('&_ssn=', '&ssPageName', $OneItem);
		
		$Output[$OneOutput['ID']] = $OneOutput;
		
	}
	
	return($Output);
}


$Output = ProcessPage($SeedPage);

foreach($Output as $OneOutput)
{
	
	$SQL = 'INSERT INTO "EbaySpy" ("ListingID", "ListingTitle", "SellerName", "SalePrice", "SaleDate") VALUES (\''.$OneOutput['ID'].'\', \''.$OneOutput['Title'].'\', \''.$OneOutput['SellerName'].'\', \''.$OneOutput['Price'].'\', \''.$OneOutput['UnixDate'].'\') ON CONFLICT DO NOTHING ;';

	//echo $SQL."\n";

	$Counter += runsql($SQL);
}


//echo $RawList;
//print_r($Output);
echo $Counter.' products added.';

?>