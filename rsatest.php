<?php
include('BigInteger.php');
include('Crypt/RSA.php');
$app_key= "919fc0716ad8f931325ad9c0a483574e";
$app_pass= "919fc0716ad8f931325ad9c0a483574e";
$retinfo=file_get_contents("http://api.bistu.edu.cn/api/api_app.php?table=member&action=getloginkey&app_key=".$app_key."&app_pass=".$app_pass);
$modulus = json_decode($retinfo,true); 
echo $modulus ;
echo "\n";
$userinfo = "2012011183|02093838";
$modulus_16 = new Math_BigInteger($modulus,16);
$mend = $modulus_16->toString();
echo $mend ;
echo "\n";
$error_handler = create_function('$obj', 'echo "error: ", $obj->getMessage(), "\n"');
$key = new Crypt_RSA_Key($mend , '10001', 'public', 'default');
echo $a;
echo $key;
$rsa_obj = new Crypt_RSA;

$encrypt_html_data_str = $rsa_obj->encrypt($userinfo, $key);
echo $encrypt_html_data_str;
$info=bin2hex($encrypt_html_data_str );
echo $info;

?>