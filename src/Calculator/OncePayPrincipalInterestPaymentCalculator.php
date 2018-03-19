<?php
namespace Zwei\LoanCalculator\Calculator;

use Zwei\LoanCalculator\Helper;
use Zwei\LoanCalculator\PaymentCalculatorAbstract;

/**
 * 一次性还本付息还款方式计算器
 *
 * Class OncePayPrincipalInterestPaymentCalculator
 * @package Zwei\LoanCalculator\Calculator
 *
 * @note 一次性还本付息：贷款到期后一次性归还本金和利息
 *
 * @note 计算公式：
 * @note 总利息 = 贷款额度×月利率×贷款期限=100000×1%×10=10000（元）
 *
 * @note 优势：既可提高资金占有率，也可缓解借款人的还贷压力。操作简单，省去了每月还贷的麻烦。
 *
 * @note 缺点：为了有效防止资金风险，银行规定，选择一次性还本付息的用户，贷款期限必须控制在1年以内。
 */
class OncePayPrincipalInterestPaymentCalculator extends PaymentCalculatorAbstract
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
        $interest = $period == $this->months ? $this->getTotalInterest() : 0;;
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