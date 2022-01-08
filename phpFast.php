<?php
/**
 * File Class that handles everything to do with working with files.
 * @author Marc
 *
 */
class phpFastFile {
	private $farray;
	function __construct() {
		// phpFastArray
		$this -> farray = new phpFastArray();
		
	}
	/**
	 * Converts a directory to an array of files, wildcard acceptable.
	 *
	 * @param string $directory
	 * @param string $wild_card
	 * @return array
	 */
	function dir_to_array($directory, $wild_card = '*') {
		$dir = new DirectoryIterator( dirname( $directory ) );
		$array = array_diff(scandir($directory), array('.', '..'));
		$array_final = array();
		foreach($array as $aitem) {
		    $this -> farray -> push($array_final, $aitem);
		}
		return $array_final;
	}
	
	function get_dir_creation_time($dir, $relative_path = true) {
	    clearstatcache();
	    if($relative_path)
	        return stat($dir)['ctime'];
	   else
	        return stat(getcwd() . $dir)['ctime'];
	}
	
	function get_all_folders($directory = '.') {
	    $d = dir($directory);
	    $array = array();
        while(false !== ($entry = $d -> read())) {
            if($this -> extension($entry) == '' && ($entry != '.') && ($entry != '..')) {
                $this -> farray -> push($array, $entry);
            }
        }
        $d->close();
        return $array;
	}
	
	/**
	 * Attempts to create a file in the specified path.
	 *
	 * @param string $path
	 * @return boolean
	 */
	function create($path) {
		if ( $this -> exists( $path, $as_dir = true ) == false ) {
			$this -> dir_c( str_replace( $this -> name( $path ), '', $path ) );
		}
		if ( $this -> exists( $path ) == false ) {
			$f = fopen( $path, 'w' );
			fwrite( $f, '' );
			fclose( $f );
			return true;
		}
		return false;
	}
	/**
	 * Attempts to delete a file in the specified path.
	 *
	 * @param string $path
	 * @return boolean
	 */
	function delete($path) {
		if ( $this -> exists( $path ) ) {
			return unlink( $path );
		}
		return false;
	}
	/**
	 * Attempts to copy a file to the specified path, delete_original=True to delete the original file.
	 *
	 * @param string $path
	 * @param string $path_to
	 * @param boolean $delete_original
	 * @return boolean
	 */
	function copy_to($path, $path_to, $delete_original = false) {
		if ( $this -> exists( $path ) ) {
			$read = $this -> read( $path );
			$this -> create( $path_to );
			$this -> write( $path_to, $read );
			if ( $delete_original )
				$this -> delete( $path );
			return true;
		}
		return false;
	}
	/**
	 * 'Attempts to rename a file specified by path.
	 *
	 * @param string $path
	 * @param string $rename
	 * @return boolean
	 */
	function rename($path, $rename) {
		return $this -> copy_to( $path, $rename , true);
	}
	/**
	 * Attempts to rename a file specified by path
	 *
	 * @param string $path
	 * @return string
	 */
	function name($path) {
		return basename( $path );
	}
	/**
	 * Attempts to get the path name from a full path
	 *
	 * @param string $path
	 * @return mixed
	 */
	function path($path) {
		return str_replace( $this -> name( $path ), '', $path );
	}
	/**
	 * Gets the extension of a file.
	 *
	 * @param string $path
	 * @return string
	 */
	function extension($path) {
		$i = strrpos( $path, "." );
		if ( ! $i ) {
			return "";
		}
		$l = strlen( $path ) - $i;
		$ext = substr( $path, $i, $l );
		return $ext;
	}
	/**
	 * Reads the file out to a string.
	 *
	 * @param string $path
	 * @return string
	 */
	function read($path, $relative_dir = true) {
		//return file_get_contents( $path , $relative_dir );
		$fail = false;
		$myfile = fopen($path, "r") or $fail = true;
		$string = "";
        // Output one line until end-of-file
        if(!$fail) {
        	$index = 0;
            while(!feof($myfile)) {
                $index++;
                if($index == 1)
                    $string = fgets($myfile);
                else
                {
                    if($index == 2)
                        $string = $string . '\n';
                    $string = $string . fgets($myfile) . '\n';
                    //echo fgets($myfile) . "<br>";
                }
            }
            fclose($myfile);
        }
        return $string;
	}
	/**
	 * Checks to see if a file exists, $as_dir=True to check a directory instead.
	 *
	 * @param string $path
	 * @param boolean $as_dir
	 * @return boolean
	 */
	function exists($path, $as_dir = False) {
		if ( $as_dir ) {
			return file_exists( $path );
		}
		return is_file( $path );
	}
	/**
	 * Writes contents to the file specified.
	 *
	 * @param string $path
	 * @param string $contents
	 * @return number
	 */
	function write($path, $contents) {
		return file_put_contents( $path, $contents );
	}
	/**
	 * Attempts to write a json object to a file, this method handles encoding.
	 *
	 * @param string $path
	 * @param array $json
	 * @return string
	 */
	function json_write($path, $json) {
		if ( $this -> exists( $path ) ) {
			$json = json_encode( $json );
			file_put_contents( $path, $json );
			return $json;
		}
	}
	/**
	 * Attempts to read a file containing a json, to a php array to loop/work with.
	 *
	 * @param string $path
	 * @return mixed|boolean
	 */
	function json_read($path) {
		if ( $this -> exists( $path ) ) {
			$data = $this -> read( $path );
			$json = json_decode( $data, true );
			return $json;
		}
		return false;
	}
	/**
	 * Recursively remove a directory.
	 *
	 * @param string $dir
	 * @return boolean
	 */
	function dir_r($dir) {
		if ( $this -> exists( $dir ) ) {
			return false;
		}
		if ( is_dir( $dir ) ) {
			$objects = scandir( $dir );
			foreach ( $objects as $object ) {
				if ( $object != "." && $object != ".." ) {
					if ( is_dir( $dir . DIRECTORY_SEPARATOR . $object ) && ! is_link( $dir . "/" . $object ) )
						$this -> dir_r( $dir . DIRECTORY_SEPARATOR . $object );
					else
						unlink( $dir . DIRECTORY_SEPARATOR . $object );
				}
			}
			rmdir( $dir );
		}
	}
	/**
	 * Create a directory at $dir.
	 *
	 * @param string $dir
	 * @return boolean
	 */
	function dir_c($dir) {
		return mkdir( $dir );
	}
}
/**
 * String Class, handles everything to do with working with strings.
 *
 * @author Marc
 *        
 */
