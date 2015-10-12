<?php
error_reporting(E_ALL);
$fields = array (
  'at' => 2,
  'badv' => 
  array (
    0 => 'myntra.com',
  ),
  'bcat' => 
  array (
    0 => 'IAB8-18',
    1 => 'IAB23',
    2 => 'IAB7-28',
    3 => 'IAB7-44',
    4 => 'IAB26',
    5 => 'IAB25',
    6 => 'IAB24',
    7 => 'IAB17-18',
    8 => 'IAB25-3',
    9 => 'IAB26-2',
    10 => 'IAB25-2',
    11 => 'IAB26-1',
    12 => 'IAB25-1',
    13 => 'IAB25-7',
    14 => 'IAB25-6',
    15 => 'IAB25-5',
    16 => 'IAB26-4',
    17 => 'IAB25-4',
    18 => 'IAB26-3',
    19 => 'IAB7-39',
    20 => 'APL8-6',
    21 => 'IAB8-5',
    22 => 'IAB7-42',
    23 => 'IAB19-3',
    24 => 'IAB9-9',
  ),
  'device' => 
  array (
    'connectiontype' => 0,
    'devicetype' => 1,
    'geo' => 
    array (
      'country' => 'IND',
      'type' => 3,
    ),
    'ip' => '101.222.166.197',
    'js' => 0,
    'make' => 'Generic',
    'model' => 'Android 1.5',
    'os' => 'Android',
    'osv' => '1.5',
    'ua' => 'Mozilla/5.0 (Linux; U; Android 5.1; en-US; XT1022 Build/LPC23.13-34.8) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.2.0.535 U3/0.8.0 Mobile Safari/534.30',
  ),
  'ext' => 
  array (
    'carriername' => 'Aircel',
    'coppa' => 0,
    'operaminibrowser' => 0,
    'udi' => 
    array (
    ),
  ),
  'id' => '02R1WdIvdt',
  'imp' => 
  array (
    0 => 
    array (
      'banner' => 
      array (
        'battr' => 
        array (
          0 => 1,
          1 => 3,
          2 => 5,
          3 => 8,
          4 => 9,
        ),
        'btype' => 
        array (
          0 => 1,
          1 => 3,
        ),
        'h' => 36,
        'mimes' => 
        array (
          0 => 'image/gif',
          1 => 'image/jpeg',
          2 => 'image/png',
        ),
        'w' => 216,
      ),
      'displaymanager' => 'SOMA',
      'id' => '1',
      'instl' => 0,
    ),
  ),
  'site' => 
  array (
    'cat' => 
    array (
      0 => 'IAB9',
    ),
    'domain' => 'tamilanda.us',
    'id' => '65835000',
    'name' => 'Tamilanda.us',
    'publisher' => 
    array (
      'id' => '923864840',
      'name' => 'Adzmedia',
    ),
  ),
  'user' => 
  array (
  ),
);

//print_r($fields);exit;


//$response = http_post_flds("http://staging.digibrahma.in/sama/admin/plugins/DSP/dsp_request.php?dsp=testsmaato", $fields);

//echo $response;

$url = 'http://staging.digibrahma.in/sama/admin/plugins/DSP/dsp_request.php?dsp=testsmaato';

$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($fields),
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
print_r($result);



function http_post_flds($url, $data, $headers=null) {   
    $data = http_build_query($data);    
    $opts = array('http' => array('method' => 'POST', 'content' => $data));

    if($headers) {
        $opts['http']['header'] = $headers;
    }
    $st = stream_context_create($opts);
    $fp = fopen($url, 'rb', false, $st);

    if(!$fp) {
        return false;
    }
    return stream_get_contents($fp);
}
?>