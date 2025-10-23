<?php
	session_start();	//	Fires off a session cookie

	if(isset($_SESSION['visit_count'])){
		$_SESSION['visit_count']++;
	}else{
		$_SESSION['visit_count'] = 1;
	}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cookie Test</title>
</head>
<body>
    <?php if ($_SESSION['visit_count'] > 1): ?>
        <h1>I see this is visit number <?= $_SESSION['visit_count'] ?> for you.</h1>
    <?php else: ?>
        <h1>I see this is your first visit.</h1>
    <?php endif ?>
</body>
</html>