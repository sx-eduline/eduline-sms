<?php
declare (strict_types=1);

namespace eduline\sms\providers\aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use eduline\sms\interfaces\SmsInterface;
use think\facade\Validate;

class Sms implements SmsInterface
{
    /**
     * 发送接口
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-04-07
     *
     * @param    [type]                         $phoneNumbers [description]
     * @param string $code [description]
     * @param    [type]                         $config       [description]
     * @return   [type]                                       [description]
     */
    public function send($phoneNumbers, $templateParam = [], $config = [])
    {
        $phoneNumbers = $this->getPhoneNumbers($phoneNumbers);
        if (!$phoneNumbers) {
            return false;
        }
        $templateParam = $this->getTemplateParam($templateParam);
        // 参数定义
        $postParams = [
            'RegionId'      => "cn-hangzhou",
            'PhoneNumbers'  => $phoneNumbers,
            'SignName'      => Config::get('sign_name'),
            'TemplateCode'  => Config::get('template_code'),
            'TemplateParam' => $templateParam,
        ];

        $config = $this->getSendConfig($config);

        $postParams = array_merge($postParams, $config);

        try {
            AlibabaCloud::accessKeyClient(Config::get('accessKey_id'), Config::get('accessKey_secret'))
                ->regionId('cn-hangzhou')
                ->asDefaultClient();
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => $postParams,
                ])
                ->request();
            $result = $result->toArray();
            if ($result['Code'] == 'OK') {
                return true;
            }
        } catch (ClientException $e) {
            // echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            // echo $e->getErrorMessage() . PHP_EOL;
        }

        return false;
    }

    /**
     * 获取发送的手机号
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2019-06-03
     *
     * @param    [type]                         $phoneNumbers [description]
     * @return   [type]                                       [description]
     */
    private function getPhoneNumbers($phoneNumbers)
    {
        $phoneNumbers = !is_array($phoneNumbers) ? explode(',', (string)$phoneNumbers) : $phoneNumbers;
        $phones       = [];
        foreach ($phoneNumbers as $k => $phone) {
            // 检测手机号是否+86
            if (Validate::is($phone, 'mobile')) {
                $phones[$k] = $phone;
            }
        }

        return implode(',', $phones);
    }

    /**
     * 获取模板参数
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2019-06-02
     *
     * @param    [type]                         $templateParam [description]
     * @return   [type]                                        [description]
     */
    protected function getTemplateParam($templateParam)
    {
        if (!is_array($templateParam)) {
            $templateParam = [
                "code" => $templateParam,
            ];
        }

        return json_encode($templateParam, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取发送短信配置数据
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2019-06-02
     *
     * @param    [type]                         $sendConfig [description]
     * @return   [type]                                     [description]
     */
    protected function getSendConfig($sendConfig)
    {
        $sendConfig = $sendConfig ?: 'template_code';
        if (is_string($sendConfig) && $sendConfig) {
            $template = Config::get($sendConfig);
            return ['TemplateCode' => $template];
        }

        return $sendConfig ?: [];
    }
}
