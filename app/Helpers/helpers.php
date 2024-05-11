<?php
  
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
  
//Converting timestamp to time ago
if (! function_exists('timeElapsedString')) {
    function timeElapsedString($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'Just now';
    }
}


// Convert string singular to prural
if (! function_exists('convertToPlural')) {
    function convertToPlural($string) {
        $plural = array(
            '/(quiz)$/i'                        => "$1zes",
            '/^(ox)$/i'                         => "$1en",
            '/([m|l])ouse$/i'                   => "$1ice",
            '/(matr|vert|ind)ix|ex$/i'          => "$1ices",
            '/(x|ch|ss|sh)$/i'                  => "$1es",
            '/([^aeiouy]|qu)y$/i'               => "$1ies",
            '/(hive)$/i'                        => "$1s",
            '/(?:([^f])fe|([lr])f)$/i'          => "$1$2ves",
            '/(shea|lea|loa|thie)f$/i'          => "$1ves",
            '/sis$/i'                           => "ses",
            '/([ti])um$/i'                      => "$1a",
            '/(tomat|potat|ech|her|vet)o$/i'    => "$1oes",
            '/(bu)s$/i'                         => "$1ses",
            '/(alias)$/i'                       => "$1es",
            '/(octop)us$/i'                     => "$1i",
            '/(ax|test)is$/i'                   => "$1es",
            '/(us)$/i'                          => "$1es",
            '/s$/i'                             => "s",
            '/$/'                               => "s"
        );

        $uncountable = array( 
            'sheep', 
            'fish',
            'deer',
            'series',
            'species',
            'money',
            'rice',
            'information',
            'equipment'
        );

        $irregular = array(
            'move'   => 'moves',
            'foot'   => 'feet',
            'goose'  => 'geese',
            'sex'    => 'sexes',
            'child'  => 'children',
            'man'    => 'men',
            'tooth'  => 'teeth',
            'person' => 'people',
            'valve'  => 'valves'
        );


        // save some time in the case that singular and plural are the same
        if (in_array( strtolower( $string ), $uncountable ) )
            return $string;


        // check for irregular singular forms
        foreach ($irregular as $pattern => $result ){
            $pattern = '/' . $pattern . '$/i';
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach ( $plural as $pattern => $result ){
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }
}


// Get auth user id
if (! function_exists('created_by')) {
    function created_by(){               
        return Auth::user()->id;     
    }    
}

// Get auth user id
if (! function_exists('updated_by')) {
    function updated_by(){               
        return Auth::user()->id;     
    }    
}

//Number format
if (! function_exists('money_format')) {
    function money_format($money){
        $sign = '';
        if(!is_numeric($money)){
            $money = 0;
        }
        if($money > 0){
            $money = $money;
        }else{
            $sign = '-';
            $money = abs($money);
        }
        $decimal = (string)($money - floor($money));
        $money = floor($money);
        $length = strlen($money);
        $m = '';
        $money = strrev($money);
        for($i=0;$i<$length;$i++){
            if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$length){
                $m .=',';
            }
            $m .=$money[$i];
        }
        $result = strrev($m);
        $decimal = preg_replace("/0\./i", ".", $decimal);
        $decimal = substr($decimal, 0, 3);
        if( $decimal != '0'){
            $result = $result.$decimal;
        }

        return $sign.$result;
    }
}


//Date format
if (! function_exists('format_date')) {
    function format_date($date){
        $date = date('d M, Y', strtotime($date));
        return $date;
    }
}






