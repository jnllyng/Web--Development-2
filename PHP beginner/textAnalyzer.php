<?php 

function count_words($text){
    return str_word_count($text);
}

function count_given_string($str, $text){
    $str = '/' . strtolower($str) . '/';
    $text = strtolower($text);
    $count = array();
    preg_match_all($str, $text, $count);
    return sizeof($count[0]);
}

?>