<?php

/**
 * For facebook activity feed - make links in text clicable
 */
if ( ! function_exists('make_links_clicable'))
{
    function make_links_clicable($s) {

        $pattern = "/(
     ([a-z\d_-]+(\.[a-z\d_-]+)*@|(ht|f)tps?:\/\/)?
     [a-z\d]+
     ((-|\.)[a-z\d]+)*
     (\.[a-z]{2,4})
     (\/[a-z\d+_,%&=*!~'\.\/\?\#\[\]-]*)?
     (?![a-z\d])
  )/ix";
        preg_match_all($pattern, $s, $m, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
        if (empty($m)) return $s;
        $result = substr($s, 0, $m[0][0][1]);
        for($j=0; $j<count($m); $j++){
            $value = $m[$j][0][0];
            $pos = $m[$j][0][1];
            if (strpos($value, '@') !== false) $link = 'mailto:'.$value;
            elseif (!preg_match("/^(ht|f)tps?:\/\/(.*)/i", $value)) $link = 'http://'.$value;
            else $link = $value;
            $result .= '<a href="'.$link.'">'.$value.'</a>';
            $start = $pos + strlen($value);
            $length = $j == count($m) - 1 ? 0 : $m[$j+1][0][1] - $start;
            $text = $length ? substr($s, $start, $length) : substr($s, $start);
            $result .= $text;
        }
        return $result;

    }
}