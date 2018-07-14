<?php
	
	$result = '';

	if (count($_SERVER['argv']) <= 1){
		help();
		exit;
	}



	$ethcsv = '';
	$zaifjpyfile = '';
	$reverse = false;


	for ($i=1; $i < count($_SERVER['argv']); $i++){
		if (strcmp(strtolower ( $_SERVER['argv'][$i] ) , '--ethcsv' ) == 0){
			$i++;
			$ethcsv = $_SERVER['argv'][$i];
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

	if ($ethcsv == ''){
		help();
	}
	
	
	// "Txhash","Blockno","UnixTimestamp","DateTime","From","To","ContractAddress","Value_IN(ETH)","Value_OUT(ETH)","CurrentValue @ $436.4/Eth","TxnFee(ETH)","TxnFee(USD)","Historical $Price/Eth","Status","ErrCode"

	
	$csv = array();

	
	if (($handle = fopen($ethcsv, "r")) !== FALSE) {
		$count = 0;
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($count == 0){ $count ++; continue; }
			
			$num = count($data);
			if ($num > 10){
				array_push ( $csv, array('datetime' => $data[2], 'in' => $data[7], 'out' => $data[8], 'tx' => $data[0]) );
			}
		}
	}
	if ($reverse){
	    $txjson = array_reverse( $csv );
    }else{
    	$txjson = $csv;
    }
    
    foreach ($txjson as $key => $value){
        $hiduke = date( 'Y-m-d H:i:s', $value['datetime'] );
        $ymd    = date( 'Y/m/d', $value['datetime'] );
        
//        var_dump ($value);
    	
		$result .= '"'.$hiduke.'","'. $value['out']. '","'. $value['in'].'"'. ',"'. getjpyvalue($ymd, $zaifjpyfile) . '"'. "\n";
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

Use : php ethcsv.php
	: [option]
	: --ethcsv [https://etherscan.io/ export csvfile] (require)
	: --zaifjpyfile [japan yen csv file] https://zaif.jp/download_trade_price (option)
	: --reverse [on] default is off (option)
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