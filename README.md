# phpFast
A really light-weight php library that allows you to do basic, tedious things, quicker.<br>


### Instantiate the class
```php
  include('phpFast.py');
  $pf = new phpFast();
```

### isset checking - get/post/put vars

```php
  // this equates to: if(isset($_GET/POST/PUT['some_value']))
  if($pf -> has('some_value')) {
    // can output: post, get or put
    echo $pf -> get_request_type();
    // set the value to a var
    $value_of = $pf -> get('some_value');
  }
  
  // get the current request type provided by user
  if($pf -> has('some_value','post')) {
    // get_request_type() gets the info from the server
    echo $pf -> get_request_type();
    // user provided value may differ from the servers above
    $value_of = $pf -> get('some_value','post');
  }
 
```

### Cookies

```php
// do we have 'some_cookie' cookie set?
if($pf -> cookie_has('some_cookie')) {
   echo 'cookie - > ' . $pf - > cookie_get('some_cookie');
} else {
  // sets the cookie with default expiration date to 30 days
  $pf -> cookie_set('some_cookie');
  // set a 60 day expiration instead
  $expiration = 60;
  $pf -> cookie_set('some_cookie',$expiration);
}
```

### Files

```php
// check if a file exists
// $as_dir=true will check if a directory exists instead
if($pf -> $file -> exists($path, $as_dir=false)) {
  // read the file to a string
  $read = $pf -> $file -> file_read($path);
} else {
  // create the file
  $pf -> $file -> file_create($path);
  // write some text to the file
  $pf -> $file -> file_write($path,'some text to the file');
}
// Recursively remove a directory
$pf -> $file -> dir_r($path);
```

### Json files

```python
// does the file exist?
if($pf -> $file -> exists($path)) {
  // Read the json file and convert it to a php array
  $json = $pf -> $file -> json_read($path); 
  foreach($json as $attr => $val){
    // echo each attribute of the array and the value associated
    echo $attr . ' -> ' . $val;
  }
  // write the php array back to the file as json
  $pf -> $file -> json_write($path, $json);
}
```
### Misc

```python

// gets full string query
echo $pf -> get_query();
// outputs 
//    https://www.someurl.com/some_php_file.php?arg1=1&arg2=2

// gets the request type
echo $pf -> get_request_type();
// outputs: 
//    GET/POST/PUT

```


