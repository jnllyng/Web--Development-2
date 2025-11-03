<?php
/*******w******** 
    Name: Jueun Yang
    Date: 2025-10-28
    Description: Number Guessing Game using Sessions, Tracks Guesses, and Highscore
****************/

session_start();

define("RANDOM_NUMBER_MAXIMUM", 100);
define("RANDOM_NUMBER_MINIMUM", 1);

$user_submitted_a_guess = isset($_POST['guess']);
$user_requested_a_reset = isset($_POST['reset']);

if (!isset($_SESSION['number'])) {
    $_SESSION['number'] = rand(RANDOM_NUMBER_MINIMUM, RANDOM_NUMBER_MAXIMUM);
    $_SESSION['count'] = 0;
}

$message = "";
$showNameForm = false;
$showHighscores = false;

if ($user_requested_a_reset) {
    $_SESSION['number'] = rand(RANDOM_NUMBER_MINIMUM, RANDOM_NUMBER_MAXIMUM);
    $_SESSION['count'] = 0;
    setcookie("username", "", time() - 3600);
    $message = "Game has been reset.";
}

if ($user_submitted_a_guess && !$user_requested_a_reset) {
    $guess = (int) $_POST['user_guess'];
    $_SESSION['count']++;

    if ($guess > $_SESSION['number']) {
        $message = "Too high.";
    } elseif ($guess < $_SESSION['number']) {
        $message = "Too low.";
    } else {
        $message = "Correct.";
        $showNameForm = true;
        $showHighscore = true;
    }
}

if (isset($_POST['save_score']) && isset($_POST['username']) && $_POST['username'] != '') {
    $username = htmlspecialchars($_POST['username']);
    setcookie("username", $username, time() + 3600);

    $score = $_SESSION['count'];
    $highscores = [];

    if (file_exists("highscores.json")) {
        $highscores = json_decode(file_get_contents("highscores.json"), true);
    }

    $highscores[] = ["name" => $username, "score" => $score];
    usort($highscores, fn($a, $b) => $a['score'] <=> $b['score']);
    $highscores = array_slice($highscores, 0, 3);
    file_put_contents("highscores.json", json_encode($highscores));

    $message = "Score saved.";
    $showNameForm = false;
    $showHighscores = true;

    $_SESSION['number'] = rand(RANDOM_NUMBER_MINIMUM, RANDOM_NUMBER_MAXIMUM);
    $_SESSION['count'] = 0;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Number Guessing Game</title>
</head>

<body>
    <h1>Guessing Game</h1>
    <?php if (!empty($message))
        echo "<p>$message</p>"; ?>
    <form method="post">
        <label for="user_guess">Your Guess</label>
        <input id="user_guess" name="user_guess" autofocus>
        <input type="submit" name="guess" value="Guess">
        <input type="submit" name="reset" value="Reset">
    </form>
    <br>
    <?php if ($showNameForm): ?>
        <form method="post">
            <label>Your Name:</label>
            <input type="text" name="username" required>
            <button type="submit" name="save_score">Save Score</button>
        </form>
    <?php endif; ?>
    <?php if ($showHighscores && file_exists("highscores.json")): ?>
        <h2>Highscore Board</h2>
        <ol>
            <?php
            $data = json_decode(file_get_contents("highscores.json"), true);
            foreach ($data as $entry):
                ?>
                <li><?= $entry['name'] ?> - <?= $entry['score'] ?> tries</li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</body>

</html>