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
    
    // An array of hashes
    $employees = [
                ['name' => 'Leia Organa',
                'position' => 'Rebel scum'],
                ['name' => 'Elly Yang',
                'position' => 'Student']
                ];
    echo "<p>{$employees[1]['name']} is {$employees[1]['position']}.</p>";

    foreach ($employees as $employee){
        echo"<p>{$employee['name']} is {$employee['position']}.</p>";
    }

    $music_bands = [
    [
        'leader'   => 'Alice Johnson',
        'bandname' => 'The Rockers',
        'members'  => [
            ['name'=>'Tom', 'instrument'=>'guitar'],
            ['name'=>'Lily','instrument'=>'drums'],
            ['name'=>'Sam', 'instrument'=>'bass']
        ],
        'concerts_played' => 12,
        'concerts_missed' => 1
    ],
    [
        'leader'   => 'Bob Lee',
        'bandname' => 'Jazz Masters',
        'members'  => [
            ['name'=>'Clara','instrument'=>'piano'],
            ['name'=>'Dave', 'instrument'=>'saxophone'],
            ['name'=>'Eva',  'instrument'=>'trumpet']
        ],
        'concerts_played' => 8,
        'concerts_missed' => 3
    ]
];

// 여기에 foreach를 사용해 밴드 정보와 멤버 정보를 출력하세요.
foreach($music_bands as $current_music_bands){
    echo "<p>{$current_music_bands['leader']} is a team leader in {$current_music_bands['bandname']}.</p>";
    echo "<p>The team has played {$current_music_bands['concerts_played']} times and missed {$current_music_bands['concerts_missed']} time.</p>";
    foreach($current_music_bands['members'] as $current_members){
        echo"<p>{$current_members['name']} plays {$current_members['instrument']}.</p>";
    }

}

    
?>