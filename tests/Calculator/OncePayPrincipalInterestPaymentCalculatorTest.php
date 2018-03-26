<?php
namespace Zwei\LoanCalculator\Tests\Calculator;

use Zwei\LoanCalculator\Calculator\OncePayPrincipalInterestPaymentCalculator;
use Zwei\LoanCalculator\Tests\LoanCalculatorTestCase;

/**
 * 一次性还本付息还款方式计算器单元测试
 *
 * Class OncePayPrincipalInterestPaymentCalculatorTest
 * @package Zwei\LoanCalculator\Tests\Calculator
 */
class OncePayPrincipalInterestPaymentCalculatorTest extends LoanCalculatorTestCase
{
    /**
     * 测试总收益
     */
    public function testGetTotalInterest()
    {
        $principal          = 10000;// 本金
        $yearInterestRate   = "0.10";// 年利率10%
        $months             = 12;// 借款12个月
        $time               = strtotime("2018-03-20 10:05");// 借款时间
        $decimalDigits      = 2;// 保留小数点后3位,默认保留2位
        $obj                = new OncePayPrincipalInterestPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
        $this->assertEquals("999.99", $obj->getTotalInterest());
    }

    /**
     * 测试还款计划
     */
    public function testGetPlanLists()
    {
        $principal          = 10000;// 本金
        $yearInterestRate   = "0.10";// 年利率10%
        $months             = 12;// 借款12个月
        $time               = strtotime("2018-03-20 10:05");// 借款时间
        $decimalDigits      = 2;// 保留小数点后3位,默认保留2位
        $obj                = new OncePayPrincipalInterestPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
        $planLists = $obj->getPlanLists();
//        print_r($planLists);
        // 第1期的利息 +  第一期剩余还款利息
        $this->assertEquals("999.99", bcadd($planLists[1]['interest'], $planLists[1]['remain_interest'], $decimalDigits));
//        $this->assertEquals("2708.33", $obj->getTotalInterest());
        $this->assertEquals("10000.00", $planLists[1]['principal']);
        $this->assertEquals("999.99", $planLists[1]['interest']);
        $this->assertEquals("10999.99", $planLists[1]['total_money']);
    }
}