class phpFastString {
	/**
	 * Replaces a word in a string with another word.
	 *
	 * @param string $str
	 * @param string $repl
	 * @param string $with
	 * @return mixed
	 */
	function replace($str, $repl, $with) {
		// lol at this default syntax php thought was ideal
		return str_replace( $repl, $with, $str );
	}
	/**
	 * Splits the string into an array, seperated by $by.
	 *
	 * @param string $str
	 * @param string $by
	 * @return array
	 */
	function split($str, $by) {
		// once again..
		return explode( $by, $str );
	}
	/**
	 * Returns the length of the string.
	 *
	 * @param string $str
	 * @return number
	 */
	function len($str) {
		return strlen( $str );
	}
	/**
	 * Slices the string from left to right (optional).
	 *
	 * @param string $str
	 * @param string $start
	 * @param string $end
	 * @return string
	 */
	function slice($str, $start, $end = "unset") {
		if ( $end == "unset" ) {
			$end = $this -> len( $str );
		}
		return substr( $str, $start, $end );
	}
	/**
	 * Returns the index of the string found, if any.
	 *
	 * @param string $str
	 * @param string $sub_str
	 * @return number
	 */
	function index($str, $sub_str) {
		return strpos( $str, $sub_str );
	}
	/**
	 * Lowercase a string.
	 * Can provide additional arguments to customize the first letter and the rest of the letters.
	 *
	 * @param string $str
	 * @param boolean $just_first
	 * @param boolean $just_rest
	 * @return string
	 */
	function lower($str, $just_first = false, $just_rest = false) {
		if ( $just_first == false && $just_rest == false ) {
			return strtolower( $str );
		} else {
			if ( $just_first == true ) {
				$str = $this -> slice( strtolower( $str ), 0, 1 ) . $this -> slice( $str, 1 );
			}

			if ( $just_rest == true ) {
				$str = $this -> slice( $str, 0, 1 ) . $this -> slice( strtolower( $str ), 1 );
			}
		}
		return $str;
	}
	/**
	 * Uppercase a string.
	 * Can provide additional arguments to customize the first letter and the rest of the letters.
	 *
	 * @param string $str
	 * @param boolean $just_first
	 * @param boolean $just_rest
	 * @return string
	 */
	function upper($str, $just_first = false, $just_rest = false) {
		if ( $just_first == false && $just_rest == false ) {
			return strtoupper( $str );
		} else {
			if ( $just_first ) {
				$str = $this -> slice( strtoupper( $str ), 0, 1 ) . $this -> slice( $str, 1 );
				// return $this -> slice(strtolower($str), 0, 1) . $this ->slice($str,1);
			}

			if ( $just_rest ) {
				$str = $this -> slice( $str, 0, 1 ) . $this -> slice( strtoupper( $str ), 1 );
				// return $this->slice($str, 0, 1) . $this -> slice(strtolower($str, $start));
			}
		}
		return $str;
	}
	/**
	 * Check if a string contains a string within it.
	 *
	 * @param string $str
	 * @param string $contains
	 * @param boolean $case_sensitive
	 * @return boolean
	 */
	function contains($str, $contains, $case_sensitive = false) {
		if ( $case_sensitive ) {
			//return str_contains( $str, $contains );
            if(strpos($str, $contains) !== false)
                return true;
            return false;
		} else {
			if(strpos( strtolower( $str ), strtolower( $contains ) ) !== false)
                return true;
            return false;
		}
	}
	/**
	 * Extracts a string between two strings.
	 *
	 * @param string $str
	 * @param string $str1
	 * @param string $str2
	 * @return string
	 */
	function between($str, $str1, $str2) {
		$string = ' ' . $str;
		$ini = $this -> index( $string, $str1 );
		if ( $ini == 0 )
			return '';
		$ini += $this -> len( $str1 );
		$len = $this -> index( $string, $str2, $ini ) - $ini;
		return $this -> slice( $string, $ini, $len );
	}
}
/**
 * Array Class that handles everything to do with arrays.
 *
 * @author Marc
 *        
 */
