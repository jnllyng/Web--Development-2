<?php
/* PHP Intro Demo
    Showing the basics of php syntax
    August 27, 2025 
*/

echo "<p>Hello World</p>";

// Variables
$cats = 13;     // Declare variables with $
$cat_paws = $cats * 4;
$paw_story = "Once there were " . $cat_paws . " cat paws in our kitchen.";      //Concat with . instead of addition sign
echo $paw_story;


$my_int = 12;
$my_float = (float) $my_int;    // Casting to a float
unset($my_int);     //Equivalent to setting to NULL
if (!isset($my_int) && is_float($my_float)) {
    // If my_int is NULL and my_float is float echo "All is well"
    echo "<p>All is well</p>";
}

// Constants
define("THE_ANSWER", 42);
define("FULL_NAME", "Elly");
echo "<p>" . FULL_NAME . " knows the answer: " . THE_ANSWER . "</p>";

// String
$name = "Jabba the Hutt";
$fancy_string = "My name is {$name}.<br/>";
$plain_string = 'My name is {$name}.<br/>';

echo $fancy_string;
echo $plain_string;

$fancy_string .= "<p>Our name is " . strlen($name) . " characters long.</p>";
echo $fancy_string;

// Arrays
$numbers = [1,2,3];
$to_do_list = ["assignment", "review", "quiz"];
$to_do_list[] = "practice taxidermy";
echo "Elly, {$to_do_list[3]} now!";
echo "<p> There are " . count($to_do_list) ." items in our array. </p>";

// Print_r
$numbers = "5,9,15,18,23,42,50";
$hatch = explode(",", $numbers);    // Splits a string into an array
print_r($hatch);    // Prints an array into human-readable informaiton
foreach($hatch as $code){   
    echo "<p>Now press {$code}</p>"; 
}

// Functions
function say_good_day($name) {
    echo "<p>A fine day indeed, {$name}!</p>";
}
say_good_day("J"); // If we don't put name in the bracket, it shows error 

?>