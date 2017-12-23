<?php

namespace youwen\apidog;

use youwen\apidog\util\Sign;
use youwen\apidog\util\ErrorContainer;

/*
    服务器端
 */
class ApiServer
{
    private $_partner = [ //effective_time
        'app_id' => '',
        'app_secret' => '',
        'expiryTime'=>'60', //过期时间 60秒
        'allowIPs' => '127.0.0.1,192.168.1.10', //0.0.0.0,
        'deniedIPs' => '',
    ];
    private $_checkIP = false;
    private $_checkTime = false;

    public $EC;

    public $client = [
        'ip' => '192.168.1.1',
        'timestamp' => ''
    ];


    /**
     * config需要是数组或者继承arrayaccess的对象
     * @param  [type] $config [description]
     * @author baiyouwen
     */
    public function __construct($partnerConfig, $checkIP=false, $checkTime=false)
    {
        $this->_partner = array_merge($this->_partner,$partnerConfig);
        $this->_checkIP = $checkIP;
        $this->_checkTime = $checkTime;

        $this->EC = ErrorContainer::instance();
    }


    public function check($data, $requestSign='')
    {
        // 是否检查IP
        if($this->_checkIP && !$this->checkIP()){
            return false;
        }
        // 是否检查时间
        if($this->_checkTime && !$this->checkTime()){
            return false;
        }
        
        // 检查签名
        $sign = Sign::createSign($data, $this->_partner['app_secret']);
        if ($sign == $requestSign){
            return true;
        }
        
        $this->EC->setError('4000', 'check sign failed');
        return false;
    }

    public function checkIP()
    {
        // 是否IP黑名单
        if(strpos($this->_partner['deniedIPs'], $this->client['ip']) !== false){
            $this->EC->setError('4001', 'ip denied');
            return false;
        }
        // 是否容许所有IP
        if(strpos($this->_partner['allowIPs'], '0.0.0.0') !== false){
            return true;
        }

        // 是否在IP白名单中
        if(strpos($this->_partner['allowIPs'], $this->client['ip'].',') !== false){
            return true;
        }

        $this->EC->setError('4002', 'ip not allowed');
        return false;
    }

    public function checkTime()
    {
        $real = $this->client['timestamp'];
        // 时间传的太大
        if(time() - $real <0){
            $this->EC->setError('4003', 'time not allowed');
            return false;
        }
        // 时间传的太小
        if(time() - $real > $this->_partner['expiryTime']){
            $this->EC->setError('4004', 'time not invalid');
            return false;
        }
        return true;
    }

    /*        上方是签名检查，下方是参数设置          */

    /**
     * 配置$_conf信息
     */
    // public function setConf(array $config)
    // {
    //     $this->_conf = array_merge($this->_conf, $config);
    //     return $this;
    // }

    /**
     * 客户端信息
     */
    public function withClientInfo($clientInfo=[])
    {
        $this->client = array_merge($this->client, $clientInfo);
        return $this;
    }

    /**
     * 客户端请求时间
     */
    public function withTime($timestamp='')
    {
        $this->client['timestamp'] = $timestamp;
        return $this;
    }

    /**
     * 客户端IP地址
     */
    public function withIP($ip='')
    {
        $this->client['ip'] = $ip;
        return $this;
    }
}
