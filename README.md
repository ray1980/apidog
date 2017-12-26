# apidog  
一个API接口签名生成和校验，接口安全库包。  
这个项目的开源的，可能拿去随便用。

>git地址：https://github.com/youwen21/apidog

## 项目说明

规划功能有：

+ 接口签名生成与校验（已实现）
+ 接口accessToken生成和校验（待开发）
+ 接口varchar一次性凭证生成和校验（待开发）

## composer安装说明

>composer require youwen/apidog


## 目录说明

```
apidog  
├─util           apiClient和apiServer使用到的共同功能
│  ├─ErrorContainer.php             错误信息容器
│  ├─Sign.php        签名生成类
│  └─ 
├─ApiClient.php        API客户端
├─ApiServer.php       API服务器端
```

# DEMO 使用方法

> 客户端DEMO

```php
$conf = [
    'app_id' => '123',
    'app_secret' => '456'
];

$data = ['a'=>'aa', 'b'=>'bb'];
$apiClient = new ApiClient($conf);
$sign = $apiClient->getSign($data);
return $sign;
```

> 服务端DEMO

```php

$requestSign = '8B72473A87D3D4AA986BB01823E35C5E';

$partnerConf = [
    'app_id' => '123',
    'app_secret' => '456'
];

$data = ['a'=>'aa', 'b'=>'bb'];
$server = new ApiServer($partnerConf);
$ret = $server->check($data, $requestSign);
// $server = new ApiServer($partnerConf, true, true);// 检查IP，检查时间戳
// $ret = $server->withIP('127.0.0.1')->withTime(time())->check($data, $requestSign);
if(!$ret){
    $EC = youwen\apidog\util\ErrorContainer::instance();
    $msg = $EC->getLastMsg();
    echo $msg;
}

```

# 接口传输内容加密解密   
数据很敏感的情况下，单sign签名字符串不能保存安全传输时， 建议使用Xxtea加密传输数据。  
Xxtea-php地址为：
>https://github.com/xxtea/xxtea-php


