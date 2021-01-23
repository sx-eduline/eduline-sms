<?php
declare (strict_types=1);

namespace eduline\sms\interfaces;

/**
 * 短信接口类
 */
interface SmsInterface
{
    /**
     * 短信发送
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2019-06-05
     *
     * @param string|array $phoneNumbers   手机号,如果是多个,可以是数组或逗号分割的字符串
     * @param string|array $templateParams 模板变量,如果是验证码,可以直接填写要发送的验证码数字
     * @param string|array $config         配置,可以自定义数组配置,或者直接给字符串标识,该字符串标识需要在各自短信发送模型中处理
     * @return   boolean 是否发送成功
     */
    public function send($phoneNumbers, $templateParams, $config);
}
