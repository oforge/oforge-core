<?php

use PayPal\Api\PaymentExecution;

define('OFORGE_SCRIPT_TIMEOUT', 90);
define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/vendor/autoload.php';

die();

$context = new \PayPal\Rest\ApiContext(new PayPal\Auth\OAuthTokenCredential('AfTL33gPrLxXeTBV4231A4YMPwMCFrmv2p4mKjubYJK81NtWxLE1pTP5re-K0cWG0DWYpRnWydUPrrFQ',
    'EF4rT730jlO4y7XfvOQS0BatT7xGuApvYeUNLm_RFoWueQt8Ysor3RzvW3N2_PY7fcPaDSDDO1ZbHer0'));
$context->setConfig([
    'mode' => 'LIVE',
]);
$payment = \PayPal\Api\Payment::get('PAYID-LZD3NBA0VX327432D334564V', $context);

print_r($payment);

die();
$execution = new PaymentExecution();
$execution->setPayerId($payment->getPayer()->getPayerInfo()->getPayerId());

// Execute the payment
// (See bootstrap.php for more on `ApiContext`)
$result = $payment->execute($execution, $context);
