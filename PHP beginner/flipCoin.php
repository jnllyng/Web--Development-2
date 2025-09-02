<?php 

function multiple_flips($num_flips){
    $i = 0;
    $heads_count = 0;
    $tails_count = 0;

    while($i <= $num_flips){
        if(random_int(0,1)){
            $heads_count++;
        }else{
            $tails_count++;
        }
        $i++;
}
echo "Results: $heads_count Heads and $tails_count Tals. <br/>";

}
multiple_flips(100);
?>
