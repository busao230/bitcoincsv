<?php
    define ('INSIGHT_BASE_API_BTC', 'https://insight.bitpay.com/api');
    define ('INSIGHT_BASE_API_BCH', 'https://bch-insight.bitpay.com/api');
    define ('INSIGHT_BASE_API_MONA', 'https://mona.chainsight.info/api');

	
	
	

	
	$result = '';

	if (count($_SERVER['argv']) <= 1){
		help();
		exit;
	}



	$address = '';
	$coin    = '';
	$zaifjpyfile = '';
	$reverse = false;

	for ($i=1; $i < count($_SERVER['argv']); $i++){
		if (strcmp(strtolower ( $_SERVER['argv'][$i] ) , '--coin' ) == 0){
			$i++;
			$coin = $_SERVER['argv'][$i];
		}
		if (strcmp(strtolower ( $_SERVER['argv'][$i] ) , '--address' ) == 0){
			$i++;
			$address = $_SERVER['argv'][$i];
		}

		if (strcmp(strtolower ( $_SERVER['argv'][$i] ) , '--zaifjpyfile' ) == 0){
			$i++;
			$zaifjpyfile = $_SERVER['argv'][$i];
		}
		if (strcmp(strtolower ( $_SERVER['argv'][$i] ) , '--reverse' ) == 0){
			$i++;
			if (strcmp (strtolower( $_SERVER['argv'][$i] ), 'on') == 0){
				$reverse = true;
			}else{
				$reverse = false;
			}
		}			
			
	}

	if ($address == ''){
		help();
	}
	
	if ($coin == '' ){
		$coin = 'btc';
	}
	
	if (strcmp (strtolower($coin), 'btc' ) == 0 ){
		$insight_base_uri = INSIGHT_BASE_API_BTC;
	}
	if (strcmp (strtolower($coin), 'bch' ) == 0 ){
		$insight_base_uri = INSIGHT_BASE_API_BCH;
	}
	if (strcmp (strtolower($coin), 'mona' ) == 0 ){
		$insight_base_uri = INSIGHT_BASE_API_MONA;
	}
	
	
    $url = $insight_base_uri.'/addr/'.$address;
    
    $json   = file_get_contents($url);
    if ($reverse){
	    $txjson = array_reverse( json_decode($json,true) );
    }else{
	    $txjson = json_decode($json,true);
    }
    
    foreach ($txjson['transactions'] as $key => $tx){
    	
    	echo '*';
    	sleep(5);
    	
        $url = $insight_base_uri.'/tx/'.$tx;
        $json2   = file_get_contents($url);
        $txjson2 = json_decode($json2,true);
        
        $hiduke = date( 'Y-m-d H:i:s', $txjson2['blocktime'] );
        $ymd    = date( 'Y/m/d', $txjson2['blocktime'] );
        



        $ins  = $txjson2['vin'];
        $outs = $txjson2['vout'];
        
        
//        var_dump ($txjson2);

        foreach ($ins as $in){
            
            if (strcmp( strtolower($in['addr']) , strtolower($address) ) == 0){
            	$result .= '"'.$hiduke.'","'. $in['value']. '","0"'. ',"'. getjpyvalue($ymd, $zaifjpyfile) . '"'. "\n";
            }
        }

        foreach ($outs as $out){
            $addresses = $out['scriptPubKey']['addresses'];
            foreach ($addresses as $adr){

                if (strcmp( strtolower($adr) , strtolower($address) ) == 0){
	            	$result .= '"'.$hiduke.'","0","'. $out['value']. '","' . getjpyvalue($ymd, $zaifjpyfile) . '"'. "\n";
                }
            }
        }


    }

	echo "\n\n";
    echo $result;
	echo "\n\n";

	exit;
    
    
/////////////////////////////////
//     SHOW HELP             ////
/////////////////////////////////
    
	function help(){
?>

Use : php bitcoincsv.php
	: [option]
	: --coin [btc][bch][mona] default is btc (option)
	: --address [COIN ADDRESS] (require)
	: --zaifjpyfile [japan yen csv file] https://zaif.jp/download_trade_price (option)
	: --reverse [on] default is off (option)
	: memo
	: new address of bitcoin cash is require
	: 

<?php

	}


/////////////////////////////////
//     GET JPY VALUE (DAY)   ////
/////////////////////////////////

	function getjpyvalue($ymd, $zaifjpyfile){
		if ($zaifjpyfile == ''){
			return '0';
		}

		if (($handle = fopen($zaifjpyfile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				if ($num > 3){
					if ( strcmp($data[0] , $ymd) == 0){
						return $data[2];
					}
				}
			}
		}
	}

?>