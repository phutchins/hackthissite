 <?php

//------------------------------------------------------------------------------------
function evalCrossTotal($strMD5)
{
    $intTotal = 0;
    $arrMD5Chars = str_split($strMD5, 1);
    foreach ($arrMD5Chars as $value)
    {
        $intTotal += '0x0'.$value;
    }
    return $intTotal;
}//-----------------------------------------------------------------------------------



//------------------------------------------------------------------------------------
function encryptString($strString, $strPassword)
{
    // $strString is the content of the entire file with serials
    $strPasswordMD5 = md5($strPassword);
    $intMD5Total = evalCrossTotal($strPasswordMD5);
    //echo $intMD5Total."\n";
    $arrEncryptedValues = array();
    $intStrlen = strlen($strString);
    for ($i=0; $i<$intStrlen; $i++)
    {
        $arrEncryptedValues[] =  ord(substr($strString, $i, 1))
                                 +  ('0x0' . substr($strPasswordMD5, $i%32, 1))
                                 -  $intMD5Total;
        $intMD5Total = evalCrossTotal(substr(md5(substr($strString,0,$i+1)), 0, 16)
                                 .  substr(md5($intMD5Total), 0, 16));
    }
    return implode(' ' , $arrEncryptedValues);
}//-----------------------------------------------------------------------------------

function decryptString($encString, $strPassword)
{
  $strPasswordMD5 = md5($strPassword);
  $intMD5Total = evalCrossTotal($strPasswordMD5);
  $strString ='';
  $arrEncryptedValues = explode(' ', $encString);

  $intStrlen = count($arrEncryptedValues);

  for ($i=0; $i<$intStrlen; $i++)
  {
    echo "Enc val [".$i."]:".$arrEncryptedValues[$i]."\n";

    $strString .= chr(intval($arrEncryptedValues[$i]) - ('0x0' . substr($strPasswordMD5, $i%32, 1)) +  $intMD5Total);
    //$strString .= chr('0x0'. substr($strPasswordMD5, $i%32, 1))
    // ord <=> chr   (ord gets decimal for ascii, chr returns ascii for decimal)

    // implode explode (convert to integer with intval)
    $intMD5Total = evalCrossTotal(substr(md5(substr($strString,0,$i+1)), 0, 16).substr(md5($intMD5Total), 0, 16));

  }
  return $strString;
}

// Guess md5 total and md5 password

echo "Encrypted Message: ".encryptString('hello', 'password')."\n";

echo "Decrypted Message: ".decryptString('-163 -73 -167 -112 -31', 'password')."\n";

?>
