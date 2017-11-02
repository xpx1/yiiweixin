<?php
//配置域名即链接 (为了便于域名信息的更改)
return [
    'domain' =>[
        'www'=>'http://www.yiiweixin.com',//ngrok域名  http://www.yiiweixin.com
        'web'=>'http://www.yiiweixin.com/web', //http://www.yiiweixin.com/web
        'm'=>'http://www.yiiweixin.com/m' //http://www.yiiweixin.com
    ] ,
    'upload' => [
        'avatar' => '/uploads/avatar',
        'brand' => '/uploads/brand',
        'book' => '/uploads/book',
    ],
    'weixin' => [
        'appid' => 'wxf703ccc61698d99a', //微信公众号的appid 这里是测试号的
        'sk' => '255290819bd28972e3b2379073c30d96', //微信公众号的AppSecret 这里是测试号的
        'token' => 'weixin', //基本配置里填的token
        'aeskey' => 'NPPUF38g4hqeurAISpB2PGiNOrbhyhHTmwOWYr4sj', //基本配置里填的EncodingAESKey
        'pay' => [
            'key' => '根据实际情况填写',
            'mch_id' => '根据实际情况填写',
            'notify_url' => [
                'm' => '/pay/callback'
            ]
        ]
    ]
];