class phpFastArray {
	/**
	 * Returns a new, empty array
	 *
	 * @return array
	 */
	function new() {
		return array ();
	}
	/**
	 * Dumps the array out to view, echos var_dump.
	 *
	 * @param array $array
	 */
	function dump($array) {
		echo var_dump( $array );
	}
	/**
	 * Pushes the object into the array, uses reference to the array.
	 *
	 * @param array $array
	 * @param
	 *        	$obj
	 * @return number
	 */
	function push(&$array, $obj) {
		return array_push( $array, $obj );
	}
	/**
	 * Pop an object out of the array, uses reference to the array.
	 *
	 * @param array $array
	 * @param int $index
	 * @param number $extend
	 * @return $array
	 */
	function pop(&$array, $index, $extend = 0) {
		if ( $extend == 0 )
			$extend = 1;
		$arr = array_splice( $array, $index, $extend );
		return $array;
	}
	function indexOf($array, $array_item) {
		return array_search( $array_item, $array );
	}
	
	function contains($array, $item, $case_sens = true) {
	    if(!$case_sens)
	    {
	        $array_lower = array_map('strtolower', $array);
	        return in_array(strtolower($item), $array_lower);
	    }
	    else
	        return in_array($item, $array);
	}
}
/**
 * Date Class that handles everything to do involded with working with dates.
 *
 * @author Marc
 *        
 */
