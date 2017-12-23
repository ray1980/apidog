<?php

namespace youwen\apidog;

use youwen\apidog\util\Sign;

class ApiClient
{
    private $_partner;

    public function __construct($partnerConfig)
    {
        $this->_partner = $partnerConfig;
    }

    public function getSign($data)
    {
        $sign = Sign::createSign($data, $this->_partner['app_secret']);
        return $sign;
    }
}
