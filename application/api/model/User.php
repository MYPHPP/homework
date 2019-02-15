<?php
namespace app\api\model;

use Firebase\JWT\JWT;
use think\facade\Cache;
use think\Model;
use crypto\Crypt;

class User extends Model{
    protected $pk = "id";//设置主键
    //protected $table = "tt_user";//设置表名
    //protected $connection = "";//设置数据库

    public function getPasswdAttr($val){
        return ['original'=>$val,'change'=>Crypt::decrypt($val,config('web_key'))];
    }

    public function createToken($uid){
        //token_type：表示令牌类型，该值大小写不敏感，这里用bearer
        $key = config('setting.jwt_key');
        $data['uid'] = $uid;
        $time = time(); //当前时间
        $token = [
            "iss"   => "centos",            // 签发者
            "iat"   => $time,               // 签发时间
            "aud"   => 'web',               // 接收方
            "sub"   => 'all',               // 面向的用户
            "data"  => $data
        ];
        $access_token = $token;
        $access_token['scopes'] = 'role_access'; //token标识，请求接口的token
        $access_token['exp'] = $time+300; //access_token过期时间,这里设置2个小时
        $refresh_token = $token;
        $refresh_token['scopes'] = 'role_refresh'; //token标识，刷新access_token
        $refresh_token['exp'] = $time+(86400 * 30); //access_token过期时间,这里设置30天
        $atoken = JWT::encode($access_token,$key);
        Cache::store('redis')->set('refresh_token:'.$atoken,JWT::encode($refresh_token,$key),$time+(86400 * 30));
        return $atoken;
    }
}