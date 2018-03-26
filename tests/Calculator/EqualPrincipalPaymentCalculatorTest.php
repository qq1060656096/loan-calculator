<?php
namespace Zwei\LoanCalculator\Tests\Calculator;

use Zwei\LoanCalculator\Calculator\EqualPrincipalPaymentCalculator;
use Zwei\LoanCalculator\Tests\LoanCalculatorTestCase;

/**
 * 等额本金还款方式计算器单元测试
 *
 * Class EqualPrincipalPaymentCalculatorTest
 * @package Zwei\LoanCalculator\Tests\Calculator
 */
class EqualPrincipalPaymentCalculatorTest extends LoanCalculatorTestCase
{
    /**
     * 测试总收益
     */
    public function testGetTotalInterest()
    {
        $principal          = 50000;// 本金
        $yearInterestRate   = "0.10";// 年利率10%
        $months             = 12;// 借款12个月
        $time               = strtotime("2018-03-20 10:05");// 借款时间
        $decimalDigits      = 2;// 保留小数点后3位,默认保留2位
        $obj                = new EqualPrincipalPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
        $this->assertEquals("2708.32", $obj->getTotalInterest());
    }

    /**
     * 测试还款计划
     */
    public function testGetPlanLists()
    {
        $principal          = 50000;// 本金
        $yearInterestRate   = "0.10";// 年利率10%
        $months             = 12;// 借款12个月
        $time               = strtotime("2018-03-20 10:05");// 借款时间
        $decimalDigits      = 2;// 保留小数点后3位,默认保留2位
        $obj                = new EqualPrincipalPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
        $planLists = $obj->getPlanLists();
        print_r($planLists);
        // 第1期的利息 +  第一期剩余还款利息
        $this->assertEquals("2708.32", bcadd($planLists[1]['interest'], $planLists[1]['remain_interest'], $decimalDigits));
        $this->assertEquals("2708.32", $obj->getTotalInterest());
        $this->assertEquals("4166.66", $planLists[1]['principal']);
        $this->assertEquals("416.66", $planLists[1]['interest']);
        $this->assertEquals("	4583.32", $planLists[1]['total_money']);
        $this->assertEquals("0.00", $planLists[12]['remain_principal']);
        $this->assertEquals("0.00", $planLists[12]['remain_interest']);
    }
}