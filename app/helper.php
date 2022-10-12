<?php 



if (! function_exists('zero_fill')) {
    function zero_fill($val) {
        return sprintf("%08d", $val);
    }
}

if (! function_exists('toLetras')) {
    function toLetras($val)
    {
        return $val;
        $letras = [
            "1"=>"L",
            "2"=>"R",
            "3"=>"E",
            "4"=>"A",
            "5"=>"S",
            "6"=>"G",
            "7"=>"F",
            "8"=>"B",
            "9"=>"P",
            "0"=>"X",
        ];


        foreach ($letras as $numero => $letra) {
            
            $val = str_replace($numero, $letra, $val);
        }

        return $val;
    }
}

  
 ?>