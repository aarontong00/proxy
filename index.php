<?php
/**
 * Created by PhpStorm.
 * User: yons
 * Date: 2018/7/30
 * Time: 17:45
 * 静态资源代理程序  用来做静态资源的代理
 * 服务器  iis
 */
if ($_SERVER['REQUEST_URI'] == '/') {
    echo '404';
    exit();

} else {
    $real_url = trim($_SERVER['REQUEST_URI'], '/');

    $real_url_parse = parse_url($real_url);

    $real_url_path = $real_url_parse['path'];

    $real_url_query = '';

    if (isset($real_url_parse['query'])) {
        $real_url_query = $real_url_parse['query'];
    }


    $real_urls = explode('/', $real_url_path);
    if (count($real_urls) < 2) {
        exit('too short');
    }

    if (stripos($real_urls[0], '.') === false) {
        exit('not  domain');
    }

    $file_name = array_pop($real_urls);
    $dir_name = __DIR__ . '/' . implode('/', $real_urls);
    if (!is_dir($dir_name)) {
        mkdir($dir_name, 0777, true);
    }

    $fp = fopen($dir_name . '/' . $file_name, 'wb');

    $ch = curl_init($real_url);
    curl_setopt($ch,CURLOPT_ENCODING ,'utf8');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_FILE, $fp);

    curl_exec($ch);
    curl_close($ch);

    fclose($fp);

    $file_name_type = strrchr($file_name, '.');


    if ($file_name_type !== false) {
        $file_name_type = str_replace('.', '', $file_name_type);
        $file_name_type = strtolower($file_name_type);

        switch ($file_name_type) {
            case 'png':
                header('Content-Type: image/png');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'js':
                header('Content-Type: application/javascript');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'css':
                header('Content-Type: text/css');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'gif':
                header('Content-Type: image/gif');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'mp3':
                header('Content-Type: audio/mpeg');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'txt':
                header('Content-Type: text/plain');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'json':
                header('Content-Type: application/json');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'xml':
                header('Content-Type: text/xml');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'woff':
                header('Content-Type: font/x-font-woff');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'woff2':
                header('Content-Type: application/octet-stream');
                readfile($dir_name . '/' . $file_name);
                break;
            case 'ttf':
                header('Content-Type: application/octet-stream');
                readfile($dir_name . '/' . $file_name);
                break;
            default:
                header('Content-Type: text/html; charset=utf-8');
                readfile($dir_name . '/' . $file_name);
                break;

        }


    }


}

