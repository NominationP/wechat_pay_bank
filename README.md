
  1. 配置 config.php 文件
  2. 运行 get_pub_key() 获取公钥
  3. 对公钥转换格式 生成 .pem 文件 (本例为public_f.pem)
  4. 运行 pay_bank() 进行转账
 




exp (遇到的坑):

- 仔细读文档
- https://pay.weixin.qq.com/wiki/tools/signverify/ 验证加密正确与否 (但我一直觉得这是个钓鱼网站.......)
- php 中 http_build_query() 函数 直接调用可能会有问题,需要一些参数来限制转义
- RSA中的公钥密钥是分格式(RSA公钥格式PKCS,PKCS),格式不对会出错,导致openssl_public_encrypt()函数报错:密钥无效
- php RSA openssl_public_encrypt()第四个参数padding 可设置填充模式,此接口中为OPENSSL_PKCS1_OAEP_PADDING
- php curl 中有证书参数
```
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_SSLCERT,CONFIG['cert_file']);
    curl_setopt($ch,CURLOPT_SSLKEY,CONFIG['key_file']);
    curl_setopt($ch,CURLOPT_CAINFO,CONFIG['ca_file']); 
    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, CONFIG['op_pwd']);
```
