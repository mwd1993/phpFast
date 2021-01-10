# phpFast
A really light-weight library that allows you to do basic php things a bit quicker.<br>


### Instantiate the class
```php
  $pf = new phpFast();
```

### isset checking - get/post/put vars

```php
  // will get the current request type provided by php
  if($pf -> has('some_value')) {
    echo $pf -> get_request_type();
    $value_of = $pf -> get('some_value');
  }
  
  // will get the current request type provided by user
  if($pf -> has('some_value','post')) {
    echo $pf -> get_request_type();
    $value_of = $pf -> get('some_value');
  }
 
```

### Cookies

```php
if($pf -> cookie_has('some_cookie')) {
  
} else {
  // sets the cookie with default expiration date in 30 days
  $pf -> cookie_set('some_cookie');
  // set 60 day expiration
  $expiration = time() + (86400 * 60),"/";
  $pf -> cookie_set('some_cookie',$expiration);
}
```

### files

```php
if($pf -> $file -> exists($path, $as_dir=false)) {
  $read = $pf -> $file -> file_read($path);
} else {
  $pf -> $file -> file_create($path);
  $pf -> $file -> file_write('some text to the file');
}
// Recursively remove a directory
$pf -> $file -> dir_r($path);
```

### Json files

```python
if($pf -> $file -> exists($path)) {
  // string to json
  $json = $pf -> $file -> json_read($path);
  foreach($json as $attr => $val){
    echo $attr . ' -> ' . $val;
  }
  // json back to string
  $pf -> $file -> json_write($path);
}
```
### Misc

```python

echo $pf -> get_query();
// outputs https://www.someurl.com/some_php_file.php?arg1=1&arg2=2

echo $pf -> get_request_type();
// outputs: GET/POST/PUT

```


