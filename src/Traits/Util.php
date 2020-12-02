<?php


namespace App\Traits;


use http\Env\Response;

trait Util
{
    public function generateEmailConfirmationCode($length)
    {

        //32 karakter olduğu için 16. dan itibaren uzunluk kadar oluştur
        return substr(md5(uniqid(rand(), true)), 0, $length);

    }

    public function slugify($text)
    {

        $before = array('ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ö', 'Ç', 'Ş');
        $after = array('i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 'o', 'c', 's');

        $clean = str_replace($before, $after, $text);
        $clean = preg_replace('/[^a-zA-Z0-9 ]/', '', $clean);
        $clean = preg_replace('!\s+!', '-', $clean);
        $clean = strtolower(trim($clean, '-'));

        return $clean;

    }

    // tarih formatı doğrula
    public function validateDate($date, $format = 'd-m-Y'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    // tarih farkını hesaplar. (Y-m-d H:i:s) formatında string girilmeli.
    public function dateDiffGetInterval($sDate1, $sDate2, $sUnit='H') {
        $nInterval = strtotime($sDate2) - strtotime($sDate1);
        if ($sUnit=='D') { // days
            $nInterval = $nInterval/60/60/24;
        } else if ($sUnit=='H') { // hours
            $nInterval = $nInterval/60/60;
        } else if ($sUnit=='M') { // minutes
            $nInterval = $nInterval/60;
        } else if ($sUnit=='S') { // seconds

        }
        return $nInterval;
    }

    public function fileUpload($file, $file_type, $file_size, $file_error, $temp, $upload_url): ?Response
    {
        //Kabul edilen uzantılar.
        $allowed_extensions = array(
            "gif", "jpeg", "jpg", "png", "txt", "pdf", "doc", "docx", "rtf",
            "mp4", "mov", "3gp", "ogg", "ppt", "pptx", "xls", "xlsx"
        );
        //Kabul edilen tipler.
        $allowed_files = array(
            "image/jpeg", "image/jpg", "image/gif", "image/png",
            "text/plain", "application/pdf", "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "application/rtf", "video/mp4", "video/mov", "video/3gp", "video/ogg",
            "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation"
        );
        //Dosya uzantisini al.
        $file_name = explode(".", $file);
        $file_ext = end($file_name);
        if (!in_array($file_ext, $allowed_extensions)) {
            return new Response(
                'unsupported file extension: ' . $file_ext,
                Response::HTTP_FORBIDDEN,
                array(
                    'file_name' => $file_name,
                    'file_ext' => $file_ext,
                    'msg' => 'unsupported file extension: ' . $file_ext
                )
            );
        }
        if (!in_array($file_type, $allowed_files)) {
            return new Response(
                'unsupported file type: ' . $file_type,
                Response::HTTP_FORBIDDEN,
                array(
                    'file_name' => $file_name,
                    'file_type' => $file_type,
                    'msg' => 'unsupported file type: ' . $file_type
                )
            );
        }
        // Dosya yükleme hatası varsa
        if ($file_error > 0):
            return new Response(
                'file_error: ' . $file_error,
                Response::HTTP_NOT_FOUND,
                array(
                    'file_name' => $file_name,
                    'file_type' => $file_type,
                    'msg' => 'file_error: ' . $file_error
                )
            );
        endif;
        // Aynı isimde dosya varsa uyar
        if (file_exists($upload_url . $file)):
            return new Response(
                'file_exists',
                Response::HTTP_CONFLICT,
                array(
                    'file_name' => $file_name,
                    'file_type' => $file_type,
                    'msg' => 'file_exists'
                )
            );
        endif;
        // Dosya varsa yükle
        if ($file_size > 0):
            // Benzersiz dosya ismi üret
            $gen_ = strtoupper(md5(uniqid(rand() . time(), true)));
            $gen_text =
                substr($gen_, 0, 8) . '-' .
                substr($gen_, 8, 4) . '-' .
                substr($gen_, 12, 4) . '-' .
                substr($gen_, 16, 4) . '-' .
                substr($gen_, 20);
            // Sadece imajların ismini hashle
            // DB kullanmadığımız için imaj dışındakilerin ismini gösteriyoruz
            $images = array("gif", "jpeg", "jpg", "png");
            if (in_array($file_ext, $images)):
                $newfilename = $gen_text . '.' . $file_ext;
            else:
                $newfilename = $file;
            endif;
            move_uploaded_file($temp, $upload_url . $newfilename);
            return new Response(
                'succesfully_uploaded',
                Response::HTTP_OK,
                array(
                    'file_name' => $file_name,
                    'file_ext' => $file_ext
                )
            );
        endif;
    }

    public function sendIOSNotification($message, $deviceToken, $apns_cert)
    {
        //ini_set('display_errors', 'On');
        //error_reporting(E_ALL);
        $passphrase = '123456';
        //$message = 'my push notification';
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $apns_cert);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        $fp = stream_socket_client(
            'ssl://gateway.sandbox.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        /*
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        echo 'Connected to APNS' . PHP_EOL;
        */
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
        );
        $payload = json_encode($body);
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        /*
        if (!$result)
            echo 'Message not delivered' . PHP_EOL;
        else
            echo 'Message successfully delivered' . PHP_EOL;
        */
        return fclose($fp);
    }

    public function sendAndroidNotification($ids = array(), $message)
    {
        $registrationIds = $ids;
        $fields = array
        (
            "registration_ids" => $registrationIds,
            "data" => array('message' => $message)
        );
        $headers = array
        (
            'Authorization: key=' . '',
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_exec($ch);
        return curl_close($ch);
    }

    public function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if (strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if (strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if (strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if (strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if (!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    public function bytesFormat($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' B';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    public function convertToBytes($from)
    {
        $number = substr($from, 0, -2);
        switch (strtoupper(substr($from, -2))) {
            case "KB":
                return $number * 1024;
            case "MB":
                return $number * pow(1024, 2);
            case "GB":
                return $number * pow(1024, 3);
            case "TB":
                return $number * pow(1024, 4);
            case "PB":
                return $number * pow(1024, 5);
            default:
                return $from;
        }
    }

}