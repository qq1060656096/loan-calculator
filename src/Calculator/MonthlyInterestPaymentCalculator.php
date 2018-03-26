<?php
namespace Zwei\LoanCalculator\Calculator;

use Zwei\LoanCalculator\Helper;
use Zwei\LoanCalculator\PaymentCalculatorAbstract;

/**
 * 每月还息到期还本还款方式计算器
 *
 * Class MonthlyInterestPaymentCalculator
 * @package Zwei\LoanCalculator\Calculator
 *
 * @note 每月还息到期还本：每个月只支付利息，贷款到期时一次性归还贷款本金
 *
 * @note 按月付息到期还本适用于期限较短的贷款，通常是一年（含）以内的短期贷款，
 * @note 比如汽车抵押贷款等。所以，金融机构的短期贷款，都是标的月利率。
 *
 * @note 计算公式：
 * @note 月利息 = 贷款额度×月利率=100000×1%=1000（元）
 * @note 总利息 = 贷款额度×月利率×贷款期限=100000×1%×10=10000（元）
 *
 * @note 优点：对于借款人来讲，平时无还款压力，可以充分将资金用于经营项目。
 *
 * @note 缺点：对于贷款机构来讲，这种贷款相对按月还本的还款方式的贷款风险要大。
 *
 * @note 适用范围：
 * @note 适宜短期贷款，适合平时无现金流入或有很少现金流的借款人，如工程行业、种植业和养殖业。
 */
class MonthlyInterestPaymentCalculator extends PaymentCalculatorAbstract
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        // 设置总期数
        $this->totalPeriod = $this->months;
    }

    /**
     * @inheritdoc
     */
    public function getTotalPeriod()
    {
        return $this->totalPeriod;
    }

    /**
     * @inheritdoc
     *
     */
    public function getTotalInterest()
    {
        $decimalDigitsNew = $this->decimalDigits + 5;
        $monthInterestRate = bcdiv($this->yearInterestRate, 12, $decimalDigitsNew);
        // 总利息 = 贷款额度×月利率×贷款期限=100000×1%×10=10000（元）
        $result = bcmul($this->principal, $monthInterestRate, $decimalDigitsNew);
        $interest = bcmul($result, $this->months, $this->decimalDigits);
        return $interest;
    }

    /**
     * 计算每月还款利息
     *
     * @return float
     */
    public function calcMonthlyInterest()
    {
        $interest = bcdiv($this->getTotalInterest(), $this->months, $this->decimalDigits);
        return $interest;
    }

    /**
     * 计算每月还款本金
     * @param int $period 期数(还款第几期)
     * @return float
     */
    public function calcMonthlyPrincipal($period)
    {
        $monthlyPrincipal = $period == $this->months ? $this->principal : 0;
        $monthlyPrincipal = bcadd($monthlyPrincipal, 0, $this->decimalDigits);
        return $monthlyPrincipal;
    }

    /**
     * @inheritdoc
     */
    public function getPlanLists()
    {
        $paymentPlanLists = [];
        // 每月还款利息
        $monthlyInterest = $this->calcMonthlyInterest();
        // 总还款利息
        $totalInterest = $this->getTotalInterest();
        // 已还本金
        $hasPayPrincipal = 0;
        // 已还利息
        $hasPayInterest = 0;
        // 期数
        $period = 0;
        for($i = 0; $i < $this->totalPeriod; $i ++) {
            $period ++;
            // 每月还款本金
            $monthlyPaymentPrincipal = $this->calcMonthlyPrincipal($period);
            // 从新计算最后一期,还款本金和利息
            if ($period == $this->months) {
                $monthlyInterest = bcsub($totalInterest, $hasPayInterest, $this->decimalDigits);
            }
            $hasPayInterest     = bcadd($hasPayInterest, $monthlyInterest, $this->decimalDigits);
            $hasPayPrincipal    = bcadd($hasPayPrincipal, $monthlyPaymentPrincipal, $this->decimalDigits);
            // 剩余还款本金
            $rowRemainPrincipal = bcsub($this->principal, $hasPayPrincipal, $this->decimalDigits);
            // 剩余还款利息
            $rowRemainInterest  = bcsub($totalInterest, $hasPayInterest, $this->decimalDigits);
            $rowTotalMoney      = bcadd($monthlyPaymentPrincipal, $monthlyInterest, $this->decimalDigits);
            $rowPlan = [
                self::PLAN_LISTS_KEY_PERIOD => $period,// 本期还款第几期
                self::PLAN_LISTS_KEY_PRINCIPAL => $monthlyPaymentPrincipal,// 本期还款本金
                self::PLAN_LISTS_KEY_INTEREST => $monthlyInterest,// 本期还款利息
                self::PLAN_LISTS_KEY_TOTAL_MONEY => $rowTotalMoney,// 本期还款总额
                self::PLAN_LISTS_KEY_TIME => strtotime("+ {$period} month", $this->time),// 本期还款时间
                self::PLAN_LISTS_KEY_REMAIN_PRINCIPAL => $rowRemainPrincipal,// 剩余还款本金
                self::PLAN_LISTS_KEY_REMAIN_INTEREST => $rowRemainInterest,// 剩余还款利息
            ];
            $paymentPlanLists[$period] = $rowPlan;
        }
        return $paymentPlanLists;
    }
}