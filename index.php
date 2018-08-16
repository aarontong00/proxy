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
    if (isset($_SERVER['REDIRECT_URL'])) {
        $url_redirect = $_SERVER['REDIRECT_URL'];

    } else if (isset($_SERVER['REQUEST_URI'])) {
        $url_redirect = $_SERVER['REQUEST_URI'];
    }

    $real_url = trim($url_redirect, '/');

    $real_url_parse = parse_url($real_url);

    $real_url_path = $real_url_parse['path'];
    $real_url_path = rtrim($real_url_path, '/');
    $real_url_path = rtrim($real_url_path, '\\');


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

    $encode = mb_detect_encoding($dir_name, array('ASCII','UTF-8','GB2312','GBK','BIG5'));

    if (stripos(PHP_OS, 'WIN') !== false && $encode == 'EUC-CN') {
        $dir_name = iconv($encode, 'UTF-8', $dir_name);
        $file_name = iconv($encode, 'UTF-8', $file_name);
    } elseif ($encode == 'UTF-8') {
        $dir_name = iconv($encode, 'GBK', $dir_name);
        $file_name = iconv($encode, 'GBK', $file_name);
    }

    if (!is_dir($dir_name)) {
        mkdir($dir_name, 0777, true);
    }

    $file_name = trim($file_name);

    $fp = fopen($dir_name . '/' . $file_name, 'wb');

    $urls = parse_url( $real_url);

    $explode_url = explode('/', $urls['path']);
    $real_url = '';
    foreach ($explode_url as $value) {
        $real_url .= '/' . rawurlencode($value);
    }

    if (!isset($urls['host'])) {
        $real_url = ltrim($real_url, '/');
    } else {
        $real_url = ltrim($real_url, '/');

        $real_url = $urls['scheme'] . '://' . $urls['host'] . '/' . $real_url;
    }


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

