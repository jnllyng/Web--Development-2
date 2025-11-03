<?php
/*******w******** 
    Name: Jueun Yang
    Date: 2025-10-31
    Description: Number Guessing Game using Sessions + Cookies, Tracks Guesses, Highscore
****************/

session_start();

define("RANDOM_NUMBER_MINIMUM", 1);
define("RANDOM_NUMBER_MAXIMUM", 100);

$user_submitted_a_guess = isset($_POST['guess']);
$user_requested_a_reset = isset($_POST['reset']);
$user_submitted_name = isset($_POST['username']) && $_POST['username'] != '';

if (!isset($_SESSION['target'])) {
    $_SESSION['target'] = rand(RANDOM_NUMBER_MINIMUM, RANDOM_NUMBER_MAXIMUM);
    $_SESSION['guess_count'] = 0;
}

if (!isset($_SESSION['high_scores'])) {
    $_SESSION['high_scores'] = []; 
}

$message = "";
$showNameForm = false;
$showHighscore = false;

if ($user_requested_a_reset) {
    $_SESSION['target'] = rand(RANDOM_NUMBER_MINIMUM, RANDOM_NUMBER_MAXIMUM);
    $_SESSION['guess_count'] = 0;
    $message = "Game has been reset.";
}

if ($user_submitted_a_guess && !$user_requested_a_reset) {
    $guess = (int)$_POST['user_guess'];
    $_SESSION['guess_count']++;

    if ($guess < $_SESSION['target']) {
        $message = "Higher Number";
    } elseif ($guess > $_SESSION['target']) {
        $message = "Lower Number";
    } else {
        $message = "Correct. You guessed it in {$_SESSION['guess_count']} tries.";
        $showNameForm = !isset($_COOKIE['username']);
        $showHighscore = isset($_COOKIE['username']);
    }
}

if ($user_submitted_name) {
    $username = htmlspecialchars($_POST['username']);
    setcookie("username", $username, time() + 3600 * 24 * 30); 

    $score = $_SESSION['guess_count'];
    $_SESSION['high_scores'][] = ['name' => $username, 'score' => $score];

    usort($_SESSION['high_scores'], function($a, $b) {
        return $a['score'] - $b['score'];
    });
    if (count($_SESSION['high_scores']) > 3) {
        $_SESSION['high_scores'] = array_slice($_SESSION['high_scores'], 0, 3);
    }

    $showNameForm = false;
    $showHighscore = true;

    $_SESSION['target'] = rand(RANDOM_NUMBER_MINIMUM, RANDOM_NUMBER_MAXIMUM);
    $_SESSION['guess_count'] = 0;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Number Guessing Game</title>
</head>
<body>
    <h1>Number Guessing Game</h1>
    <?php if ($message !== ''): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="number" name="user_guess" placeholder="Enter Number From 1 To 100">
        <button type="submit" name="guess">Guess</button>
        <button type="submit" name="reset">Reset</button>
    </form>

    <?php if ($showNameForm): ?>
        <form method="post">
            <label>Your Name:</label>
            <input type="text" name="username">
            <button type="submit">Save Score</button>
        </form>
    <?php endif; ?>

    <?php if ($showHighscore && !empty($_SESSION['high_scores'])): ?>
        <h2>Highscore Board</h2>
        <ol>
            <?php foreach ($_SESSION['high_scores'] as $entry): ?>
                <li><?= $entry['name'] ?> - <?= $entry['score'] ?> tries</li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>

</body>
</html>
