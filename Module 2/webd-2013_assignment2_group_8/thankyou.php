<?php

/*******w******** 

    Name: Group 8
    Date: 2025-09-25
    Description: Validates inputs, calculates totals, and displays an invoice.
        Shows validation errors if any, and triggers bonus easter egg.

****************/

function filterinput()
{
    $errors = [];
    $full_name = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
    if (!$full_name || trim($full_name) == '') {
        $errors[] = "You must enter a Full Name.";
    }
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    if (!$address || trim($address) == '') {
        $errors[] = "You must enter an Address.";
    }
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    if (!$city || trim($city) == '') {
        $errors[] = "You must enter the City.";
    }
    $province = filter_input(INPUT_POST, 'province', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(AB|BC|MB|NB|NL|NS|ON|PE|QC|SK|NT|NU|YT)$/']]);
    if (!$province) {
        $errors[] = "You must choose a valid Province.";
    }
    $postal = filter_input(INPUT_POST, 'postal', FILTER_DEFAULT);
    if (empty($postal)) {
        $errors[] = "Postal Code is required.";
    } elseif (!preg_match('/^(?:[A-Z]\d[A-Z][ -]?\d[A-Z]\d)$/i', $postal)) {
        $errors[] = "You must enter a valid Postal Code.";
    }
    $email = filter_input(INPUT_POST, 'email', FILTER_DEFAULT);
    if (empty($email)) {
        $errors[] = "Email Address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "You must enter a valid Email Address.";
    }
    $card_type = filter_input(INPUT_POST, 'cardtype', FILTER_SANITIZE_STRING);
    if (!$card_type) {
        $errors[] = "You must choose a Card Type.";
    }
    $card_name = filter_input(INPUT_POST, 'cardname', FILTER_SANITIZE_STRING);
    if (!$card_name || trim($card_name) == '') {
        $errors[] = "You must enter a Name on Card.";
    }
    $card_month = filter_input(INPUT_POST, 'month', FILTER_VALIDATE_INT);
    if (!$card_month || $card_month < 1 || $card_month > 12) {
        $errors[] = "Card Month is required.";
    }
    $card_year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
    $min_year = date("Y");
    $max_year = $min_year + 5;
    if (!$card_year || $card_year < $min_year || $card_year > $max_year) {
        $errors[] = "You must choose a valid year.";
    }
    $card_number = filter_input(INPUT_POST, 'cardnumber', FILTER_DEFAULT);
    if (empty($card_number)) {
        $errors[] = "Card Number is required.";
    } elseif (!preg_match('/^\d{10}$/', $card_number)) {
        $errors[] = "You must enter a valid Card Number.";
    }
    $items = [
        ['qtys' => 'qty1', 'description' => 'iMac', 'cost' => 1899.99],
        ['qtys' => 'qty2', 'description' => 'Razer Mouse', 'cost' => 79.99],
        ['qtys' => 'qty3', 'description' => 'WD HDD', 'cost' => 179.99],
        ['qtys' => 'qty4', 'description' => 'Nexus', 'cost' => 249.99],
        ['qtys' => 'qty5', 'description' => 'Drums', 'cost' => 119.99],
    ];
    $total = 0;
    foreach ($items as &$item) {
        $qty = filter_input(INPUT_POST, $item['qtys'], FILTER_VALIDATE_INT);
        if (!$qty || $qty < 0) {
            $item['qty'] = 0;
        } else {
            $item['qty'] = $qty;
        }
        $total += $item['qty'] * $item['cost'];
    }
    return [
        'errors' => $errors,
        'fullname' => $full_name,
        'address' => $address,
        'city' => $city,
        'province' => $province,
        'postal' => $postal,
        'email' => $email,
        'cardtype' => $card_type,
        'cardname' => $card_name,
        'month' => $card_month,
        'year' => $card_year,
        'cardnumber' => $card_number,
        'items' => $items,
        'total' => $total
    ];
}
$result = filterinput();
$content = '';
if (isset($result['fullname'])) {
    $content = "Thank you for your order, {$result['fullname']}";
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
    <?php if (empty($result['errors'])): ?>
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
                        <td><?= $result['address'] ?></td>
                        <td class="alignright">
                            <span class="bold">City:</span>
                        </td>
                        <td><?= $result['city'] ?></td>
                    </tr>
                    <tr>
                        <td class="alignright">
                            <span class="bold">Province:</span>
                        </td>
                        <td><?= $result['province'] ?></td>
                        <td class="alignright">
                            <span class="bold">Postal Code:</span>
                        </td>
                        <td><?= $result['postal'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="alignright">
                            <span class="bold">Email:</span>
                        </td>
                        <td colspan="2"><?= $result['email'] ?></td>
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
                        <td><span class="bold">Quantity</span></td>
                        <td><span class="bold">Description</span></td>
                        <td><span class="bold">Cost</span></td>
                    </tr>
                    <?php foreach ($result['items'] as $item): ?>
                        <?php if ($item['qty'] > 0): ?>
                            <tr>
                                <td><?= $item['qty'] ?></td>
                                <td><?= $item['description'] ?></td>
                                <td class="alignright"><?= $item['cost'] * $item['qty'] ?></td>
                            </tr>
                        <?php endif ?>
                    <?php endforeach ?>
                    <tr>
                        <td colspan="2" class="alignright">
                            <span class="bold">Totals</span>
                        </td>
                        <td class="alignright">
                            <span class="bold"><?= '$' . $result['total'] ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php if ($result['items'][0]['qty'] == 42): ?>
            <div id="vikingur">
                <h2>Congrats on the big order. Víkingur Ólafsson congratulates you.</h2>
                <iframe width="600" height="475" src="//www.youtube.com/embed/c2gVYB5oZ7o" style="border:0;"
                    allowfullscreen=""></iframe>
            </div>
        <?php endif ?>
    <?php else: ?>
        <div class="error">
            <h3>Here's a validation error:</h3>
            <table>
                <tbody>
                    <tr>
                        <td colspan="1">
                            <h3>Failed Validation</h3>
                        </td>
                    </tr>
                    <?php foreach ($result['errors'] as $error): ?>
                        <tr>
                            <td><?= $error ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</body>
</html>