class phpFastDate {
	public $today;
	public $timezone;
	private $string;
	public $name;
	function __construct() {
		$this -> today = date( "m/d/Y" );
		$this -> timezone = date_default_timezone_get();
		$this -> name = date( 'l', strtotime( date( "m/d/Y" ) ) );
		$this -> string = new phpFastString();
	}
	/**
	 * Returns the name of the day, ie: monday, tuesday, etc.
	 *
	 * @return string
	 */
	function name() {
		return date( 'l', strtotime( $this -> today ) );
	}
	/**
	 * Returns the date.
	 * Default format: month/day/year. Use $year_first=True for year/month/day.
	 *
	 * @param boolean $year_first
	 * @return string
	 */
	function today($year_first = False) {
		if ( $year_first )
			return date( 'Y/m/d' );
		return date( "m/d/Y" );
	}
	/**
	 * Returns current time, custom format allowed.
	 * Default: h:i:sa.
	 *
	 * @param boolean $format
	 * @return string
	 */
	function time($format = false) {
		if ( $format == false )
			$format = "h:i:sa";
		return date( $format );
	}
	/**
	 * Returns a string useful for naming files.
	 *
	 * @param boolean $reversed
	 * @param boolean $year_first
	 * @return mixed
	 */
	function file_string($reversed = False, $year_first = False) {
		if ( $reversed ) {
			if ( $year_first ) {
				$fs = $this -> time( 'h:i:s' ) . '_' . $this -> today( $year_first = True );
			} else {
				$fs = $this -> time( 'h:i:s' ) . '_' . $this -> today;
			}
		} else {
			if ( $year_first ) {
				$fs = $this -> today( $year_first ) . '_' . $this -> time( 'h:i:s' );
			} else {
				$fs = $this -> today . '_' . $this -> time( 'h:i:s' );
			}
		}
		$fs = $this -> string -> replace( $fs, ':', '' );
		$fs = $this -> string -> replace( $fs, '/', '' );
		return $fs;
	}
}
/**
 * Time class, though you can get the time from the date class, this has other general useful time methods.
 *
 * @author Marc
 *        
 */
class phpFastTime {
	private $ffile;
	private $farray;
	public $stamp_path = 'time_stamp/stamps.json';
	function __construct() {
		$this -> ffile = new phpFastFile();
		$this -> farray = new phpFastArray();
	}
	/**
	 * Returns current time in milliseconds
	 *
	 * @return number
	 */
	function now_ms() {
		$mt = explode( ' ', microtime() );
		return ( ( int ) $mt[1] ) * 1000 + ( ( int ) round( $mt[0] * 1000 ) );
	}
	/**
	 * Returns current time in seconds.
	 *
	 * @return number
	 */
	function now_seconds() {
		$mt = explode( ' ', microtime() );
		return ( ( int ) $mt[1] ) + ( ( int ) round( $mt[0] ) );
	}
	/**
	 * Stamps the current time, to a json file via $name
	 *
	 * @param string $name
	 */
	function stamp($name) {
		$ff = $this -> ffile;
		$time_ms = $this -> now_ms();
		$path = $this -> stamp_path;
		if ( $ff -> exists( $path ) == False ) {
			$ff -> create( $path );
			$ff -> write( $path, '[]' );
		} else {
		}
		$json = $ff -> json_read( $path );
		$detected = False;
		foreach ( $json as $attr => $val ) {
			if ( $val['name'] == $name ) {
				$json[$attr]['stamp'] = $time_ms;
				$detected = True;
				break;
			}
		}

		if ( ! $detected ) {
			$stamp_array = array (
					'name' => $name,
					'stamp' => $time_ms
			);
			$this -> farray -> push( $json, $stamp_array );
		}

		$ff -> json_write( $path, $json );
	}
	/**
	 * Gets the current time in ms, then gets the time from the stamp json file via $name<br>
	 * calculates the difference in time, and returns that difference
	 *
	 * @param string $name
	 * @return boolean|number
	 */
	function stamp_time_elapsed($name) {
		$stamp = $this -> stamp_get( $name );
		if ( $stamp == false ) {
			return false;
		}
		$elapsed = ( int ) $this -> now_ms() - ( int ) $stamp;
		return $elapsed;
	}
	/**
	 * Gets a stamp's time value from the json file
	 *
	 * @param string $name
	 * @return boolean
	 */
	function stamp_get($name) {
		$ff = $this -> ffile;
		$path = $this -> stamp_path;
		if ( $ff -> exists( $path ) ) {
			$json = $ff -> json_read( $path );

			foreach ( $json as $attr => $val ) {
				if ( $val['name'] == $name ) {
					return $json[$attr]['stamp'];
				}
			}
		}
		return false;
	}
	/**
	 * Clears the stamp file totally, or if a name is specified, clears the stamp by name.
	 *
	 * @param string $name_or_all
	 * @return boolean
	 */
	function stamp_clear($name_or_all = "all") {
		$ff = $this -> ffile;
		$arr = $this -> farray;
		$path = $this -> stamp_path;
		if ( $name_or_all == "all" ) {
			$ff -> write( $path, '[]' );
			return true;
		} else {
			$json = $ff -> json_read( $path );
			$index = - 1;
			$detected = False;
			foreach ( $json as $attr => $val ) {
				$index ++;
				if ( $val['name'] == $name_or_all ) {
					$arr -> pop( $json, $index );
					$detected = True;
					break;
				}
			}
			if ( $detected ) {
				$ff -> json_write( $path, $json );
				return true;
			}
		}
		return false;
	}
}
/**
 * User class, allows for simple user/profile data management.
 *
 * @author Marc
 *        
 */
