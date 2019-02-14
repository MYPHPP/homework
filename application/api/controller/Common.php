<?php
namespace app\api\controller;

use app\api\model\User;
use Firebase\JWT\JWT;
use think\Controller;
use think\Request;

class Common extends Controller
{
    protected $userinfo;
    public function __construct(Request $request)
    {
        parent::__construct();
        $auth = $request->header('Authorization');
        $jwt = explode(' ',$auth);
        $token = isset($jwt[1]) ? $jwt[1] : '';
        $res = $this->checkToken($token);
        if($res['code'] != 200) return json($res);
        $user = User::find($res['msg']);
        if(!$user) return json(['code'=>202,'msg'=>'用户不存在']);
        $this->userinfo = $user;
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