<?php
declare (strict_types = 1);
namespace eduline\sms\admin\service;

use app\admin\logic\system\Config as SystemConfig;
use app\common\service\BaseService;
use eduline\admin\libs\pageform\FormItem;
use eduline\admin\libs\pagelist\ListItem;
use eduline\admin\page\PageForm;
use eduline\admin\page\PageList;
use eduline\sms\Provider;

class Config extends BaseService
{
    /**
     * 上传列表
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2020-03-27
     * @return   [type]                         [description]
     */
    public function index()
    {
        $data = Provider::getProviders();
        $sms  = SystemConfig::get('system.package.sms');
        // 查询配置
        foreach ($data as $key => $provider) {
            // 储存配置key
            $__key                = 'system.package.upload.' . $provider['key'];
            $data[$key]['__key']  = $__key;
            $data[$key]['config'] = SystemConfig::get($__key);
            $data[$key]['status'] = (isset($sms['provider']) && $sms['provider'] == $provider['key']) ? 1 : 0;
        }
        // 定义字段
        $keyList = [
            'key'    => ListItem::make()->title('标识'),
            'name'   => ListItem::make()->title('名称'),
            'desc'   => ListItem::make('custom')->title('描述'),
            'status' => ListItem::make('custom')->title('启用状态'),
        ];

        // 设置表单
        $list = app(PageList::class);
        // 表单字段
        $list->pageKey = $keyList;
        $list->datas   = $data;

        return $list->send();
    }

    /**
     * 上传配置
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2020-03-27
     * @return   [type]                         [description]
     */
    public function config($provider)
    {
        // 配置界面
        $form = Provider::getProviderConfigPage($provider);

        return $form->send();
    }

    /**
     * 短信验证码配置
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2020-04-17
     * @return   [type]                         [description]
     */
    public function verify()
    {
        $key    = 'system.package.sms.verify';
        $fields = [
            'length'  => FormItem::make('inputNumber')->title('验证码位数')->min(4)->max(6)->help('可配置4-6位短信验证码')
        ];

        $form          = new PageForm();
        $form->pageKey = $fields;
        $form->withSystemConfig();
        $config          = SystemConfig::get($key, []);
        $config['__key'] = $key;
        $form->datas     = $config;

        return $form->send();
    }
}
