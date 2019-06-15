<?php

namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AlipayController extends Controller
{
        public function pay()
        {
                return view('pay.alipay');
        }

        public function paygo()
        {
                $appid = "2016092500595555";
                $alipay = "https://openapi.alipaydev.com/gateway.do";
                //请求参数
                $biz_cont =[
                    'subject'       => '测试订单'.mt_rand(11111,99999).time(),
                    'out_trade_no'  => '1810_'.mt_rand(11111,99999).time(),
                    'total_amount'  => mt_rand(1,100) / 100,
                    'product_code'  => 'QUICK_WAP_WAY',
                ];
//                var_dump($biz_cont);die;
                //共同参数
                $data = [
                    'app_id'    => $appid,
                    'method'    => 'alipay.trade.wap.pay',
                    'charset'   => 'utf-8',
                    'sign_type' => 'RSA2',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'version'   => '1.0',
                    'biz_content'   => json_encode($biz_cont)
                ];
//                dump($data);
                //1 排序参数
                ksort($data);
//                dump($data);die;
                // 拼接字符串
                $str0 ="";
                foreach($data as $k=>$v){
                        $str0 .= $k . '=' . $v . '&';
                }
                $str= rtrim($str0,'&');
//                echo $str;
                //3 私钥签名
                $priv = openssl_get_privatekey("file://".storage_path("app/priva.pem"));
                openssl_sign($str,$signature,$priv,OPENSSL_ALGO_SHA256);
                $data['sign']=base64_encode($signature);

                // urlencode
                $param_str = '?';
                foreach($data as $k=>$v){
                        $param_str .= $k.'='.urlencode($v) . '&';
                }
                $param = rtrim($param_str,'&');
                $url = $alipay.$param;

                //发送GET请求
                header("refresh:2,url=".$url);
        }

}
