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
     *
     * @return float
     */
    public function calcMonthlyInterest()
    {
        $interest = $this->getTotalInterest() / $this->months;
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
        for($i = 0; $i < $this->months; $i ++) {
            $period ++;
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