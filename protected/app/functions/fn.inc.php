<?php declare(strict_types = 1);

function strposa($haystack, $needle, $offset=0)
{
    if (!is_array($needle)) {
        $needle = array($needle);
    }

    foreach ($needle as $query) {
        if (strpos($haystack, $query, $offset) !== false) {
            return true;
        }
    }

    return false;
}

if (!function_exists('hash_equals')) {
    function hash_equals($str1, $str2)
    {
        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) {
                $ret |= ord($res[$i]);
            }
            return !$ret;
        }
    }
}

function checkUrlLink($path = [], $needle = [])
{
    $needle = str_replace(array("\r","\n"), '', $needle);
    $needle = preg_split('/\;+/', $needle, -1, PREG_SPLIT_NO_EMPTY);

    $valid = false;

    if (!empty($needle) && !empty($path)) {
        foreach ($needle as $item) {
            $compared = preg_split('/\/+/', $item, -1, PREG_SPLIT_NO_EMPTY);

            if (count($path) <= count($compared) || end($compared) == '*') {
                $difference = array_diff($compared, $path);

                if (!empty($difference)) {
                    $difference = array_values($difference);

                    if ($difference[0] == '*') {
                        $valid = true;
                    }
                } else {
                    $valid = true;
                }
            }
        }
    } elseif (empty($path) && in_array('main', $needle)) {
        $valid = true;
    }

    return $valid;
}

function _session_start()
{
    $sn = session_name();

    if (isset($_COOKIE[$sn])) {
        $sessid = $_COOKIE[$sn];
    } elseif (isset($_GET[$sn])) {
        $sessid = $_GET[$sn];
    } else {
        return session_start();
    }

    if (!preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $sessid)) {
        return false;
    }

    return session_start();
}

function simpleUpload($name = '', $group = '')
{
    if (!$group) {
        $group = 'upload_' . substr(md5(uniqid()), 3, 8)  . '_' . substr(md5(uniqid()), 5, 10);
    }

    $files = new Files();
    $flist = $files->upload($name, $group);

    return $flist;
}

function humanFileSize($size)
{
    if ($size >= 1073741824) {
        $fileSize = round($size / 1024 / 1024 / 1024, 1) . 'GB';
    } elseif ($size >= 1048576) {
        $fileSize = round($size / 1024 / 1024, 1) . 'MB';
    } elseif ($size >= 1024) {
        $fileSize = round($size / 1024, 1) . 'KB';
    } else {
        $fileSize = $size . ' bytes';
    }
    return $fileSize;
}

function clean_data($html)
{
    $html = str_replace("\n", " ", str_replace("\r", "", $html));

    $content = '';

    if ($content = strstr($html, '<!-- profile -->')) {
        $content = trim(substr($content, strlen('<!-- profile -->')));
        $content = trim(substr($content, 0, (0 - strlen($content) + strpos($content, '<!-- /profile -->'))));
    }

    $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);
    $content = preg_replace("/\&[^\;]*\;/", "", $content);
    $content = str_replace('&quot;', '', $content);
    $content = trim($content);

    return $content;
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), [ '.', '..', '.gitkeep', '.gitignore' ]);

        foreach ($files as $file) {
            if (is_dir($dir.DS.$file)) {
                rrmdir($dir.DS.$file);
            } else {
                unlink($dir.DS.$file);
            }
        }

        reset($files);
        rmdir($dir);
    }
}

function _curl($url)
{
    $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(
        CURLOPT_CUSTOMREQUEST  => "GET",        //set request type post or get
        CURLOPT_POST           => false,        //set to GET
        CURLOPT_USERAGENT      => $user_agent, //set user agent
        CURLOPT_COOKIEFILE     => "cookie.txt", //set cookie file
        CURLOPT_COOKIEJAR      => "cookie.txt", //set cookie jar
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
   );

    $ch      = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err     = curl_errno($ch);
    $errmsg  = curl_error($ch);
    $header  = curl_getinfo($ch);
    curl_close($ch);

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;

    return $header;
}

if (!function_exists('json_encode')) {
    function json_encode($data)
    {
        $json = new Services_JSON();
        return($json->encode($data));
    }
}

if (!function_exists('json_decode')) {
    function json_decode($data, $bool)
    {
        if ($bool) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        } else {
            $json = new Services_JSON();
        }
        return($json->decode($data));
    }
}

function to_money($number = 0, $d = 0)
{
    return number_format(floatval($number), $d, ',', ' ');
}

function __get($val)
{
    return isset($_GET[$val])?$_GET[$val]:false;
}

function __post($key = '', $post = [])
{
    $result = false;

    if (isset($post) && !empty($post)) {
        if (isset($post[$key])) {
            $result = $post[$key];
        }
    } elseif (!empty($_POST)) {
        if (isset($_POST[$key])) {
            $result = $_POST[$key];
        }
    }

    return $result;
}

