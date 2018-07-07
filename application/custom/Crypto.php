<?php
namespace app\custom;

/*
 * 自定义加密解密方法
 * */
class Crypto{
    /*
     * aes加密
     * */
    public function aesencrypt($plaintext,$key='opldkedw',$cipher="aes-256-cbc"){
        try{
            $cipher = 'aes-256-cbc';
            if(!$this->check_cipher_method($cipher)){
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

    /*
     * aes解密
     * */
    public function aesdecrypt($val,$key='opldkedw',$cipher="aes-256-cbc"){
        try{
            $cipher = 'aes-256-cbc';
            if(!$this->check_cipher_method($cipher)){
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

    /*
     * 验证可用的加密算法
     * @param $cipher_method 加密算法
     * return true or false
     * */
    public function check_cipher_method($cipher_method){
        $result = false;
        if(!empty($cipher_method)){
            if(in_array($cipher_method,openssl_get_cipher_methods())){
                $result = true;
            }
        }
        return $result;
    }
}