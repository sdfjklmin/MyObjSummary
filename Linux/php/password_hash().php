<?php
/*password_hash数据加密 具体请参照PHP官方文档*/
$password_plaintext = [
    'name'=>'passwd',
    'id'=>'noId'
];

#操作参数(可省)
$options = [
    'cost' => 11, # 对应的cost值算法默认为10,可以根据硬件情况进行设置
    'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM), # 加盐设置(建议省掉,函数中已经自动加盐)
];
#使用加密password_hash('str|must',must);
$password_hash = password_hash( json_encode($password_plaintext), PASSWORD_DEFAULT,$options);

#获取加密信息
password_get_info( $password_hash ) ;
#解密验证 bool
$check = password_verify(json_encode($password_plaintext),$password_hash) ;