function __cookie($val)
{
    return isset($_COOKIE[$val])?$_COOKIE[$val]:false;
}

/**
 * Check that URL is valid and exists.
 * @param string $url Url to check
 * @return bool TRUE when valid | FALSE anyway
 */
function urlExists($url)
{
    // Remove all illegal characters from a url
    $url = filter_var($url, FILTER_SANITIZE_URL);

    // Validate URI
    if (filter_var($url, FILTER_VALIDATE_URL) === false
        // check only for http/https schemes.
        || !in_array(strtolower(parse_url($url, PHP_URL_SCHEME)), ['http','https'], true)
    ) {
        return false;
    }

    // Check that url exists
    $file_headers = @get_headers($url);
    return !!(!is_array($file_headers) || strpos($file_headers[0], '404') === false);
}

function to_base($string)
{
    if (is_string($string)) {
        $string = trim($string);
        return addslashes($string);
    }

    return $string;
}

function from_base($string)
{
    return $string ? stripslashes($string) : $string;
}

function scanDIRR($dir)
{
    if (is_dir($dir)) {
        return array_diff(scandir($dir), [ '.', '..', '.gitkeep', '.gitignore' ]);
    }

    return [];
}

function rDir($dir)
{
    $arr = [];
    $d = scandir($dir);
    foreach ($d as $v) {
        if ($v != "." and $v != "..") {
            $arr[] = array(
                "name" => iconv("windows-1251", "UTF-8", $v),
                "info" => getImageSize(PATH_ROOT.IMPORT_DIR.$v),
           );
        }
    }

    return $arr;
}

function getMonthString($month) {
    $month_number = '00';
    $days = 0;

    switch ($month) {
        case 'Январь':
          $month_number = '01';
          $days = 31;
          break;
        case 'Февраль':
          $month_number = '02';
          $days = 28;
          break;
        case 'Март':
          $month_number = '03';
          $days = 31;
          break;
        case 'Апрель':
          $month_number = '04';
          $days = 30;
          break;
        case 'Май':
          $month_number = '05';
          $days = 31;
          break;
        case 'Июнь':
          $month_number = '06';
          $days = 30;
          break;
        case 'Июль':
          $month_number = '07';
          $days = 31;
          break;
        case 'Август':
          $month_number = '08';
          $days = 31;
          break;
        case 'Сентябрь':
          $month_number = '09';
          $days = 30;
          break;
        case 'Октябрь':
          $month_number = '10';
          $days = 31;
          break;
        case 'Ноябрь':
          $month_number = '11';
          $days = 30;
          break;
        case 'Декабрь':
          $month_number = '12';
          $days = 31;
          break;											
        }	
    
    return array('month_number' => $month_number, 'days' => $days);
}

function num2str($num)
{
        /* 	$num = str_replace('.', $num, ','); */
    $nul = 'ноль';
    $ten = array(
        array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
    );
    $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
    $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
    $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
    $unit = array( // Units
        array('копейка', 'копейки', 'копеек', 1),
        array('рубль', 'рубля', 'рублей', 0),
        array('тысяча', 'тысячи', 'тысяч', 1),
        array('миллион', 'миллиона', 'миллионов', 0),
        array('миллиард', 'милиарда', 'миллиардов', 0),
    );
            //
    list($rub, $kop) = explode(',', sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub) > 0) {
        foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit) - $uk - 1; // unit key
            $gender = $unit[$uk][3];
            list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                    // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
            else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                    // units without rub & kop
            if ($uk > 1) $out[] = morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
        } //foreach
    } else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
    $out[] = $kop . ' ' . morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
}

function morph($n, $f1, $f2, $f5)
{
    $n = abs(intval($n)) % 100;
    if ($n > 10 && $n < 20) return $f5;
    $n = $n % 10;
    if ($n > 1 && $n < 5) return $f2;
    if ($n == 1) return $f1;
    return $f5;
}

function getMonth($month) {
    $month_string = '';

    switch ($month) {
        case '01':
          $month_string = 'Январь';
          break;
        case '02':
          $month_string = 'Февраль';
          break;
        case '03':
          $month_string = 'Март';
          break;
        case '04':
          $month_string = 'Апрель';
          break;
        case '05':
          $month_string = 'Май';
          break;
        case '06':
          $month_string = 'Июнь';
          break;
        case '07':
          $month_string = 'Июль';
          break;
        case '08':
          $month_string = 'Август';
          break;
        case '09':
          $month_string = 'Сентябрь';
          break;
        case '10':
          $month_string = 'Октябрь';
          break;
        case '11':
          $month_string = 'Ноябрь';
          break;
        case '12':
          $month_string = 'Декабрь';
          break;											
        }	
    
    return $month_string;
}