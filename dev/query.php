<?php
// Prepare cURL command
$key = 'czbHGlnM+XsO89ECula7oXpWXFZ75qlzpxm402Spf7M';
//http://services.odata.org/V4/TripPinServiceRW/People('russellwhyte')?$format=application/json;odata.metadata=full
// 
// Metadata: 'https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/$metadata'


// Search: 
// Author: https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/Author?$filter=ID%20eq7&$format=json
// Paper_Author: https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/Paper?$filter=AuthorID%20eq%207&$format=json
// Paper: https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/Paper?$filter=ID%20eq%202404604&$format=json

//$ch = curl_init('https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/Journal?$filter=ID%20eq%201791&$format=json');
//$ch = curl_init('https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/Paper_Ref?$filter=DstID%20eq%202&$format=json');
//$ch = curl_init('https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/Paper?$filter=ID%20eq%2029%20or%20ID%20eq%2023%20or%20ID%20eq%2024%20or%20ID%20eq%2025%20or%20ID%20eq%2026%20or%20ID%20eq%2027%20or%20ID%20eq%2028%20or%20ID%20eq%2030%20or%20ID%20eq%2031%20or%20ID%20eq%2032%20or%20ID%20eq%2033&$format=json');
//$ch = curl_init('https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/Author?$filter=Name%20eq%20%27Kewen%20Wang%27&$format=json');
$ch = curl_init('https://api.datamarket.azure.com/MRC/MicrosoftAcademic/v2/Paper_Author?$count&$filter=AuthorID%20eq%20599994&$format=json');


curl_setopt($ch, CURLOPT_USERPWD, $key.':'.$key);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Parse the XML response
$result = curl_exec($ch);

$httpRespCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
print("Response code: ".$httpRespCode);
curl_close($ch);
//$result = $result[0];

//$result = serialize($result);

//$result = '"{"d":{"results":[{"__metadata":{"id":"https://api.datamarket.azure.com/Data.ashx/MRC/MicrosoftAcademic/v2/Journal(1791)","uri":"https://api.datamarket.azure.com/Data.ashx/MRC/MicrosoftAcademic/v2/Journal(1791)","type":"MRC.MicrosoftAcademic.Journal"},"ID":1791,"ShortName":"PROTEIN SCI","FullName":"Protein Science","Homepage":"http://www.proteinscience.org/"}]}}";';
print ("
	
	
	INIT STR: ".$result);

$jsonData = json_decode($result);

$db = new PDO();
/*
$start = strpos($result, '"');
$data = substr($result, $start+1, -2);
$jsonData = null;
if($data){
	print("
		
		
		DATA2: ".$data);
	$jsonData = json_decode($data);
}
*//*
$fullName = $jsonData->{'d'}->{'results'}[0]->{'FullName'};
print("
	
	
	DUMP: ");
print(var_dump($jsonData->{'d'}->{'results'}[0])); 

print("
	
	
	FULL_NAME: ".$fullName); 
*/
?>