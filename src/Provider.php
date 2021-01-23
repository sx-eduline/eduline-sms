<?php
declare (strict_types=1);

namespace eduline\sms;

class Provider
{
    /**
     * 获取储存空间列表
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-03-27
     *
     * @return   [type]                         [description]
     */
    public static function getProviders()
    {
        $dir       = __DIR__ . '/' . 'providers';
        $providers = [];
        // 遍历文件夹
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ((is_dir($dir . '/' . $file)) && $file != '.' && $file != '..') {
                    // 读取.ini配置文件
                    $config = $dir . '/' . $file . '/' . '.ini';
                    if (is_file($config)) {
                        $providers[] = parse_ini_file($config, true, INI_SCANNER_TYPED);
                    }
                }
            }
            closedir($dh);
        }

        return $providers;
    }

    /**
     * 获取配置界面表单
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-03-28
     *
     * @param string $provider [description]
     * @return   [type]                                [description]
     */
    public static function getProviderConfigPage(string $provider)
    {
        $stdclass = __NAMESPACE__ . '\\providers\\' . $provider . '\\Config';

        return $stdclass::page();
    }

    /**
     * 获取储存配置字段信息
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-03-27
     *
     * @param string $provider 储存端标识
     * @return   [type]                                [description]
     */
    public static function getProviderConfig(string $provider, $getClass = false)
    {
        $stdclass = __NAMESPACE__ . '\\providers\\' . $provider . '\\Config';

        return $getClass ? new $stdclass() : $stdclass::get();

    }
}
