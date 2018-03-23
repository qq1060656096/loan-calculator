<?php
namespace Zwei\LoanCalculator\Tests\Calculator;

use Zwei\LoanCalculator\Calculator\EqualTotalPaymentCalculator;
use Zwei\LoanCalculator\Tests\LoanCalculatorTestCase;

/**
 * 等额本息还款方式计算器单元测试
 *
 * Class EqualTotalPaymentCalculatorTest
 * @package Zwei\LoanCalculator\Tests\Calculator
 */
class EqualTotalPaymentCalculatorTest extends LoanCalculatorTestCase
{
    /**
     * 测试总收益
     */
    public function testGetTotalInterest()
    {
        $principal          = 1;// 本金
        $yearInterestRate   = "0.10";// 年利率10%
        $months             = 12;// 借款12个月
        $time               = strtotime("2018-03-20 10:05");// 借款时间
        $decimalDigits      = 2;// 保留小数点后3位,默认保留2位
        $obj                = new EqualTotalPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
        print_r($obj->getTotalInterest());
//        $this->assertEquals("2708.33", $obj->getTotalInterest());
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
        $obj                = new EqualTotalPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
        $planLists = $obj->getPlanLists();
//        print_r($planLists);
        // 第1期的利息 +  第一期剩余还款利息
        $this->assertEquals("549.90", bcadd($planLists[1]['interest'], $planLists[1]['remain_interest'], $decimalDigits));
//        $this->assertEquals("2708.33", $obj->getTotalInterest());
        $this->assertEquals("795.82", $planLists[1]['principal']);
        $this->assertEquals("83.33", $planLists[1]['interest']);
        $this->assertEquals("879.15", $planLists[1]['total_money']);
        $this->assertEquals("0.00", $planLists[12]['remain_principal']);
        $this->assertEquals("0.00", $planLists[12]['remain_interest']);
    }
}