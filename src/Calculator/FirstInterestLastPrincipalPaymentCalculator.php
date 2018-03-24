<?php
namespace Zwei\LoanCalculator\Calculator;

use Zwei\LoanCalculator\Helper;
use Zwei\LoanCalculator\PaymentCalculatorAbstract;

/**
 * 先息后本还款方式计算器
 *
 * Class FirstInterestLastPrincipalPaymentCalculator
 * @package Zwei\LoanCalculator\Calculator
 *
 * @note 借款成功当天一次性把利息扣掉，到期还本金。
 *
 * @note 计算公式：
 * @note 总利息 = 贷款额度×月利率×贷款期限=100000×1%×10=10000（元）
 *
 * @note 适用范围：适用民间短期的贷款。
 *
 * @note 优点：对贷款机构来讲，部分资金可以循环利用。
 *
 * @note 缺点：借款人成本较高，提前付息法律不予支持。
 */
class FirstInterestLastPrincipalPaymentCalculator extends PaymentCalculatorAbstract
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        // 设置总期数
        $this->totalPeriod = 1;
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
        $interest = $this->principal * $this->yearInterestRate / 12 * $this->months;
        $interest = Helper::formatMoney($interest);
        return $interest;
    }

    /**
     * 计算每月还款利息
     * @param int $period 期数(还款第几期)
     * @return float
     */
    public function calcMonthlyInterest($period)
    {
        $interest = $period == 1 ? $this->getTotalInterest() : 0;;
        $interest = Helper::formatMoney($interest);
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
        $monthlyPrincipal = Helper::formatMoney($monthlyPrincipal);
        return $monthlyPrincipal;
    }

    /**
     * @inheritdoc
     */
    public function getPlanLists()
    {
        $paymentPlanLists = [];

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
            // 每月还款利息
            $monthlyInterest = $this->calcMonthlyInterest($period);
            // 每月还款本金
            $monthlyPaymentPrincipal = $this->calcMonthlyPrincipal($period);

            $hasPayInterest += $monthlyInterest;
            $hasPayPrincipal += $monthlyPaymentPrincipal;
            // 剩余还款本金
            $rowRemainPrincipal = $this->principal - $hasPayPrincipal;
            $rowRemainPrincipal = Helper::formatMoney($rowRemainPrincipal);
            // 剩余还款利息
            $rowRemainInterest = $totalInterest - $hasPayInterest;
            $rowRemainInterest = Helper::formatMoney($rowRemainInterest);
            $rowPlan = [
                self::PLAN_LISTS_KEY_PERIOD => $period,// 本期还款第几期
                self::PLAN_LISTS_KEY_PRINCIPAL => $monthlyPaymentPrincipal,// 本期还款本金
                self::PLAN_LISTS_KEY_INTEREST => $monthlyInterest,// 本期还款利息
                self::PLAN_LISTS_KEY_TOTAL_MONEY => $monthlyPaymentPrincipal + $monthlyInterest,// 本期还款总额
                self::PLAN_LISTS_KEY_TIME => strtotime("+ {$period} month", $this->time),// 本期还款时间
                self::PLAN_LISTS_KEY_REMAIN_PRINCIPAL => $rowRemainPrincipal,// 剩余还款本金
                self::PLAN_LISTS_KEY_REMAIN_INTEREST => $rowRemainInterest,// 剩余还款利息
            ];
            $paymentPlanLists[$period] = $rowPlan;
        }
        return $paymentPlanLists;
    }
}