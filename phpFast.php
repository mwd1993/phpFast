<?php
class phpFastFile {
    function create($path) {
        if ($this->exists($path, $as_dir = true) == false) {
            $this->dir_c(str_replace($this->name($path), '', $path));
        }
        if ($this->exists($path) == false) {
            $f = fopen($path, 'w');
            fwrite($f, '');
            fclose($f);
            return true;
        }
        return false;
    }
    function delete($path) {
        if ($this->exists($path)) {
            return unlink($path);
        }
        return false;
    }
    function copy_to($path, $path_to, $delete_original = false) {
        if ($this->exists($path)) {
            $read = $this->read($path);
            $this->create($path_to);
            $this->write($path_to, $read);
            if ($delete_original) $this->delete($path);
            return true;
        }
        return false;
    }
    function rename($path, $rename) {
        return $this->copy_to($path, $rename);
    }
    function name($path) {
        return basename($path);
    }
    function path($path) {
        return str_replace($this->name($path), '', $path);
    }
    function extension($path) {
        $i = strrpos($path, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($path) - $i;
        $ext = substr($path, $i, $l);
        return $ext;
    }
    function read($path) {
        return file_get_contents($path);
    }
    function exists($path, $as_dir = False) {
        if ($as_dir) {
            return file_exists($path);
        }
        return is_file($path);
    }
    function write($path, $contents) {
        return file_put_contents($path, $contents);
    }
    function json_write($path, $json) {
        if ($this->exists($path)) {
            $json = json_encode($json);
            file_put_contents($path, $json);
            return $json;
        }
    }
    function json_read($path) {
        if ($this->exists($path)) {
            $data = $this->read($path);
            $json = json_decode($data, true);
            return $json;
        }
    }
    function dir_r($dir) {
        if ($this->exists($dir)) {
            return false;
        }
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) $this->dir_r($dir . DIRECTORY_SEPARATOR . $object);
                    else unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            rmdir($dir);
        }
    }
    function dir_c($dir) {
        return mkdir($dir);
    }
}
class phpFastString {
    function replace($str, $repl, $with) {
        // lol at this default syntax php thought was ideal
        return str_replace($repl, $with, $str);
    }
    function split($str, $by) {
        // once again..
        return explode($by, $str);
    }
    function len($str) {
        return strlen($str);
    }
    function slice($str, $start, $end = "unset") {
        if ($end == "unset") {
            $end = $this->len($str);
        }
        return substr($str, $start, $end);
    }
    function index($str, $sub_str) {
        return strpos($str, $sub_str);
    }
    function contains($str, $contains, $case_sensitive = false) {
        return str_contains(strtolower($str), strtolower($contains));
    }
    function between($str, $str1, $str2) {
        $string = ' ' . $str;
        $ini = $this->index($string, $str1);
        if ($ini == 0) return '';
        $ini+= $this->len($str1);
        $len = $this->index($string, $str2, $ini) - $ini;
        return $this->slice($string, $ini, $len);
    }
}
class phpFastArray {
    function dump($array) {
        echo var_dump($array);
    }
    function push(&$array, $obj) {
        return array_push($array, $obj);
    }
    function pop(&$array, $index, $extend = 0) {
        if ($extend == 0) $extend = 1;
        $arr = array_splice($array, $index, $extend);
        return $array;
    }
}
class phpFastDate {
    public $today;
    public $timezone;
    private $string;
    public $name;
    function __construct() {
        $this->today = date("m/d/Y");
        $this->timezone = date_default_timezone_get();
        $this->name = date('l', strtotime(date("m/d/Y")));
        $this->string = new phpFastString();
    }
    function name() {
        return date('l', strtotime($this->today));
    }
    function today() {
        return date("m/d/Y");
    }
    function time($format = false) {
        if ($format == false) $format = "h:i:sa";
        return date($format);
    }
    function file_string($reversed = False) {
        if ($reversed) {
            $fs = $this->time('h:i:s') . '_' . $this->today;
        } else {
            $fs = $this->today . '_' . $this->time('h:i:s');
        }
        $fs = $this->string->replace($fs, ':', '');
        $fs = $this->string->replace($fs, '/', '');
        return $fs;
    }
}
class phpFast {
    public $file;
    public $string;
    public $array;
    public $date;
    public $logging_enabled = false;
    public $session_started = false;
    function __construct() {
        $this->file = new phpFastFile();
        $this->string = new phpFastString();
        $this->array = new phpFastArray();
        $this->date = new phpFastDate();
    }
    function log($str) {
        if ($this->$logging_enabled) {
            echo $str;
        }
    }
    function request_query($full_query = true) {
        if ($full_query) {
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            return $url;
        }
    }
    function request_type() {
        return $_SERVER['REQUEST_METHOD'];
    }
    function has($key, $type = - 1) {
        // add support for put requests
        if ($type == - 1) {
            $type = strtolower($this->request_type());
        }
        if ($type == "post") {
            return isset($_POST[$key]);
        } else if ($type == "get") {
            return isset($_GET[$key]);
        }
    }
    function get($key, $type = - 1) {
        // add support for put requests
        if ($type == - 1) {
            $type = strtolower($this->request_type());
        }
        if ($type == 'get') {
            return $_GET[$key];
        } else if ($type == 'post') {
            return $_POST[$key];
        }
    }
    function cookie_has($cookie_name) {
        return isset($_COOKIE[$cookie_name]);
    }
    function cookie_get($cookie_name) {
        if ($this->cookie_has($cookie_name)) {
            return $_COOKIE[$cookie_name];
        }
        return false;
    }
    function cookie_set($cookie_name, $expiration = 30) {
        $expiration = time() + (86400 * $expiration);
        setcookie($cookie_name, 'True', $expiration, "/");
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0;$i < $length;$i++) {
            $randomString.= $characters[rand(0, $charactersLength - 1) ];
        }
        return $randomString;
    }
    function session_start() {
        session_start();
        $_SESSION['started'] = true;
    }
    function session_set($key, $val) {
        if ($_SESSION['started']) {
            $_SESSION[$key] = $val;
        } else {
            // session not started..
            
        }
    }
    function session_get($key) {
        if ($_SESSION['started']) {
            if (isset($_SESSION[$key]) == false) {
                // no key found
                return false;
            }
            return $_SESSION[$key];
        }
    }
    function session_clear($key) {
        return session_unset($key);
    }
    function session_end() {
        return session_unset();
    }
}
?>