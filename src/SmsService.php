<?php
namespace eduline\sms;

use think\facade\Route;
use think\Service;

class SmsService extends Service
{
    public function boot()
    {
        $this->registerRoutes(function () {
            /** 接口路由--需要移入到composer中 */
            Route::group('system/package/sms', function () {
                Route::get('/list', '@index')->name('system.package.sms'); // 短信配置页面
                Route::get('/<provider>/config', '@config')->pattern(['provider' => '[a-zA-Z_]+'])->name('system.package.sms.config'); // 短信账号配置页面
                Route::get('/verify', '@verify')->name('system.package.sms.config');
            })->prefix('\eduline\sms\admin\service\Config')->middleware(['adminRoute']);
        });
    }
}
