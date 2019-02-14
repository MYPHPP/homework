<?php

namespace app\http\middleware;

use app\api\model\User;
use Firebase\JWT\JWT;
use think\facade\Cache;

class CheckLogin
{
    public function handle($request, \Closure $next)
    {
        $newToken = null;
        if(!$request->isMobile()) return json(['code'=>0,'msg'=>'禁止PC访问']);
        $auth = $request->header('Authorization');
        if(empty($auth)) return json(['code'=>100,'msg'=>'未登录']);//未登录
        $jwt = explode(' ',$auth);
        if(count($jwt) != 2) return json(['code'=>101,'msg'=>'Authorization参数格式错误']);//参数格式错误
        $token = $jwt[1];
        $res = $this->checkToken($token);
        if($res['code'] != 200){//access token失效
            if($newToken =  Cache::store('redis')->get('old_access_token:'.$token)){//失效access token还存在redis
                header('Authorization','Bearer '.$newToken);
                //header('Cache-Control','no-store'); 跨域需要设置
                return $next($request);
            }
            $refresh_token = Cache::store('redis')->get('refresh_token:'.$token);
            $res1 = $this->checkToken($refresh_token);
            if($res1['code'] != 200){//refersh token失效
                return json(['code'=>$res1['code'],'msg'=>$res1['msg']]);
            }else{
                //refersh token未失效，根据refersh token刷新access token
                $model = new User();
                $newToken = $model->createToken($res1['msg']);
                header('Authorization','Bearer '.$newToken);
                //header('Cache-Control','no-store'); 跨域需要设置
                // 将旧token存储在redis中,30秒内再次请求是有效的
                Cache::store('redis')->set('old_access_token:'.$token,$newToken,30);
            }
        }
        $respon = $next($request);
        if($newToken){
            header('Authorization','Bearer '.$newToken);
            //header('Cache-Control','no-store'); 跨域需要设置
        }
        return $respon;
    }

    public function checkToken($token){
        $key = config('setting.jwt_key');
        //Firebase定义了多个 throw new，我们可以捕获多个catch来定义问题，catch加入自己的业务，比如token过期可以用当前Token刷新一个新Token
        try {
            //JWT::$leeway = 60;//当前时间减去60，把时间留点余地
            $decoded = JWT::decode($token, $key, ['HS256']); //HS256方式，这里要和签发的时候对应
            $res = ['code'=>200,'msg'=>$decoded->data->uid];
        } catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
            $res = ['code'=>10003,'msg'=>$e->getMessage()];
        }catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
            $res = ['code'=>10002,'msg'=>$e->getMessage()];
        }catch(\Firebase\JWT\ExpiredException $e) {  // token过期
            $res = ['code'=>10001,'msg'=>$e->getMessage()];
        }catch(Exception $e) {  //其他错误
            $res = ['code'=>10000,'msg'=>$e->getMessage()];
        }
        return $res;
    }
}
