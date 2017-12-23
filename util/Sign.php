<?php

namespace youwen\apidog\util;

/**
 * 生成检查Sign
 * 根据Token获取用户信息
 * 生成规则
 * 1，按数组key值从小到大排序
 * 2，以html方式把数组转为字符串$string
 * 3，$string链接&app_secret={$app_secret}
 */
class Sign
{

    /**
     * 生成签名
     */
    public static function createSign($data, $app_secret)
    {
        if(is_string($data)){
            $str = $data . '&app_secret='.$app_secret;
        }else if(is_array($data)){
            ksort($data);
            // $str = self::arr2str($data);
            $str = http_build_query($data);
            $str .= '&app_secret='.$app_secret;
        }else{
            return false;
        }
        $mySign = strtoupper(md5($str));
        return $mySign;
    }

    public static function arr2str($data)
    {
        $str = '';
        foreach ($data as $key => $value) {
            if(is_array($value)){
                $value = http_build_query( $value );
            }
            $str .= $key.'=' . $value . '&';
        }
        return rtrim($str, '&');
    }

}