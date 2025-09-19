<?php $words = ['is a', 'loves']; 
    $employees = [['name' => 'Wally Glutton', 'position' => 'Ninja'],
              ['name' => 'Daisy Glutton', 'position' => 'Instructor']]; 
    $favourite_foods = ['Jane' => ['cabbage','pizza'],
                    'John' => ['rainbow trout','chalk']];

//Wally Glutton is a Ninja and Jane loves pizza. 
// $sentence = "{$employees[0]['name']} {$words[0]} {$employees[0]['position']} and Jane {$words[1]} {$favourite_foods['Jane'][1]} .";
// echo $sentence;


// $words = ['one','two','buckle','my','shoe'];

// foreach ($words as $word){

//   echo $word . ' ';

// }

$tasty_treats = ['zaaah' => ['name'=>'pizza', 'image' =>'pizza.png'],
                'chips' => ['name'=>'french fries', 'image'=>'fries.png'],
                'burger' => ['name'=>'hamburger', 'image'=>'burger.png'],
                'sheights' => ['name'=>'Silver Height lagers', 'images'=>'beer.png']];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php foreach($tasty_treats as $tasty_treat=>$info):?>
    <p><?= "Lets order some {$tasty_treat}"?></p>
    <p><?="I mean {$info['name']}"?></p>
    <img src="<?=$info['image']?>" alt="<?=$info['name']?>">
    <?php endforeach ?>
</body>
</html>