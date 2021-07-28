<?php
declare (strict_types=1);

namespace eduline\sms;

use app\admin\logic\system\Config as SystemConfig;
use app\common\exception\ConfigException;

class Client
{
    public static function send($phoneNumbers, $templateParam = [], $config = [])
    {
        $sms = SystemConfig::get('system.package.sms', [], request()->mhm_id);
        if (!$sms) {
            throw new ConfigException('短信配置错误', '短信发送失败');
        }

        // 当前处理类
        $class = __NAMESPACE__ . '\\providers\\' . $sms['provider'] . '\\Sms';

        $provider = new $class();

        return $provider->send($phoneNumbers, $templateParam, $config);
    }
}
