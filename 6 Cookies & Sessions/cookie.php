<?php
	if(isset($_COOKIE['visit_count'])){
		$visit_count = $_COOKIE['visit_count'];
		$visit_count++;
	} else{
		$visit_count = 1;
	}

	$now = time();

	//	Set visit_count cookie. Expires in 8 days
	setcookie('visit_count', $visit_count, $now + 60 * 60 * 24 * 8);
	// 60 seconds * 60 minutes * 24 hours * 8 days
?>	
<!DOCTYPE html>
<html>
<head>
    <title>Cookie Test</title>
</head>
<body>
    <?php if ($visit_count > 1): ?>
        <h1>I see this is visit number <?= $visit_count ?> for you.</h1>
    <?php else: ?>
        <h1>I see this is your first visit.</h1>
    <?php endif ?>
    
    <p><strong>Time:</strong> <?= $now ?></p>
</body>
</html>