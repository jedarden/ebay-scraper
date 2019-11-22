<?PHP
define("DB_SERVER", "hostname");
define("DB_USER", "username");
define("DB_PASS", "password");
define("DB_NAME", "database_name");

function fetchBetween($needle1,$needle2,$haystack,$include=false)
{
	$position = stripos($haystack,$needle1);
	
	if ($position === false) { return null; }
	
	if ($include == false) $position += strlen($needle1);
	
	$position2 = stripos($haystack,$needle2,$position);
	
	if ($position2 === false) { return null; }
	
	if ($include == true) $position2 += strlen($needle2);
	
	$length = $position2 - $position;
	
	$substring = substr($haystack, $position, $length);
	
	return trim($substring);
}

function get_url_contents($url)
{
	$crl = curl_init();
	$timeout = 5;
	curl_setopt ($crl, CURLOPT_URL,$url);
	curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($crl, CURLOPT_REFERER, 'https://www.ebay.com/');	
	curl_setopt($crl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30");
	$ret = curl_exec($crl);
	curl_close($crl);
	return $ret;
}	

function get($sql, &$array)
{
	try 
	{
		$db = new PDO('pgsql:user='.DB_USER.' dbname='.DB_NAME.' password='.DB_PASS.' host='.DB_SERVER);
	} 
		catch (PDOException $e) 
	{
		echo $e->getMessage();
	}
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
	$sth = $db->prepare($sql);
	/*
	echo '<textarea>';
		echo $sql.'\n\n';
		print_r($db->errorInfo());
		echo '</textarea>';
	*/	
	$q = $sth->execute();
	if(!$q)
	{
		die("Execute query error, because: ". print_r($db->errorInfo(), true). " Query: [".$sql."]");
	}
	else
	{
		$array = $sth->fetchAll();
		$numrows = sizeof($array);
	
		return $numrows;
	}
}

function runsql($sql)
{
	if($sql == "")	
	{
		return;
	}
	
	try 
	{
		$db = new PDO('pgsql:user='.DB_USER.' dbname='.DB_NAME.' password='.DB_PASS.' host='.DB_SERVER);
	} 
	catch (PDOException $e) 
	{
		echo $e->getMessage()." SQL: ".$sql;
	}
	
	$q = $db->exec($sql);	
	
	if(!$q && $db->errorCode() != "00000")
	{
		echo "Execute query error, because: ". print_r($db->errorCode(), true)."<BR><BR>".print_r($db->errorInfo(), true). " Query: [".$sql."]";
	}
	
	$db = null;
	
	return($q);
}

?>