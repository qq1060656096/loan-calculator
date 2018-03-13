<?php
include_once 'vendor/autoload.php';

$principal = 50000;
$yearInterestRate = 0.10;
$month = 12;
$time = 0;

echo <<<str
principal: $principal;
yearInterestRate: $yearInterestRate
month: $month

str;

//$obj = new \Zwei\LoanCalculator\Calculator\EqualPrincipalPaymentCalculator($principal, $yearInterestRate, $month, 0);
//$lists = $obj->getPlanLists();
//print_r($lists);


//$obj = new \Zwei\LoanCalculator\Calculator\EqualTotalPaymentCalculator($principal, $yearInterestRate, $month, 0);
//$lists = $obj->getPlanLists();
//print_r($lists);

//$obj = new \Zwei\LoanCalculator\Calculator\MonthlyInterestPaymentCalculator($principal, $yearInterestRate, $month, 0);
//$lists = $obj->getPlanLists();
//print_r($lists);

//$obj = new \Zwei\LoanCalculator\Calculator\OncePayPrincipalInterestPaymentCalculator($principal, $yearInterestRate, $month, 0);
//$lists = $obj->getPlanLists();
//print_r($lists);

$obj = new \Zwei\LoanCalculator\Calculator\FirstInterestLastPrincipalPaymentCalculator($principal, $yearInterestRate, $month, 0);
$lists = $obj->getPlanLists();
print_r($lists);