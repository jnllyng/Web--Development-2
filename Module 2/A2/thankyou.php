<?php

/*******w******** 
    
    Name: Jueun Yang
    Date: 2025-09-10
    Description:

****************/

if (isset($_POST['fullname'])) {
    $content = "Thank you for your order, {$_POST['fullname']}.";
}
$items = [[$quantity => $_POST['qty1'], $description => 'iMac', $cost => 1899.99], 
[$quantity => $_POST['qty2'], $description => 'Razer Mouse', $cost => 79.99],
[$quantity => $_POST['qty3'], $description => 'WD HDD', $cost => 179.99],
[$quantity => $_POST['qty4'], $description => 'Nexus', $cost => 249.99],
[$quantity => $_POST['qty5'], $description => 'Drums', $cost => 119.99]];
$address = $_POST['address'];
$city = $_POST['city'];
$province = $_POST['province'];

// Data Validation
function filterinput() {
    $errors = [];

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $errors[] = "Invalid Email Address.";
    }
    $postal = filter_input(INPUT_POST,'postal', FILTER_VALIDATE_REGEXP, [
        'options' => ['regexp' => '/^(?:[A-Z]\d[A-Z][ -]?\d[A-Z]\d)$/i']
    ]);
    if (!$postal) {
        $errors[] = "Invalid Postal Code.";
    }

    return !$errors;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Thanks for your order!</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div class="invoice">
    <h2><?= $content ?></h2>
    <h3>Here's a summary of your order:</h3>
    <table>
        <tbody>
            <tr>
                <td colspan="4">
                    <h3>Address Information</h3>
                </td>
            </tr>
            <tr>
                <td class="alignright">
                    <span class="bold">Address:</span>
                </td>
                <td><?= $address ?></td>
                <td class="alignright">
                    <span class="bold">City:</span>
                </td>
                <td><?= $city ?></td>
            </tr>
            <tr>
                <td class="alignright">
                    <span class="bold">Province:</span>
                </td>
                <td><?= $province ?></td>
                <td class="alignright">
                    <span class="bold">Postal Code:</span>
                </td>
                <?php if(filterinput()): ?>
                <td><?=$_POST['postal']?></td>
                <?php else: ?>
                <td>Form could not be processed.</td>
                <?php endif ?>
            </tr>
            <tr>
                <td colspan="2" class="alignright">
                    <span class="bold">Email:</span>
                </td>
                <?php if(filterinput()): ?>
                <td colspan="2"><?=$_POST['email']?></td>
                <?php else: ?>
                <td>Form could not be processed.</td>
                <?php endif ?>
            </tr>
            
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <td colspan="3">
                    <h3>Order Information</h3>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="bold">Quantity</span>
                </td>
                <td>
                    <span class="bold">Description</span>
                </td>
                <td>
                    <span class="bold">Cost</span>
                </td>
            </tr>
            <!-- Quantity Loop?-->
            <tr>
             
                <td></td>
                <td></td>
                <td class="alignright"></td>
            
            </tr>
            <tr>
                <td colspan="2" class="alignright">
                    <span class="bold">Totals</span>
                </td>
                <td class="alignright">
                    <span class="bold"></span>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
</body>
</html>