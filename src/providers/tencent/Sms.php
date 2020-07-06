<?php
declare (strict_types = 1);
namespace eduline\sms\providers\tencent;

use eduline\sms\interfaces\SmsInterface;
use eduline\sms\providers\tencent\Config;
use Qcloud\Sms\SmsMultiSender;
use think\facade\Validate;

class Sms implements SmsInterface
{
    /**
     * 发送接口
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2020-04-07
     * @param    [type]                         $phoneNumbers [description]
     * @param    string                         $code         [description]
     * @param    [type]                         $config       [description]
     * @return   [type]                                       [description]
     */
    public function send($phoneNumbers, $templateParam = [], $config = [])
    {
        $phoneNumbers = $this->getPhoneNumbers($phoneNumbers);
        if (!$phoneNumbers) {
            return false;
        }

        // 模板参数,对应腾讯短信模板中的变量,注意腾讯短信模板变量从1开始计数
        $templateParam = $this->getTemplateParam($templateParam);

        // 发送配置,目前仅能配置模板ID
        $config = $this->getSendConfig($config);
        try {
            $msender = new SmsMultiSender(Config::get('appid'), Config::get('appkey'));
            $result = $msender->sendWithParam("86", $phoneNumbers,
                $config, $templateParam, Config::get('sign_name'), "", "");
            $rsp = json_decode($result);

            if($rsp->result === 0){
                return true;
            }
            
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * 获取发送的手机号
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2019-06-03
     * @param    [type]                         $phoneNumbers [description]
     * @return   [type]                                       [description]
     */
    private function getPhoneNumbers($phoneNumbers)
    {
        $phoneNumbers = !is_array($phoneNumbers) ? explode(',', $phoneNumbers) : $phoneNumbers;
        $phones       = [];
        foreach ($phoneNumbers as $k => $phone) {
            // 检测手机号是否+86
            if (Validate::is($phone, 'mobile')) {
                $phones[$k] = $phone;
            }
        }

        return $phones;
    }

    /**
     * 获取模板参数
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2019-06-02
     * @param    [type]                         $templateParam [description]
     * @return   [type]                                        [description]
     */
    protected function getTemplateParam($templateParam)
    {
        if (!is_array($templateParam)) {
            $templateParam = [
                $templateParam,
            ];
        }

        return [$templateParam['code']];
        
        return array_values($templateParam);
    }

    /**
     * 获取发送短信配置数据
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2019-06-02
     * @param    [type]                         $sendConfig [description]
     * @return   [type]                                     [description]
     */
    protected function getSendConfig($sendConfig)
    {
        $sendConfig = $sendConfig ?: 'template_code';
        if (is_string($sendConfig) && $sendConfig) {
            return Config::get($sendConfig);
        }

        return [];
    }
}
