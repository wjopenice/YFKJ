<?php

class Sms{
    public function testsmsAction(){
        $tel = "xxxxxxxxx";
        $code = "xxxx";
        include APP_PATH."/vendor/alibabacloud/client/src/AlibabaCloud.php";
        $message['message_keyid'] = "LTAIPTCxrOfaWDc7";
        $message['message_keysecret'] = "ZcG78bxmB4LPiAVE5OTNuh8hj2A5A7";
        $message['message_sign'] = "FOMO";
        $message['message_code'] = "SMS_172743355";
        $message['message_keyword'] = "code";
        AlibabaCloud::accessKeyClient($message['message_keyid'], $message['message_keysecret'])
            ->regionId(time())
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => time(),
                        'PhoneNumbers' => $tel,
                        'SignName' => $message['message_sign'],
                        'TemplateCode' => $message['message_code'],
                        'TemplateParam' => json_encode([$message['message_keyword']=>$code]),
                        'OutId' => "",
                    ],
                ])
                ->request();
            print_r($result->toArray());
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }

    }
}