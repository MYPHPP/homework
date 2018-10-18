<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function dd($data= ''){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}

/**
 * Description 判断加密算法是否可用
 * @CreateTime 2018/10/17 11:58:45
 * @param $cipher_method
 * @return bool
 */
function check_cipher_method($cipher_method){
    return in_array($cipher_method,openssl_get_cipher_methods());
}

/**
 * Description 加密aes
 * @CreateTime 2018/10/17 12:01:21
 * @param $plaintext
 * @param string $key
 * @param string $cipher
 * @return string
 */
function aesencrypt($plaintext,$key='opldkedw',$cipher="aes-256-cbc"){
    try{
        $cipher = 'aes-256-cbc';
        if(!check_cipher_method($cipher)){
            throw new \Exception("加密算法名称错误");
        }
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA , $iv);
        $hmac = hash_hmac('sha256', $ciphertext, $key, true);
        return base64_encode($iv.$hmac.$ciphertext);
    }catch (\Exception $e){
        return $e->getMessage();
    }
}

/**
 * Description ase解密
 * @CreateTime 2018/10/17 13:37:45
 * @param $val
 * @param string $key
 * @param string $cipher
 * @return string
 */
function aesdecrypt($val,$key='opldkedw',$cipher="aes-256-cbc"){
    try{
        $cipher = 'aes-256-cbc';
        if(!check_cipher_method($cipher)){
            throw new \Exception("加密算法名称错误");
        }
        $val = base64_decode($val);
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($val,0,$ivlen);
        $start = 32+$ivlen;
        $plaintext = substr($val,$start);
        $value = openssl_decrypt($plaintext,$cipher,$key,OPENSSL_RAW_DATA,$iv);
        return $value;
    }catch (\Exception $e){
        return $e->getMessage();
    }
}