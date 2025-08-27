<?php
    // Hashes Demo
    // Hashes use keys to retreieve values, instead of zero-based integers

    $actors = ['Patrick Stewart' => 'Jean-Luc Picard', 
                'Kate Mulgrew' => 'Kathryn Janeway',
                'William Shatner' => 'James T Kirk',
                'Harrison Ford' => 'Han Solo'];
    echo "<p>The best Star Trek captain was {$actors['Kate Mulgrew']}.</p>";
    //If we use values inside square bracket, it will show the errrors so we always have to use the keys to retreive the values

    //Traversing hashes
    foreach ($actors as $actor => $captain) {
        // Left hand side is always the key and the right hand side is always the value
        echo "<p>{$actor} played the role of Captain {$captain}.</p>";
    }
    
?>