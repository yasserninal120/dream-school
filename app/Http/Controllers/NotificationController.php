<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public static function sendNotification($token, $title, $body)
    {
        $server_key = 'AAAA_gDIc40:APA91bGrLvp0KJsnNYy5YRlvY2MM5KlarK2FL5P7U2DCva-TreO-86fqf6QmwQ9ZF-3S_wQF_fMBOMFX9419rUfUphdBN9YzXwAXAhnfmX-IrOAyMSvvK8-ykpu9viBZktxrgyisu5YD';
        $URL = 'https://fcm.googleapis.com/fcm/send';
        $data = '{
                "to" : "' . $token . '",
                "notification": {
                    "title" : "' . $title . '",
                    "body" : "' . $body . '",
                    "sound": "default"
                },
                "apns": {
                    "payload": {
                        "alert": {
                            "title" : "' . $title . '",
                            "body" : "' . $body . '",
                        },
                        "aps": {
                            "sound": "default"
                        }
                    }
                }
            }';
            error_log($data);
        $ch = curl_init();
        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization:key=' . $server_key;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $rest = curl_exec($ch);

        if ($rest === false) {
            $result_noti = 0;
        } else {

            $result_noti = 1;
        }
        curl_close($ch);
        return $rest;
    }
}
