<?php


namespace app\modules\weixin\controllers;


use app\common\components\BaseWebController;
use app\models\book\Book;
use app\common\services\UrlService;
use Yii;
use yii\log\FileTarget;
class MsgController extends BaseWebController
{
    public function actionIndex( )
    {
        if (!$this->checkSignature()) {
            if( !$this->checkSignature() ){
                $this->record_log( "校验错误" );//将错误日志记录下来
                //可以直接回复空串，微信服务器不会对此作任何处理，并且不会发起重试
                return 'error signature ~~';
            }
        }

        if (array_key_exists('echostr', $_GET) && $_GET['echostr']) {//用于微信第一次认证的（因为当微信通过后就不会再发这信息了）
            return $_GET['echostr'];
        }
        //因为很多都设置了register_globals禁止,不能用$GLOBALS["HTTP_RAW_POST_DATA"];
        $xml_data = file_get_contents("php://input");
        $this->record_log( "[xml_data]:". $xml_data );
        if( !$xml_data ){
            return 'error xml ~~';
        }
        $xml_obj = simplexml_load_string( $xml_data, 'SimpleXMLElement', LIBXML_NOCDATA);//将微信服务器发过来的xml数据转换微信对象

        $from_username = $xml_obj->FromUserName; //发送人
        $to_username = $xml_obj->ToUserName; //接收人
        $msg_type = $xml_obj->MsgType;//信息类型

        $res = [ 'type'=>'text','data'=>$this->defaultTip() ];//指定默认回复消息类型
        switch ( $msg_type ){
            case "text":
                    $kw = trim( $xml_obj->Content );
                    $res = $this->search( $kw );//将搜索后的数据及type赋值给$res 即这时$res=[$type='rich',$data=<ArticleCount>%s</ArticleCount><Articles>%s</Articles>]
                break;
            case "event":
//                $res = $this->parseEvent( $xml_obj ); //事件调用方法
                break;
            default:
                break;
        }

        switch($res['type']){
            case "rich":
                return $this->richTpl($from_username,$to_username,$res['data']);//判断回复的消息type，为图文类型，则将图文完整xml模板拼接上
                break;
            default:
                return $this->textTpl($from_username,$to_username,$res['data']);
        }

        return "hello world";
    }
    /*
     * 从数据库中搜索数据
     */
    private function search( $kw ){
        $query = Book::find()->where([ 'status' => 1 ]);
        $where_name = [ 'LIKE','name','%'.strtr( $kw ,['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']).'%', false ];
        $where_tag = [ 'LIKE','tags','%'.strtr( $kw ,['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']).'%', false ];
        $query->andWhere([ 'OR',$where_name,$where_tag ]);
        $res = $query->orderBy([ 'id' => SORT_DESC ])->limit( 3 )->all();
        $data = $res?$this->getRichXml( $res ):$this->defaultTip();
        $type = $res?"rich":"text";//数据库中有数据就将回复图文消息即type为rich，没有就将回复文本消息即type为text
        return ['type' => $type ,"data" => $data];
    }
    //验证基本配置（别的方法也能调用该方法所以这里设为public）
    public function checkSignature(){
        $signature = trim( $this->get("signature","") );
        $timestamp = trim( $this->get("timestamp","") );
        $nonce = trim( $this->get("nonce","") );
        $tmpArr = array( \Yii::$app->params['weixin']['token'],$timestamp,$nonce );
        sort( $tmpArr,SORT_STRING );
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr ==  $signature ){
            return true;
        }else{
            return false;
        }
    }
    private function textTpl($from_username,$to_username,$data)
    {
        $tpl=<<<EOT
        <xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>
EOT;
        return sprintf($tpl,$from_username,$to_username,time(),$data);
    }
    //富文本
    private function richTpl( $from_username ,$to_username,$data){
        $tpl = <<<EOT
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
%s
</xml>
EOT;
        return sprintf($tpl, $from_username, $to_username, time(), $data);//最后一个%s用$data代替这时候就拼接成了完整的回复图文消息xml
    }                                                                       //所以这里回复消息用到了两次拼接模板

    /*
     * 生成图文消息xml数据
     */
    private function getRichXml( $list ){
        $article_count = count( $list );
        $article_content = "";
        foreach($list as $_item){
            $tmp_description = mb_substr( strip_tags( $_item['summary'] ),0,20,"utf-8" );
            $tmp_pic_url = UrlService::buildPicUrl( "book",$_item['main_image'] );
            $tmp_url = UrlService::buildMUrl( "/product/info",[ 'id' => $_item['id'] ] );
            $article_content .= "
<item>
<Title><![CDATA[{$_item['name']}]]></Title>
<Description><![CDATA[{$tmp_description}]]></Description>
<PicUrl><![CDATA[{$tmp_pic_url}]]></PicUrl>
<Url><![CDATA[{$tmp_url}]]></Url>
</item>";
        }

        $article_body = "<ArticleCount>%s</ArticleCount>
<Articles>
%s
</Articles>";
        return sprintf($article_body,$article_count,$article_content);
    }
    /**
     * 默认回复语
     */
    private function defaultTip(){
        $resData = <<<EOT
没找到你想要的东西（：\n
EOT;
        return $resData;
    }
    //记录回复消息日志
    public   function record_log($msg){
        $log = new FileTarget();
        $log->logFile = Yii::$app->getRuntimePath() . "/logs/weixin_msg_".date("Ymd").".log";
        $request_uri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
        $log->messages[] = [
            "[url:{$request_uri}][post:".http_build_query($_POST)."] [msg:{$msg}]",
            1,
            'application',
            microtime(true)
        ];
        $log->export();
    }

}