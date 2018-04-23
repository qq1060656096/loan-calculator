# zwei/loan-calculator 包

> 贷款计算器, 生成还款计划


## 安装前准备
> 1. php5.4以上版本
> 2. bcmath扩展 http://php.net/manual/zh/book.bc.php
> 3. 创建composer.json文件,并写入以下内容:

```json
{
  "require": {
    "zwei/loan-calculator": "dev-master"
  }
}
```
> 4. 执行composer install

计算器
## 使用示例(use)
> 1. 例如项目目录在"E:\web\php7\test"
> 2. 创建index.php,并加入以下内容

```php
<?php
include_once 'vendor/autoload.php';

use Zwei\LoanCalculator\Calculator\EqualTotalPaymentCalculator;
use Zwei\LoanCalculator\Calculator\EqualPrincipalPaymentCalculator;
use Zwei\LoanCalculator\Calculator\MonthlyInterestPaymentCalculator;
use Zwei\LoanCalculator\Calculator\OncePayPrincipalInterestPaymentCalculator;
use \Zwei\LoanCalculator\PaymentCalculatorFactory;

$principal          = 50000;// 本金
$yearInterestRate   = "0.10";// 年利率10%
$months             = 12;// 借款12个月
$time               = strtotime("2018-03-20 10:05");// 借款时间
$decimalDigits      = 2;// 保留小数点后3位,默认保留2位

// 等额本金计算器
$obj = PaymentCalculatorFactory::getPaymentCalculatorObj(PaymentCalculatorFactory::TYPE_EQUAL_PRINCIPAL, $principal, $yearInterestRate, $month, 0);
$lists = $obj->getPlanLists();
print_r($lists);

// 等额本息计算器
$obj = PaymentCalculatorFactory::getPaymentCalculatorObj(PaymentCalculatorFactory::TYPE_EQUAL_TOTAL_PAYMENT, $principal, $yearInterestRate, $month, 0);
$lists = $obj->getPlanLists();
print_r($lists);

// 每月还息到期还本还款方式计算器
$obj = PaymentCalculatorFactory::getPaymentCalculatorObj(PaymentCalculatorFactory::TYPE_MONTHLY_INTEREST, $principal, $yearInterestRate, $month, 0);
$lists = $obj->getPlanLists();
print_r($lists);

// 一次性还本付息还款方式计算器
$obj = PaymentCalculatorFactory::getPaymentCalculatorObj(PaymentCalculatorFactory::TYPE_ONCE_PAY_PRINCIPAL_INTEREST, $principal, $yearInterestRate, $month, 0);
$lists = $obj->getPlanLists();
print_r($lists);

// 等额本金计算器
$obj                = new EqualPrincipalPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
$planLists          = $obj->getPlanLists();// 获取还款计划
// 等额本息计算器
$obj                = new EqualTotalPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
$planLists          = $obj->getPlanLists();// 获取还款计划
// 每月还息到期还本还款方式计算器
$obj                = new MonthlyInterestPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
$planLists          = $obj->getPlanLists();// 获取还款计划
// 一次性还本付息还款方式计算器
$obj                = new OncePayPrincipalInterestPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
$planLists          = $obj->getPlanLists();// 获取还款计划
```

### 单元测试使用
> --bootstrap 在测试前先运行一个 "bootstrap" PHP 文件
* **--bootstrap引导测试:** phpunit --bootstrap ./Tests/TestInit.php ./Tests/

D:\phpStudy\php\php-7.1.13-nts\php.exe D:\phpStudy\php\php-5.6.27-nts\composer.phar update

D:\phpStudy\php\php-7.1.13-nts\php.exe vendor\phpunit\phpunit\phpunit --bootstrap tests/TestInit.php tests/

