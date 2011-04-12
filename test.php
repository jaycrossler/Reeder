<?php header('content-type: application/json; charset=utf-8');

class custom_json {

    /**
     * Convert array to javascript object/array
     * @param array $array the array
     * @return string
     */
    public static function encode($array)
    {

        // determine type
        if(is_numeric(key($array))) {

            // indexed (list)
            $output = '[';
            for($i = 0, $last = (sizeof($array) - 1); isset($array[$i]); ++$i) {
                if(is_array($array[$i])) $output .= self::encode($array[$i]);
                else  $output .= self::_val($array[$i]);
                if($i !== $last) $output .= ',';
            }
            $output .= ']';

        } else {

            // associative (object)
            $output = '{';
            $last = sizeof($array) - 1;
            $i = 0;
            foreach($array as $key => $value) {
                $output .= '"'.$key.'":';
                if(is_array($value)) $output .= self::encode($value);
                else  $output .= self::_val($value);
                if($i !== $last) $output .= ',';
                ++$i;
            }
            $output .= '}';

        }

        // return
        return $output;

    }

    /**
     * [INTERNAL] Format value
     * @param mixed $val the value
     * @return string
     */
    private static function _val($val)
    {
        if(is_string($val)) return '"'.rawurlencode($val).'"';
        elseif(is_int($val)) return sprintf('%d', $val);
        elseif(is_float($val)) return sprintf('%F', $val);
        elseif(is_bool($val)) return ($val ? 'true' : 'false');
        else  return 'null';
    }

}

// prints ["apple","banana","blueberry"]
//echo custom_json::encode(array('apple', 'banana', 'blueberry'));

// prints {"name":"orange","type":"fruit"}
//echo custom_json::encode(array('name' => 'orange', 'type' => 'fruit'));

$big_test = array( 'ClientID' => 'eca2bebd-1101-f502-a769-4446bf26ed19',
    'Recommendations' => array(
    	array(
			'Uri' => 'http://www.mitre.org/services/rss/mitre_career_news.xml',
			'Score'=> 51341234,
			'Category' => 'rssfeed',
		),
    	array(
			'Uri' => 'http://feeds.gawker.com/lifehacker/full',
			'Score'=> 91235,
			'Category' => 'rssfeed',
		),
    	array(
			'Uri' => 'http://rss.cnn.com/rss/cnn_tech.rss',
			'Score'=> 125,
			'Category' => 'rssfeed',
		),		
    ),
);

$json = custom_json::encode($big_test);

echo isset($_GET['callback'])
    ? "{$_GET['callback']}($json)"
    : $json;

?>