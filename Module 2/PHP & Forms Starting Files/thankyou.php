<?php
//Ensure data was entered
if (isset($_POST['fname'])) {
    $content = "Thank you for your submission, {$_POST['fname']}.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you for your submission</title>
</head>

<body>
    <div id="wrapper">
        <h4><?= $content ?></h4>
        <table>
            <tr>
                <td>Name:</td>
                <td><?= $_POST['fname'] . " " . $_POST['lname'] ?></td>
                <td>Student Number:</td>
                <td><?= $_POST['studentnum'] ?></td>
            </tr>
            <tr>
                <td>Program:</td>
                <td><?= $_POST['program'] ?></td>
            </tr>
            <?php
            //If the user chose bit||ba, show their major
            if ($_POST['program'] == 'BIT'):  // Use the value instead of ID
            
                ?>
                <tr>
                    <td>Major:</td>
                    <td><?= $_POST['bitmajor'] ?></td>
                </tr>
            <?php endif ?>

            <?php
            if ($_POST['program'] == 'BA'):

                ?>
                <tr>
                    <td>Major:</td>
                    <td><?= $_POST['bamajor'] ?></td>
                </tr>
            <?php endif ?>
        </table>
    </div>
</body>

</html>