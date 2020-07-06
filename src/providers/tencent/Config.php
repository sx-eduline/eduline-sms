<?php
declare (strict_types = 1);
namespace eduline\sms\providers\tencent;

use app\admin\logic\system\Config as SystemConfig;
use eduline\admin\libs\pageform\FormItem;
use eduline\admin\page\PageForm;
use eduline\sms\interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    protected static $key = 'system.package.sms.tencent';
    public static function page(): PageForm
    {
        $fields = [
            'appid'         => FormItem::make()->title('短信应用ID'),
            'appkey'        => FormItem::make()->title('短信应用key'),
            'sign_name'     => FormItem::make()->title('签名'),
            'template_code' => FormItem::make()->title('默认短信模板')->help('用于发送统一的验证码'),
        ];

        $form          = new PageForm();
        $form->pageKey = $fields;
        $form->withSystemConfig();
        $config          = self::get();
        $config['__key'] = self::$key;
        $form->datas     = $config;

        return $form;
    }

    /**
     * 获取配置
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2020-03-28
     * @return   [type]                         [description]
     */
    public static function get($name = null)
    {
        $config = SystemConfig::get(self::$key, []);

        if ($name) {
            return isset($config[$name]) ? $config[$name] : null;
        }

        return $config;
    }
}
