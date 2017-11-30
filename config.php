<?php


	$config = [

		/** 密匙  可在设置中更改*/
		"key" => "XXXXXXXXXXXXXXXXXXXXX",
		/** 商户号  可在个人信息中查看*/
		"mch_id" => "XXXXXXXXX",

		
		
		/** apiclient_cert.pem */
		"cert_file" => "apiclient_cert.pem",		
		/** apiclient_key.pem */
		"key_file" => "apiclient_key.pem",
		/** rootca.pem */
		"ca_file" => "rootca.pem",
		/** 此密码在加载证书时设置 */
		"op_pwd" => "XXXXXXXXX",


		/** 收款方银行卡号	 */
		"enc_bank_no" => "XXXXXXXXXXXXXXX",
		/** 收款方用户名  */
		"enc_true_name" => "XXXXXXX",
		/** 收款方开户行  https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_4*/
		"bank_code" => "XXX",
		/** 付款金额 付款金额：RMB分（支付总额，不含手续费） 注：大于0的整数*/
		"amount" => 100,
		/** 付款说明 付款金额：RMB分（支付总额，不含手续费） 注：大于0的整数*/
		"desc" => "XXXX",


	];

	define(CONFIG,$config);


