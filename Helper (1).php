<?php

namespace App\PaymentChannels\Drivers\Posfix;

class Helper
{
    
    protected $HashString;
    protected $transactionDate;
    protected $paymentChannel;
    protected $Language = "tr-TR";
    protected $Token;
    protected $successUrl;
    protected $failureUrl;
    protected $Url;
    protected $BaseUrl;
    protected $ipaddress;
    protected $input_json;
    
    
    public static function GetTransactionDateString()
    {
        date_default_timezone_set('Europe/Istanbul');
        return date("Y-m-d H:i:s");
    }
    public static function CreateToken($publicKey, $hashString) 
    {
		return $publicKey . ":" . base64_encode ( sha1 ( $hashString, true ) );
	}
	
	public static function get_client_ip() {
		if (getenv ( 'HTTP_CLIENT_IP' ))
			$ipaddress = getenv ( 'HTTP_CLIENT_IP' );
		else if (getenv ( 'HTTP_X_FORWARDED_FOR' ))
			$ipaddress = getenv ( 'HTTP_X_FORWARDED_FOR' );
		else if (getenv ( 'HTTP_X_FORWARDED' ))
			$ipaddress = getenv ( 'HTTP_X_FORWARDED' );
		else if (getenv ( 'HTTP_FORWARDED_FOR' ))
			$ipaddress = getenv ( 'HTTP_FORWARDED_FOR' );
		else if (getenv ( 'HTTP_FORWARDED' ))
			$ipaddress = getenv ( 'HTTP_FORWARDED' );
		else if (getenv ( 'REMOTE_ADDR' ))
			$ipaddress = getenv ( 'REMOTE_ADDR' );
		else
			$ipaddress = '127.0.0.1';

		return $ipaddress;
	}
		public static function getCurrentUrl() {
		return  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http" . '://' . $_SERVER ['SERVER_NAME'] . ":" . $_SERVER ['SERVER_PORT'];
	}
		public static function GUID() {
		if (function_exists ( 'com_create_guid' ) === true) {
			return trim ( com_create_guid (), '{}' );
		}

		return sprintf ( '%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand ( 0, 65535 ), mt_rand ( 0, 65535 ), mt_rand ( 0, 65535 ), mt_rand ( 16384, 20479 ), mt_rand ( 32768, 49151 ), mt_rand ( 0, 65535 ), mt_rand ( 0, 65535 ), mt_rand ( 0, 65535 ) );
	}

	/**
	 * Xml çıktısı oluşturmamıza olanak sağlayan metod
	 */
	public static function formattoXMLOutput($input_xml) {
		$doc = new DOMDocument ();
		$doc->loadXML ( $input_xml );
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$output = $doc->saveXML ();
		return $output;
	}

	/**
	 * Okunabilir düz bir JSON dizesini oluşturur.

	 */
	public static function formattoJSONOutput($input_json) {
		$result = '';
		$pos = 0;
		$strLen = strlen ( $input_json );
		$indentStr = "\t";
		$newLine = "\n";

		for($i = 0; $i < $strLen; $i ++) {
			// Grab the next character in the string.
			$char = $input_json [$i];

			// Are we inside a quoted string?
			if ($char == '"') {
				// search for the end of the string (keeping in mind of the escape sequences)
				if (! preg_match ( '`"(\\\\\\\\|\\\\"|.)*?"`s', $input_json, $m, null, $i ))
					return $input_json;

				// add extracted string to the result and move ahead
				$result .= $m [0];
				$i += strLen ( $m [0] ) - 1;
				continue;
			} else if ($char == '}' || $char == ']') {
				$result .= $newLine;
				$pos --;
				$result .= str_repeat ( $indentStr, $pos );
			}

			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if ($char == ',' || $char == '{' || $char == '[') {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}

				$result .= str_repeat ( $indentStr, $pos );
			}
		}
		return $result;
	}

    public static function PreparePaymentData($paymentData)
    {
        // Ödeme verilerini düzenleme
        $paymentData['transaction_date'] = self::GetTransactionDateString();
        // Diğer gerekli düzenlemeleri burada yapabilirsiniz.
        return $paymentData;
    }
}
