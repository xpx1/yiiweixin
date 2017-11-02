<?php
namespace app\common\components;

use app\common\services\BaseService;
use Yii;
class HttpClient extends BaseService
{
    /*
     * 该类为微信接口调用
     */
    private static $headers = [];

    private static $cookie = null;

    /**
     * get方式请求接口
     * @param $url 【接口地址】
     * @param array $parmam 参数
     */
    public static function get($url,$param=[])
    {
        return self::curl($url, $param,"get");
    }

    /**
     * post方式请求接口
     * @param $url  【请求地址】
     * @param array $parmam 【参数】
     * @param array $extra  [额外的参数]
     */
    public static function post($url,$param=[],$extra = [])
    {
        return self::curl($url,$param,'post');
    }
    //curl请求数据
    protected static function curl($url,$param,$method='POST')
    {
        $calculate_time1 = microtime(true); //返回当前 Unix 时间戳和微秒数
        // 初始华
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); // 设置URL
        curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。0为不启用
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_CERTINFO , true);   //TRUE 将在安全传输时输出 SSL 证书信息到 STDERR。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//CURLOPT_SSL_VERIFYPEER 设置为FALSE 禁止 cURL 验证对等证书
        //curl_setopt($curl, CURLOPT_VERBOSE, true); //打印日志
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);//函数中加入下面这条语句

        //设置允许 cURL 函数执行的最长秒数。
        if( isset( Yii::$app->params['curl'] ) && isset(Yii::$app->params['curl']['timeout']) ){
            curl_setopt($curl, CURLOPT_TIMEOUT, Yii::$app->params['curl']['timeout']);
        }else{
            curl_setopt($curl, CURLOPT_TIMEOUT, 5); //设置允许 cURL 函数执行的最长秒数，这里设置为5秒
        }
        //在HTTP请求中包含一个"User-Agent: "头的字符串。 设置头信息
        if(array_key_exists("HTTP_USER_AGENT",$_SERVER)){
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }
        //设置 HTTP 头字段的数组。格式： array('Content-type: text/plain', 'Content-length: 100')
        if(!empty(self::$headers)){
            $headerArr = [];
            foreach( self::$headers as $n => $v ) {
                $headerArr[] = $n .': ' . $v;
            }
            curl_setopt ($curl, CURLOPT_HTTPHEADER , $headerArr );  //构造IP
        }
        //   CURLOPT_COOKIE : 传递一个包含HTTP cookie的头连接。
        if( self::$cookie ){
            curl_setopt($curl, CURLOPT_COOKIE, self::$cookie);
        }

        // post处理 else就get处理
        if ($method == 'post')
        {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            if(is_array($param)){
                $param = http_build_query($param); //转化为这种格式foo=bar&baz=boom&cow=milk&php=hypertext+processor
            }

            curl_setopt($curl, CURLOPT_POSTFIELDS, $param); //post 数据
        }else{
            curl_setopt($curl, CURLOPT_POST, FALSE); //这里为CURLOPT_POST，设置为1，表示启用时会发送一个常规的POST请求,这里为false不启用
        }

        // 执行输出
        $info = curl_exec($curl);

        //log 如果错误则打印日志
        $_errno = curl_errno($curl);
        $_error = '';
        if($_errno)
        {
            $_error = curl_error($curl);
        }
        curl_close($curl);
        $calculate_time_span = microtime(true) - $calculate_time1;
        $log = \Yii::$app->getRuntimePath().DIRECTORY_SEPARATOR.'curl.log';
        file_put_contents($log,date('Y-m-d H:i:s')." [ time:{$calculate_time_span} ] url: {$url} \nmethod: {$method} \ndata: ".json_encode($param)." \nresult: {$info} \nerrorno: {$_errno} error: {$_error} \n",FILE_APPEND);

        if( $_error ){
            return self::_err( $_error );
        }
        //成功就返回
        return $info;
    }
    //设置header头
    public static function setHeader($header){
        self::$headers = $header;
    }
    //设置cookie
    public static function setCookie( $cookie ){
        self::$cookie = $cookie;
    }
//设置代理ip
    protected static function getProxy()
    {
        $proxy = array(
            '0' => '60.16.210.118:80',
            '1' => '183.62.60.100:80',
            '2' => '58.215.185.46:82',
            '3' => '223.4.21.184:80',
            '4' => '61.53.143.179:80',
            '5' => '42.121.105.155:8888',
            '6' => '115.29.184.17:82',
            '7' => '183.131.144.204:443',
            '8' => '121.199.30.110:82',
            '9' => '113.207.130.166:80',
            '10' => '124.202.181.226:8118',
            '11' => '116.236.216.116:8080',
            '12' => '114.255.183.173:8080',
            '13' => '202.108.50.75:80',
            '14' => '122.96.59.106:82',
            '15' => '122.96.59.106:83',
            '16' => '1.202.74.121:8118',
            '17' => '114.255.183.164:8080',
            '18' => '111.13.136.59:843',
            '19' => '122.96.59.106:843',
            '20' => '101.71.27.120:80',
            '21' => '122.96.59.106:81',
            '22' => '111.1.36.6:80',
            '23' => '114.255.183.174:8080',
            '24' => '120.198.243.111:80',
            '25' => '218.240.156.82:80',
            '26' => '61.184.192.42:80',
            '27' => '119.6.144.74:83',
            '28' => '119.6.144.74:843',
            '29' => '124.202.217.134:8118',
            '30' => '221.10.102.203:83',
            '31' => '119.6.144.74:82',
            '32' => '119.6.144.74:80',
            '33' => '58.252.72.179:3128',
            '34' => '60.24.122.236:8118',
            '35' => '203.192.10.66:80',
            '36' => '221.10.102.203:81',
            '37' => '211.141.130.96:8118',
            '38' => '124.88.67.13:843',
            '39' => '119.6.144.74:81',
            '40' => '222.33.41.228:80',
            '41' => '221.10.102.203:843',
            '42' => '111.7.129.133:80',
            '43' => '124.88.67.13:83',
            '44' => '61.156.3.166:80',
            '45' => '218.204.140.212:8001',
            '46' => '116.236.203.238:8080',
            '47' => '122.96.59.106:80',
            '48' => '182.118.23.7:8081',
            '49' => '222.45.194.122:8118',
            '50' => '123.171.119.52:80'
        );

        $rand = rand(0,50);
        return $proxy[$rand];
    }
}