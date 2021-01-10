<?php
class phpFastFile {

    function file_create($path) {
        if($this -> exists($path) == false) {
            if($this -> exists($path,$as_dir=true) == false) {
                $this->dir_c($path);
            }
            f = open($path,'w');
            fwrite($path,'');
            fclose($path);
            return true;
        }
        return false;
    }

    function file_read($path) {
        return file_get_contents($path);
    }

    function exists($path, $as_dir=False) {
        if($as_dir) {
            return file_exists($path);
        }
        return is_file($path);
    }

    function file_write($path,$contents) {
        return file_put_contents($path,$contents);
    }

    function json_write($path,$json) {
        if($this -> exists($path)) {
           $json = json_encode($data);
           return $json;
        }
    }

    function json_read($path) {
        if($this -> exists($path)) {
           $data = $this -> file_read($path);
           $json = json_decode($data,true);
           return $json;
        }
    }

    function dir_r($dir){
        if($this->$file->exists($dir)) {
            return false;
        }
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                        $this->dir_r($dir. DIRECTORY_SEPARATOR .$object);
                    else
                        unlink($dir. DIRECTORY_SEPARATOR .$object);
                }
            }
            rmdir($dir);
        }
    }

    function dir_c($dir) {
        return mkdir($dir);
    }
}

class phpFast {
    public $file;
    public $logging_enabled = false;

    function __construct() {
        $this -> file = new phpFastFile();
    }

    function log($str) {
        if($this -> $logging_enabled) {
            echo $str;
        }
    }

    function get_query() {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $url;
    }

    function get_request_type() {
        return $_SERVER['REQUEST_METHOD'];
    }

    function has($str, $type=-1) {
        // add support for put requests

        if($type == -1) {
            $type = $this -> get_request_type();
        }
        if($type == "post") {
            return isset($_POST[$type]);
        } else if($type == "get") {
            return isset($_POST[$type]);
        }
    }

    function get($str,$type=-1){
        // add support for put requests

        if($type == -1) {
            $type = $this->get_request_type();
        }

        if($type == 'get') {
            return $_GET[$str];
        } else if($type == 'post') {
            return $_POST[$str];
        }
    }

    function cookie_has($cookie_name) {
        return isset($_COOKIE[$cookie_name]);
    }

    function cookie_get($cookie_name) {
        if($this->cookie_has($cookie_name)) {
            return $_COOKIE[$cookie_name];
        }
        return false;
    }

    function cookie_set($cookie_name,$expiration=30) {
        $expiration = time() + (86400 * $expiration),"/");
        setcookie($cookie_name, 'True', $expiration);
    }

    function generateRandomString($length=10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
?>