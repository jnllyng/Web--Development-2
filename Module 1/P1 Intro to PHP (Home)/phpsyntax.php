<?php
    //Constants
    define("CLOSE_TO_PIE",3.141592);
    define("THE_ANSWER",42);
    define("STUDENT_FULL_NAME", "Elly Yang");
    echo STUDENT_FULL_NAME . " knows the answer: " . THE_ANSWER;

    //Variables
    // Case-sensitive
    // Starts with a letter or underscore, followed by any number of letters, numbers, or underscores.
    $goats_in_the_kitchen = 13;
    $goat_feet = $goats_in_the_kitchen * 4;
    $feet_story = "<p>Once there were " . $goat_feet . " goat feet in our kitchen.</p>";
    echo $feet_story;

    // Casting and Testing Variable Types
    $my_int = 12;
    $my_float = (float)$my_int; //Casting int to float
    unset($my_int); //Equivalent to $my_int = NULL;
    if(!isset($my_int) && is_float($my_float)){ //isset determines if a variable is declared and is different than null
        echo "<p>All is well.</p>"; 
    }

    // Null
    if(isset($new_variable)){
        echo "How very odd?";
    }
    $new_variable = '<p>I am no longer NULL.</p>';
    if(isset($new_variable)){
        echo $new_variable;
    }
    //String
    $name = 'Bobby McGee';
    $fancy_string = "My name is {$name}. \n";
    $plain_string = 'My name is {$name}.\n';
    echo $fancy_string;
    echo $plain_string;
    //String Length
    $old_shopping_list = 'bacon,chickpeas,gasoline,grapes';
    $new_shopping_list = $old_shopping_list . ',corn,tricycle';
    $output = "<p>Out list is ". strlen($new_shopping_list);
    $output .= " new characters long.</p>"; // Add to an existing string by using .=
    echo $output;

    //Truthiness/Falsiness
    function boolean_test($var){
        if($var){
            echo "<p>{$var} is true. </p>";
        }else{
            echo "<p>{$var} is false.</p>";
        }
    }

    boolean_test(TRUE);
    boolean_test(FALSE);

    //For Loops
    for($i = 100; $i >0; $i--){
        echo "<p>$i</p>";
    }
    //While Loop
    $i = 1;
    while ($i <= 10 ){
        echo "<p>".$i++."</p>";
    }
    
?>