class phpFastUser {
	private $ffile;
	private $farray;
	private $path_main = 'Users/';
	private $path_subdir = '';
	public $password_hash_cost = 12;
	function __construct() {
		$this -> ffile = new phpFastFile();
		$this -> farray = new phpFastArray();
	}
	/**
	 * Check the password against the hashed value stored in the user directory
	 *
	 * @param string $user
	 * @param string $pass_hash
	 * @return boolean
	 */
	private function check_pass($user, $pass) {
		if ( $this -> exists( $user ) ) {
			$json = $this -> ffile -> json_read( $this -> get_active_path() . $user . '.json' );
			foreach ( $json as $attr => $val ) {
				if ( password_verify( $pass, $val['pass_hash'] ) ) {
					return true;
				}
			}
		}
		return false;
	}
	function get_active_path() {
		return $this -> path_main . $this -> path_subdir;
	}
	/**
	 * Sets the active path to use when reading/writing/updating user values.
	 *
	 * @param string $path_main
	 * @param string $path_subdir
	 */
	function set_active_path($path_main = 'Users/', $path_subdir = '') {
		$this -> path_main = $path_main;
		$this -> path_subdir = $path_subdir;
	}
	/**
	 * Registers/Creates a user to the active directory.
	 *
	 * @param string $user
	 * @param string $pass
	 * @return boolean
	 */
	function register($user, $pass) {
		$ff = $this -> ffile;
		$fa = $this -> farray;
		if ( $this -> exists( $user ) ) {
			return false;
		}

		$pass_safe = password_hash( $pass, PASSWORD_DEFAULT, [ 
				'cost' => $this -> password_hash_cost
		] );

		$ff -> create( $this -> get_active_path() . $user . '.json' );
		$ff -> write( $this -> get_active_path() . $user . '.json', '{}' );
		$json = $ff -> json_read( $this -> get_active_path() . $user . '.json' );
		$hash = array (
				'pass_hash' => $pass_safe
		);
		$fa -> push( $json, $hash );
		$ff -> json_write( $this -> get_active_path() . $user . '.json', $json );

		return true;
		// append to database
	}
	/**
	 * Will set the user as logged in..
	 *
	 * @param string $user
	 * @param string $pass
	 * @return boolean
	 */
	function login($user, $pass) {
		if ( $this -> exists( $user ) == false ) {
			return false;
		}

		if ( $this -> check_pass( $user, $pass ) ) {
			// user log in
			return true;
		} else {
			// user failed pass
			return false;
		}
	}
	function logout($user) {
	}
	function is_logged_in($user) {
	}
	function exists($user) {
		return $this -> ffile -> exists( $this -> get_active_path() . $user . '.json', $as_dir = True );
	}
	/**
	 * Will delete the user, and the user directory.
	 *
	 * @param string $user
	 * @return boolean
	 */
	function delete($user) {
		if ( $this -> exists( $user ) ) {
			$this -> ffile -> delete( $this -> get_active_path() . $user . '.json' );
			$this -> ffile -> dir_r( $this -> ffile -> path( $this -> get_active_path() . $user . '.json' ) );
			return true;
		}
		return false;
	}
	/**
	 * Gets the value stored to a user, by the key
	 *
	 * @param string $user
	 * @param string $key_value
	 * @return array|boolean
	 */
	function get_value($user, $key_value) {
		if ( $this -> exists( $user ) ) {
			$json = $this -> ffile -> json_read( $this -> get_active_path() . $user . '.json' );
			foreach ( $json as $attr => $val ) {
				if ( isset( $val[$key_value] ) ) {
					return $val;
				}
			}
		}
		return false;
	}
	/**
	 * Attempts to edit a users data, by key value, with the new value provided.
	 * $force_edit=true will write that value if it doesn't exist
	 *
	 * @param string $user
	 * @param string $key
	 * @param string|array|int|float $new_value
	 * @param boolean $force_edit
	 * @return boolean
	 */
	function edit($user, $key, $new_value, $force_edit = False) {
		if ( $this -> exists( $user ) ) {
			$detected = false;
			$json = $this -> ffile -> json_read( $this -> get_active_path() . $user . '.json' );
			foreach ( $json as $attr => $val ) {

				if ( isset( $val[$key] ) ) {
					$detected = true;
					break;
				}
			}
		}
		if ( $detected ) {
			$json[$attr] = array (
					$key => $new_value
			);
			$this -> ffile -> json_write( $this -> get_active_path() . $user . '.json', $json );
			return true;
		} else if ( $force_edit && $json != false ) {
			$obj = array (
					$key => $new_value
			);
			$this -> farray -> push( $json, $obj );
			$this -> ffile -> json_write( $this -> get_active_path() . $user . '.json', $json );
			return true;
		}
		return false;
	}
	/**
	 * Attempts to remove a key value from the users data (json)
	 *
	 * @param string $user
	 * @param string $key
	 * @return boolean
	 */
	function edit_remove($user, $key) {
		if ( $this -> exists( $user ) ) {
			$json = $this -> ffile -> json_read( $this -> get_active_path() . $user . '.json' );
			foreach ( $json as $attr => $val ) {

				if ( isset( $val[$key] ) ) {
					$this -> farray -> pop( $json, $this -> farray -> indexOf( $json, $val ) );
					$this -> ffile -> json_write( $this -> get_active_path() . $user . '.json', $json );
					return true;
				}
			}
		}
		return false;
	}
}

