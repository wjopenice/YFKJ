<?php
namespace AlibabaCloud\Client;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
class Sms{
    public function smscode($tel,$code){
        AlibabaCloud::accessKeyClient('LTAIPTCxrOfaWDc7', 'ZcG78bxmB4LPiAVE5OTNuh8hj2A5A7')
            ->regionId('LTAIPTCxrOfaWDc7')
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
                        'RegionId' => "LTAIPTCxrOfaWDc7",
                        'PhoneNumbers' => $tel,
                        'SignName' => "FOMO",
                        'TemplateCode' => "SMS_172743355",
                        'TemplateParam' => json_encode(["code"=>$code]),
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}