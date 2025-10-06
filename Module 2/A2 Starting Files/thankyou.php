<?php

/*******w******** 
    
    Name: Chibuike Anyaba
    Date: 18/09/2025
    Description: Validate the form by PHP.

 ****************/

// fields_required_map is used to store the required fields that are invalid.
$fields_required_map = [];
// fields_blank is used to store the required fields that are empty.
$fields_blank = [];
// goods_perchase is used to store the goods.
$goods_perchase = [];
// name_map is used to store the name of the fields.
$name_map = [
    'fullname' => 'Full Name',
    'address' => 'Address',
    'city' => 'City',
    'province' => 'Province',
    'postal' => 'Postal Code',
    'email' => 'Email',
    'cardtype' => 'Card Type',
    'cardname' => 'Card Name',
    'month' => 'Expiry Date Month',
    'year' => 'Expiry Date Year',
    'cardnumber' => 'Card Number',
    'quantities' => 'Quantity'
];
function qtyCheck()
{
    global $fields_required_map, $fields_blank, $goods_perchase, $sumPrice;
    // check the quantity of goods
    $qty_list = ["qty1", "qty2", "qty3", "qty4", "qty5"];
    $goods_list = [
        ['name' => 'MacBook', 'price' => '1899.99'],
        ['name' => 'Razer', 'price' => '79.99'],
        ['name' => 'WD HDD', 'price' => '179.99'],
        ['name' => 'Nexus', 'price' => '249.99'],
        ['name' => 'Drums', 'price' => '119.99'],
    ];
    $hasValidQty = false;

    for ($i = 0; $i < count($qty_list); $i++) {
        if (!empty($_POST[$qty_list[$i]])) {
            if (preg_match('/^\d+$/', $_POST[$qty_list[$i]])) {
                $goods_perchase[] = [
                    'name' => $goods_list[$i]['name'],
                    'price' => $goods_list[$i]['price'] * $_POST[$qty_list[$i]],
                    'qty' => $_POST[$qty_list[$i]]
                ];
                $sumPrice += $goods_list[$i]['price'] * $_POST[$qty_list[$i]];
                $hasValidQty = true;
            } else {
                $hasValidQty = true;
                $fields_required_map[] = 'quantities';
            }
        }
    }
    if (!$hasValidQty) {
        $fields_blank[] = 'quantities';
    }
}
// check the required fields
function check_required()
{
    global $fields_blank;
    $fields_required = ['fullname', 'address', 'city', 'province', 'postal', 'email', 'cardtype', 'cardname', 'month', 'year', 'cardnumber'];
    $count = count($fields_required);
    for ($i = 0; $i < $count; $i++) {
        // if there is at lease 1 item ,the result will be true.
        if (!empty($_POST[$fields_required[$i]])) {
            // IF the field is not empty, check the rules of the field.
            check_required_rules($fields_required[$i]);
        } else {
            // if the field is empty, add it to the fields_blank array.
            $fields_blank[] = $fields_required[$i];
        }
    }
}
// check the rules of the required fields
function check_required_rules($field)
{
    global $fields_required_map;
    switch ($field) {
        case 'email':
            if (!filter_var($_POST[$field], FILTER_VALIDATE_EMAIL)) {
                $fields_required_map[] = 'email';
            }
            break;
        case 'postal':
            if (!preg_match('/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/', $_POST[$field])) {
                $fields_required_map[] = 'postal';
            }
            break;
        case 'cardnumber':
            if (!preg_match('/^\d{10}$/', $_POST[$field])) {
                $fields_required_map[] = 'cardnumber';
            }
            break;
        case 'month':
            if (!preg_match('/^\d+$/', $_POST[$field]) || $_POST[$field] < 1 || $_POST[$field] > 12) {
                $fields_required_map[] = 'month';
            }
            break;
        case 'year':
            $currentYear = date("Y");
            $fiveYearsLater = date("Y", strtotime("+5 years"));
            if (!preg_match('/^\d{4}$/', $_POST[$field]) || $_POST[$field] < $currentYear || $_POST[$field] > $fiveYearsLater) {
                $fields_required_map[] = 'year';
            }
            break;
        case 'province':
            $provinceOption = array("AB", "BC", "MB", "NB", "NL", "NS", "ON", "PE", "QC", "SK", "NT", "NU", "YT");
            if (!in_array($_POST[$field], $provinceOption)) {
                $fields_required_map[] = 'province';
            }
            break;
        case 'cardtype':
            if ($_POST[$field] != 'on') {
                $fields_required_map[] = 'cardtype';
            }
            break;
    }
}
// check the quantity of goods
qtyCheck();
// check the required fields
check_required();

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

    <!-- if no error， show the invoince -->
    <?php if (!count($fields_blank) && !count($fields_required_map)): ?>
        <div class="invoice">
            <h2>Thanks for your order <?= $_POST['fullname'] ?>.</h2>
            <h3>Here's a summary of your order:</h3>
            <table>
                <tr>
                    <td colspan="4">
                        <h3>Address Information</h3>
                    </td>
                </tr>
                <tr>
                    <td class="alignright"><span class="bold">Address:</span>
                    </td>
                    <td><?= $_POST['address'] ?> </td>
                    <td class="alignright"><span class="bold">City:</span>
                    </td>
                    <td><?= $_POST['city'] ?> </td>
                </tr>
                <tr>
                    <td class="alignright"><span class="bold">Province:</span>
                    </td>
                    <td><?= $_POST['province'] ?> </td>
                    <td class="alignright"><span class="bold">Postal Code:</span>
                    </td>
                    <td><?= $_POST['postal'] ?> </td>
                </tr>
                <tr>
                    <td colspan="2" class="alignright"><span class="bold">Email:</span>
                    </td>
                    <td colspan="2"><?= $_POST['email'] ?> </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td colspan="3">
                        <h3>Order Information</h3>
                    </td>
                </tr>
                <tr>
                    <td><span class="bold">Quantity</span>
                    </td>
                    <td><span class="bold">Description</span>
                    </td>
                    <td><span class="bold">Cost</span>
                    </td>
                </tr>
                <?php foreach ($goods_perchase as $goods): ?>
                    <tr>
                        <td><?= $goods['qty'] ?> </td>
                        <td><?= $goods['name'] ?> </td>
                        <td class='alignright'><?= $goods['price'] ?> </td>
                    </tr>
                <?php endforeach ?>
                <tr>
                    <td colspan="2" class='alignright'><span class="bold">Totals</span></td>
                    <td class='alignright'>
                        <span class='bold'>$ <?= $sumPrice ?></span>
                    </td>
                </tr>
            </table>
            <?php if($sumPrice >= 2000): ?>
                <div id="rollingrick">
                    <h2>Congrats on the big order. Mickey congratulates you.</h2>
                    <iframe width="600" height="475" src="//www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>
                </div>
            <?php endif ?>
        </div>
        <!-- if has errors, show errors -->
    <?php else: ?>
        <h4>List of Error/s</h4>
        <table>
            <?php if (!empty($fields_blank)): ?>
                <?php foreach ($fields_blank as $field): ?>
                    <tr>
                        <td class="blank_error">Blank Error: </td>
                        <td class="blank_error"><?= $name_map[$field] ?> cannot be empty! </td>
                    </tr>

                <?php endforeach ?>
            <?php endif ?>

            <?php if (!empty($fields_required_map)): ?>
                <?php foreach ($fields_required_map as $field): ?>
                    <tr>
                        <td class="invalid_error">Invalid Error: </td>
                        <td class="invalid_error"><?= $name_map[$field] ?> is invalid! </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </table>
    <?php endif ?>

</body>

</html>