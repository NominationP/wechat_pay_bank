<?php
/**
  * wechat php test
  */
 require_once 'myen.php';
 require_once "config.php";

/**
 *  1 配置 config.php 文件
 *  2 运行 get_pub_key() 获取公钥
 *  3 对公钥转换格式 生成 .pem 文件 (本例为public_f.pem)
 *  4 运行 pay_bank() 进行转账
 */

/**
 * https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_2
 * 企业付款到银行卡
 * @return [type] [description]
 */
function pay_bank(){

    /** @var rsa类实例化 对 enc_bank_no  enc_true_name 这俩个参数进行加密  */
    $rsa = new Rsa();

    /** 过程需要用到公钥 */


    $par_arr = [
    /** @var 商户号  */
    "mch_id" => CONFIG['mch_id'],
    /** @var 商户企业付款单号 要求唯一性*/
    "partner_trade_no" => strtotime(date("d M Y H:i:s")),
    /** 随机字符串   nonce_str */
    "nonce_str" => (string)rand(1000,99999999999999999),
    /** 收款方银行卡号 enc_bank_no 需要用公钥RSA加密*/
    "enc_bank_no" => $rsa->publicEncrypt(CONFIG['enc_bank_no']),
    /** 收款方用户名  enc_true_name 需要用公钥RSA加密*/
    "enc_true_name" => $rsa->publicEncrypt(CONFIG['enc_true_name']),
    /** 收款方开户行  bank_code */
    "bank_code" => CONFIG['bank_code'],
    /** 付款金额    amount */
    "amount" => CONFIG['amount'],
    /** 付款说明    desc */
    "desc" => CONFIG['desc'],
    ];

    /** @var 签名算法  */
    $sign = getSign($par_arr);


    $par_arr['sign'] = $sign;


    /** @var 生成XML文件  */
    foreach ($par_arr as $key => $value) {
        $par_arr[$value] = $key;
        unset($par_arr[$key]);
    }
    $xml = new SimpleXMLElement('<xml/>');
    array_walk_recursive($par_arr, array ($xml, 'addChild'));
    $xml = $xml->asXML();


    $url = "https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank";
    $ch = curl_init();

    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_SSLCERT,CONFIG['cert_file']);
    curl_setopt($ch,CURLOPT_SSLKEY,CONFIG['key_file']);
    curl_setopt($ch,CURLOPT_CAINFO,CONFIG['ca_file']); 
    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, CONFIG['op_pwd']);

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    $content=curl_exec($ch);
    print_r($content);


}


/**
 * https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=4_3
 * 签名算法
 * @param  [type] $par_arr [description]
 * @return [type]          [description]
 */
function getSign($par_arr){
    
    /** if value == "" move it */
    $par_arr_noNull = array_filter($par_arr, function($value) { return $value !== ''; });

    /** sort array */
    ksort($par_arr_noNull);

    /** add key */
    $par_arr_noNull["key"] = CONFIG['key'];

    /** gen to http format */
    $str = "";
    foreach ($par_arr_noNull as $key => $value) {
        $str .= $key."=".$value."&";
    }
    $http_arr = (trim($str,"&"));

    /** @var md5  */
    $md5 = MD5($http_arr);

    /** @var stroupper  */
    $sign = strtoupper($md5);

    return $sign;
}


/**
 * https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_7&index=4
 * 获取RSA加密公钥API
 *
 * ***** need to run :
 * 接口默认输出PKCS#1格式的公钥，商户需根据自己开发的语言选择公钥格式 
 *
 * PKCS#1 转 PKCS#8:
 *   openssl rsa -RSAPublicKey_in -in <filename> -pubout
 *   PKCS#8 转 PKCS#1:
 *   openssl rsa -pubin -in <filename> -RSAPublicKey_out
 * 
 */
function get_pub_key(){

    $url = "https://fraud.mch.weixin.qq.com/risk/getpublickey";

    $post_arr = [
        "mch_id" => CONFIG['mch_id'],
        "nonce_str" => strtotime(date("d M Y H:i:s")),
        "sign_type" => "MD5",
    ];

    $mch_id = $post_arr['mch_id'];
    $nonce_str = $post_arr['nonce_str'];

    /** @var 签名算法  */
    $sign = getSign($post_arr);


    $xml="<xml>
    <mch_id>".$mch_id."</mch_id>
    <nonce_str>".$nonce_str."</nonce_str>
    <sign>".$sign."</sign>
    <sign_type>MD5</sign_type>
    </xml>";
    

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);


    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_SSLCERT,CONFIG['cert_file']);
    curl_setopt($ch,CURLOPT_SSLKEY,CONFIG['key_file']);
    curl_setopt($ch,CURLOPT_CAINFO,CONFIG['ca_file']); 
    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, CONFIG['op_pwd']);

    $content=curl_exec($ch);
    print_r($content);
}


pay_bank();




?>

