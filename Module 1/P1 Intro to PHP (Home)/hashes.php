<?php
// Arrays
$numbers = [1, 2, 3];
$to_do_list = ['finish homework', 'review', 'cook'];
$to_do_list[] = 'practice_taxidermy'; // Append the array
echo "<p>You should {$to_do_list[0]} now!</p>";

// Count the length
$bookshelf = [
    'Catcher in the eye',
    'Anathem',
    'The glassbead game',
    'Hunger games'
];
echo "<p>I own " . count($bookshelf) . " books";

//Implode and Explode
//Implode() joins array elements into a string
$poem = ['Mares eat oats', 'goat eats oats', 'little lambs eat ivy.'];
echo implode(' and ', $poem) . "\n";

//Explode() splits a string into an array
$the_numbers = '4,8,15,16,23,42';
$dharma_hatch = explode(',', $the_numbers);

print_r($dharma_hatch);

// Hashes (Associative Arrays)
// Use key-value pairs
// Each key is unique, and corresponds to a single value within an array
// In PHP, arrays are just hashes where the keys are zero-based integers
$french_fruit = [
    'apple' => 'une pomme',
    'pineapple' => 'un ananas',
    'grapefruit' => 'un pamplemousse'
];
echo "<p>Aujourd hui, je vais manger {$french_fruit['grapefruit']}.</p>";

function toy_box_contains($toy, $toy_box)
{
    if (isset($toy_box[$toy])) {
        echo "<p>You have {$toy_box[$toy]} {$toy}.</p>";
    } else {
        echo "<p>You have no $toy.</p>";
    }
}
$toy_box = [
    'tin soldiers' => 52,
    'pick-up sticks' => 35,
    'dice' => 6,
    'transformers' => 4
];
toy_box_contains('dice', $toy_box);
toy_box_contains('robots', $toy_box);

// Nesting - Array of Hashes
$employees = [
    [
        'name' => 'Elly',
        'position' => 'ninja'
    ],
    [
        'name' => 'Jane',
        'position' => 'student'
    ]
];
echo "<p>{$employees[0]['name']} is a {$employees[0]['position']}.</p>";

// Nesting = Hash of Arrays
$favorite_foods = ['jane'=>['cabbage', 'pizza'],
                    'elly'=>['hamburger', 'chalk']];
$elly_eats = $favorite_foods['elly'];
echo "<p>Elly eats {$elly_eats[0]} and {$elly_eats[1]}.</p>";

$mixed_bag = ['one', 2, 'three', 4, [5=>'five', 'six'=>6]];
print_r($mixed_bag);

//Traversing Arrays
$words = ['one','two', 'buckle', 'my', 'shoe'];
foreach ($words as $word) {
    echo '<p>'.$word . ' </p>';
}

//Traversing Hashes
$names = ['Katniss Everdeen'=>'Jennifer Lawrence',
        'Hermione Granger'=>'Emma Watson',
        'James Bond'=>'Daniel Craig',
        'Carol Aird'=>'Cate Blanchett'];
foreach ($names as $charcter_name => $actor_name) {
    echo "<p>{$charcter_name} is played by {$actor_name}.</p>";
}

$football_teams = [
    ['coach' => 'Bobby',
    'teamname'=>'Strike Attack',
    'players'=>[
        ['name'=>'Jill', 'position'=>'goalie'],
        ['name'=>'Willard','position'=>'defense'],
        ['name'=>'Ace','position'=>'centre']
    ],
    'wins'=>5,
    'loses'=>2
],
    ['coach' => 'Sam',
    'teamname' => 'Running Scared',
    'players' => [
        ['name'=> 'Warren','position'=>'goalie'],
        ['name'=> 'Jane','position'=> 'defense'],
        ['name'=> 'Tim','position'=> 'centre']
    ],
    'wins'=>0,
    'loses'=>4
    ]];

    foreach($football_teams as $current_team){
        echo "<p>{$current_team['teamname']} is coached by {$current_team['coach']}.</p>";
        echo "<p>The team has {$current_team['wins']} wins and {$current_team['loses']} loses. </p>";
        echo "<p>The team roster:\n";
        foreach($current_team['players'] as $current_player){
            echo "<p>{$current_player['name']} plays {$current_player['position']}.</p>";    
        }
    }

    //Functions
    function square($x){
        return $x * $x;
    }
    echo "<p>16 squared = ".square(16);

    function say_good_day($name){
        echo "<p>A fine day indeed {$name}!</p>";
    }
    say_good_day('Elly');
?>