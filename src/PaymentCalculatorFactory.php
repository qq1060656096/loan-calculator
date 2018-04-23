<?php
namespace Zwei\LoanCalculator;

use Zwei\LoanCalc\Exception\ParamsException;
use Zwei\LoanCalculator\Calculator\EqualTotalPaymentCalculator;
use Zwei\LoanCalculator\Calculator\EqualPrincipalPaymentCalculator;
use Zwei\LoanCalculator\Calculator\MonthlyInterestPaymentCalculator;
use Zwei\LoanCalculator\Calculator\OncePayPrincipalInterestPaymentCalculator;

/**
 * 还款计算器工厂类
 * Class PaymentCalculatorFactory
 * @package Zwei\LoanCalculator
 */
class PaymentCalculatorFactory
{
    /**
     * 等额本金计算器
     */
    const TYPE_EQUAL_PRINCIPAL = 1;

    /**
     * 等额本息计算器
     */
    const TYPE_EQUAL_TOTAL_PAYMENT = 2;

    /**
     * 每月还息到期还本还款方式计算器
     */
    const TYPE_MONTHLY_INTEREST = 3;

    /**
     * 一次性还本付息还款方式计算器
     */
    const TYPE_ONCE_PAY_PRINCIPAL_INTEREST = 4;

    /**
     * 获取计算器对象
     *
     * @param integer $type 类型
     * @param float $principal 本金
     * @param float $yearInterestRate 年利率
     * @param int $months 月数
     * @param int $time 借款时间
     * @param int $decimalDigits 保留几位小数(默认2)
     * @return PaymentCalculatorAbstract
     * @throws ParamsException
     */
    public static function getPaymentCalculatorObj($type, $principal, $yearInterestRate, $months, $time, $decimalDigits)
    {
        switch ($type) {
            case self::TYPE_EQUAL_PRINCIPAL:
                // 等额本金计算器
                $obj = new EqualPrincipalPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
                break;
            case self::TYPE_EQUAL_TOTAL_PAYMENT:
                $obj = new EqualTotalPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
                break;
            case self::TYPE_MONTHLY_INTEREST:
                $obj = new MonthlyInterestPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
                break;
            case self::TYPE_ONCE_PAY_PRINCIPAL_INTEREST:
                $obj = new OncePayPrincipalInterestPaymentCalculator($principal, $yearInterestRate, $months, $time, $decimalDigits);
                break;
            default:
                throw new ParamsException('参数非法');
                break;
        }
        return $obj;
    }
}