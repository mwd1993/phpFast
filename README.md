# phpFast
A really light-weight php library that allows you to do basic, tedious things, quicker.<br>


### Instantiate the class
```php
  include('phpFast.php');
  $pf = new phpFast();
```

### isset checking - get/post/put vars

```php
  // this equates to: if(isset($_GET/POST/PUT['some_value']))
  if($pf -> has('some_value')) {
    // can output: post, get or put
    echo $pf -> request_type();
    // set the value to a var
    $value_of = $pf -> get('some_value');
  }
  
  // get the current request type provided by user
  if($pf -> has('some_value','post')) {
    // request_type() gets the info from the server
    echo $pf -> request_type();
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

// file class to a variable..
$file = $pf -> file;

// check if a file exists
// $as_dir=true will check if a directory exists instead
if($file -> exists($path, $as_dir=false)) {
  // read the file to a string
  $read = $file -> read($path);
} else {
  // create the file
  $file -> create($path);
  // write some text to the file
  $file -> write($path,'some text to the file');
}
// Recursively remove a directory
$file -> dir_r($path);

// Create directory
$file -> dir_c('some/path/to/dir/');
```

### Json files

```php
// file class to a variable..
$file = $pf -> file;

// does the file exist?
if($file -> exists($path)) {
  // Read the json file and convert it to a php array
  $json = $file -> json_read($path); 
  foreach($json as $attr => $val){
    // echo each attribute of the array and the value associated
    echo $attr . ' -> ' . $val;
  }
  // write the php array back to the file as json
  $file -> json_write($path, $json);
}
```

### Users

```php
$pf = new phpFast();
$user = $pf -> user;
$user -> get_active_path();
// --> ../Users/

$user -> set_active_path('Data/','Users/');
// --> ../Data/Users/

$user -> exists('Username');
// --> False

$user -> register('Username','password');

// to initially set the value, force edit is set to true
$user -> edit('Username', 'Some Value', 3, $force_edit=true);

// editing the value
$user -> edit('Username','Some Value', 10);
```

### Strings

```php

$string = $pf -> string;

$my_str = 'i like to eat apples and bananas';

echo $string -> replace($my_str,'apples','mangos');
// -- > i like to eat mangos and bananas

echo $string -> slice($my_str,2,-2);
// -- > like to eat mangos and banan

echo $string -> split($my_str,' ');
// -- > splits $my_str by spaces to an array

echo $string -> index($my_str,'eat');
// -- > 10

echo $string -> contains($my_str,'bananas');
// -- > True

echo $string -> between($my_str,'eat','and');
// -- > apples

echo $string -> len($my_str);
// --> 32


```

### Date

```php
$date = $pf -> date;

echo $date -> today;
// -- > 01/11/2021

echo $date -> timezone;
// -- > Pacific Standard Time

echo $date -> name;
// -- > Monday

// formatting optional: h:i:sa (hours, minutes, seconds, am or pm)
echo $date -> time();
// -- > 03:49:18pm

// generates a str with todaysdate_todaystime
// optional arg $reversed=False set to true will
// produce: todaystime_todaysdate
echo $date -> file_string();
// -- > 01112021_035031

```

### Time

```php
$time = $pf -> time;

echo $time -> now_ms();
// -- > 424283832323

echo $time -> now_seconds();
// -- > 424490248

// Save current time in millesconds to the stamp json file by name
$time -> stamp('some_stamp');

// Retrieve stamp value (time in milliseconds)
$time -> stamp_get('some_stamp');

// Get the time elapsed from the current time (in milliseconds) and the time of the stamp
echo $time -> stamp_time_elapsed('some_stamp');
// outputs ie: 2000 (current time) - 1000 (stamp time) = 1000 (time elapsed)
```

### Arrays

```php
$array = $pf -> array;

$my_array = array(1, 2, 3, 4);

// simulates echo var_dump($my_array);
$array -> dump($my_array);

// lets remove the first object in our array
$array -> pop($my_array,0);
// -- > 2, 3, 4

// create an array of 2 items to add to $my_array
$append = array('append_key1' => '5', 'append_key2' => '10');

// lets push the append object to $my_var
$array -> push($my_array, $append);

// dump out the array to be readable
echo $array -> dump($my_array);

// -- > 
// array(4) {
//   [0] => int(2)
//   [1] => int(3)
//   [2] => int(4)
//   [3] => array(2) {
//      ["append_key1"]=>
//      string(1) "5"
//      ["append_key2"]=>
//      string(2) "10"
//    }
//}
```

### Misc

```php

// gets full string query
echo $pf -> request_query();
// outputs 
//    https://www.someurl.com/some_php_file.php?arg1=1&arg2=2

// gets the request type
echo $pf -> request_type();
// outputs: 
//    GET/POST/PUT

```