class phpMySQL {
    function __construct() {

    }

    function connect($ip, $user, $pass)
    {
        $link = mysqli_connect($ip, $user, $pass);
        if (!$link) {
            echo("PhpFast -> Mysql connection error.");
            return false;
        }
        return $link;
    }
    /**
     * "SELECT * FROM " . $select_from . " WHERE ". $select_where ."='".$value."'"
     */
    function row_exists($mysql_link, $select_from, $select_where, $value)
    {
        $query = mysqli_query($mysql_link, "SELECT * FROM " . $select_from . " WHERE ". $select_where ."='".$value."'");

        if (!$query)
        {
            die('Error: ' . mysqli_error($con));
        }

        if(mysqli_num_rows($query) > 0){
            return true;
        }
        else
        {
            return false;
        }
    }

    function query($mysql_link, $sql_string)
    {
        return $mysql_link -> query($sql_string);
    }


    function close($mysql_link)
    {
        mysqli_close($mysql_link);
    }
}

/**
 * Main class, has general methods and helper methods users will generally be using instead of default library.
 *
 * @author Marc
 *        
 */
class phpFast {
	public $file;
	public $string;
	public $array;
	public $date;
	public $time;
	public $user;
    public $mysql;
	public $logging_enabled = false;
	public $session_started = false;
	function __construct() {
		$this -> file = new phpFastFile();
		$this -> string = new phpFastString();
		$this -> array = new phpFastArray();
		$this -> date = new phpFastDate();
		$this -> time = new phpFastTime();
		$this -> user = new phpFastUser();
        $this -> mysql = new phpMySQL();
	}
	/**
	 * Echos a string.
	 *
	 * @param string $str
	 */
	function log($str) {
		if ( $this -> logging_enabled ) {
			echo $str;
		}
	}
	/**
	 * Turns on detailed error output for debugging purposes.
	 */
	function show_errors() {
		ini_set( 'display_errors', 1 );
		ini_set( 'display_startup_errors', 1 );
		error_reporting( E_ALL );
	}
	/**
	 * Gets an array of all the files attempted to be uploaded, if any.
	 *
	 * @return boolean|array
	 */
	function uploaded_files() {
		$arr = array ();
		foreach ( $_FILES as $name => $file ) {
			$this -> array -> push( $arr, array (
					$name,
					$file
			) );
		}
		if ( $this -> array -> len( $arr ) == 0 )
			return false;
		else
			return $arr;
	}
	/**
	 * returns query string ie: <br>
	 * http://example.com/example.php?example=true&example2=true
	 *
	 * @param boolean $full_query=true
	 * @return string
	 */
	function request_query($full_query = true) {
		if ( $full_query ) {
			$url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			$url = $url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http' ) . '://' . $_SERVER['REQUEST_URI'];
		}

		return $url;
	}
	/**
	 * Gets the current request type from the user.
	 *
	 * @return string
	 */
	function request_type() {
		return $_SERVER['REQUEST_METHOD'];
	}
	/**
	 * Checks post/get/put to see if a value has been set.
	 *
	 * @param string $key
	 * @param string $type
	 * @return string
	 */
	function has($key, $type = - 1) {
		// add support for put requests
		if ( $type == - 1 ) {
			$type = strtolower( $this -> request_type() );
		}
		if ( $type == "post" ) {
			return isset( $_POST[$key] );
		} else if ( $type == "get" ) {
			return isset( $_GET[$key] );
		}
	}
	/**
	 * Returns a value from post/get/put.
	 *
	 * @param string $key
	 * @param string $type
	 * @return string
	 */
	function get($key, $type = - 1) {
		// add support for put requests
		if ( $type == - 1 ) {
			$type = strtolower( $this -> request_type() );
		}
		if ( $type == 'get' ) {
			return $_GET[$key];
		} else if ( $type == 'post' ) {
			return $_POST[$key];
		}
	}
	/**
	 * Checks if a cookie exists.
	 *
	 * @param string $cookie_name
	 * @return boolean
	 */
	function cookie_has($cookie_name) {
		return isset( $_COOKIE[$cookie_name] );
	}
	/**
	 * Get the value of the cookie.
	 *
	 * @param string $cookie_name
	 * @return boolean
	 */
	function cookie_get($cookie_name) {
		if ( $this -> cookie_has( $cookie_name ) ) {
			return $_COOKIE[$cookie_name];
		}
		return false;
	}
	/**
	 * Set the value of a cookie.
	 *
	 * @param string $cookie_name
	 * @param number $expiration
	 */
	function cookie_set($cookie_name, $expiration = 30) {
		$expiration = time() + ( 86400 * $expiration );
		setcookie( $cookie_name, 'True', $expiration, "/" );
	}
	/**
	 * Generate a random string with default length of 10 characters.
	 *
	 * @param number $length
	 * @return string
	 */
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString = '';
		for( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[rand( 0, $charactersLength - 1 )];
		}
		return $randomString;
	}
	/**
	 * Start a user session.
	 */
	function session_start() {
		session_start();
		$_SESSION['started'] = true;
	}
	/**
	 * Set a variable for a user session.
	 *
	 * @param string $key
	 * @param string $val
	 */
	function session_set($key, $val) {
		if ( $_SESSION['started'] ) {
			$_SESSION[$key] = $val;
		} else {
			// session not started..
		}
	}
	/**
	 * Get the value for a user session key.
	 *
	 * @param string $key
	 * @return boolean
	 */
	function session_get($key) {
		if ( $_SESSION['started'] ) {
			if ( isset( $_SESSION[$key] ) == false ) {
				// no key found
				return false;
			}
			return $_SESSION[$key];
		}
	}
	/**
	 * Does a session exist for user?
	 *
	 * @param string $key
	 * @return boolean
	 */
	function session_exists() {
		if ( isset( $_SESSION['started'] ) ) {
			return true;
		}
        	return false;
	}
	/**
	 * Clear the key for a session.
	 *
	 * @param string $key
	 * @return boolean
	 */
	function session_clear($key) {
		return session_unset( $key );
	}
	/**
	 * End the session for a user.
	 *
	 * @return boolean
	 */
	function session_end() {
		return session_unset();
	}
	/**
	 * Attempts to get the ip of the client request
	 *
	 * @return boolean|string
	 */
	function IP_get() {
		$ip = false;
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	// function IP_is_banned() {
	// }
	// function IP_ban() {
	// }
	// function IP_unban() {
	// }
}
?>
