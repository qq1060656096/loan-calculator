<?php
namespace Zwei\LoanCalculator\Tests;

$phpUnitBootstrapAutoload = DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
$phpUnitBootstrapFile1 = dirname(__DIR__ ) .$phpUnitBootstrapAutoload;
$phpUnitBootstrapFile2 = dirname(dirname(dirname(dirname(__DIR__)))) . $phpUnitBootstrapAutoload;
if (file_exists($phpUnitBootstrapFile1)) {
    require_once $phpUnitBootstrapFile1;
} else if (file_exists($phpUnitBootstrapFile2)) {
    require_once $phpUnitBootstrapFile2;
}
else {
    throw new \Exception("Can't find $phpUnitBootstrapFile1 or $phpUnitBootstrapFile2 . Did you install dependencies via composer?");
}