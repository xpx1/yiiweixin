<?php

namespace app\models\sms;

use app\common\components\HttpClient;
use Yii;

/**
 * This is the model class for table "sms_captcha".
 *
 * @property string $id
 * @property string $mobile
 * @property string $captcha
 * @property string $ip
 * @property string $expires_at
 * @property integer $status
 * @property string $created_time
 */
class SmsCaptcha extends \yii\db\ActiveRecord
{
    /*
     * 验证手机验证码
     */
    public static function checkCaptcha( $mobile , $captcha )
    {
        $info = self::find()->where( [ 'mobile' => $mobile,'captcha' => $captcha ] )->one();
        if( $info &&  strtotime( $info['expires_at'] ) >= time()  ){
            $info->expires_at = date("Y-m-d H:i:s",time() - 1 );
            $info->status = 1;
            $info->save( 0 );
            return true;
        }
        return false;
    }
    /**
     * @param $mobile string 手机号码
     * @param $ip int 手机ip
     * @param $sign string 签名
     * @param $channel string 渠道 可理解为不同的接口供应商
     * @return bool
     */
    public function geneCustomCaptcha( $mobile,$ip = '',$sign = '',$channel = '' ){
        $this->mobile = $mobile;
        $this->ip = $ip;
        $this->captcha = rand(10000,99999);
        $this->expires_at = date("Y-m-d H:i:s",time() + 60 * 10  );
        $this->created_time = date( "Y-m-d H:i:s");
        $this->status = 0 ;
        //对接了手机验证码供应商，todo实现发验证码
//        $url="https://sapi.k780.com/?app=sms.send&tempid=51224&param=usernm%3Dusernm%26code%3D{$code}&phone={$mobile}&appkey=22516&sign=f5c57e1ba28a99e3a30b08e754ac05d1";
//       $res = HttpClient::get($url);
//       var_dump($res);
        return $this->save( 0 );

    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_captcha';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expires_at', 'created_time'], 'safe'],
            [['status'], 'required'],
            [['status'], 'integer'],
            [['mobile', 'ip'], 'string', 'max' => 20],
            [['captcha'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => 'Mobile',
            'captcha' => 'Captcha',
            'ip' => 'Ip',
            'expires_at' => 'Expires At',
            'status' => 'Status',
            'created_time' => 'Created Time',
        ];
    }
}
