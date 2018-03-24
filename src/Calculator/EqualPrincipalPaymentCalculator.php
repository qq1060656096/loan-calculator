<?php
namespace Zwei\LoanCalculator\Calculator;

use Zwei\LoanCalculator\PaymentCalculatorAbstract;
use Zwei\LoanCalculator\Helper;


/**
 * 等额本金还款方式计算器
 *
 * Class EqualPrincipalPaymentCalculator
 * @package Zwei\LoanCalculator\Calculator
 *
 * @note 计算公式：
 * @note 每月还本付息金额=(本金/还款月数)+(本金-累计已还本金)×月利率
 * @note 每月本金=总本金/还款月数
 * @note 每月利息=(本金-累计已还本金)×月利率
 * @note 还款总利息=（还款月数+1）*贷款额*月利率/2
 * @note 还款总额=(还款月数+1)*贷款额*月利率/2+贷款额
 * @note
 * @note 等额本金还款法优势:
 * @note 等额本金还款法的优势在于会随着还款次数的增多，
 * @note 还债压力会日趋减弱，在相同贷款金额、利率和贷款年限的条件下，
 * @note 等额本金还款法的利息总额要少于等额本息还款法。
 *
 * @note 等额本金还款法适用人群:
 * @note 等额本金法因为在前期的还款额度较大，而后逐月递减，
 * @note 所以比较适合在前段时间还款能力强的贷款人，
 * @note 当然一些年纪稍微大一点的人也比较适合这种方式，因为随着年龄增大或退休，收入可能会减少。
 */
class EqualPrincipalPaymentCalculator extends PaymentCalculatorAbstract
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
        // 还款总利息 =（还款月数+1）*贷款额*月利率/2
        $result = bcmul($this->months + 1, $this->principal, $this->decimalDigits);
        $result = bcmul($result, $this->yearInterestRate, $this->decimalDigits);
        $result = bcdiv($result, 12, $this->decimalDigits);
        $interest = bcdiv($result, 2, $this->decimalDigits);
        return $interest;
    }

    /**
     * 计算每月还款利息
     * @param float $principal 本金
     * @param float $hasPayPrincipal 已还本金
     * @param float $yearInterestRate 年利率
     * @return float
     */
    public function calcMonthlyInterest($principal, $hasPayPrincipal, $yearInterestRate)
    {
        // 每月利息 =(本金-累计已还本金)×月利率
        $result = bcsub($principal, $hasPayPrincipal, $this->decimalDigits);
        $result = bcmul($result, $yearInterestRate, $this->decimalDigits);
        $interest = bcdiv($result, 12, $this->decimalDigits);
        return $interest;
    }

    /**
     * 计算每月还款本金
     *
     * @return float
     */
    public function calcMonthlyPrincipal()
    {
        $monthlyPrincipal = bcsub($this->principal, $this->months, $this->decimalDigits);
        return $monthlyPrincipal;
    }

    /**
     * @inheritdoc
     */
    public function getPlanLists()
    {
        $paymentPlanLists = [];
        $monthlyPaymentPrincipal = $this->calcMonthlyPrincipal();// 每月还款本金
        $totalInterest      = $this->getTotalInterest();// 总还款利息
        $hasPayPrincipal    = 0;// 已还本金
        $hasPayInterest     = 0;// 已还利息
        $period             = 0;// 期数

        for($i = 0; $i < $this->totalPeriod; $i ++) {
            $period ++;
            // 每月还款利息
            $monthlyInterest = $this->calcMonthlyInterest($this->principal, $hasPayPrincipal, $this->yearInterestRate);
            // 从新计算最后一期,还款本金和利息
            if ($period == $this->months) {
                $monthlyPaymentPrincipal = bcsub($this->principal, $hasPayPrincipal, $this->decimalDigits);
                $monthlyInterest = bcsub($totalInterest, $hasPayInterest, $this->decimalDigits);
            }
            $hasPayInterest     = bcadd($hasPayInterest, $monthlyInterest, $this->decimalDigits);// 已支付利息
            $hasPayPrincipal    = bcadd($hasPayPrincipal, $monthlyPaymentPrincipal, $this->decimalDigits);// 已还本金
            $rowRemainPrincipal = bcsub($this->principal, $hasPayPrincipal, $this->decimalDigits);// 剩余还款本金
            $rowRemainInterest  = bcsub($totalInterest, $hasPayInterest, $this->decimalDigits);// 剩余还款利息
            $rowTotalMoney      = bcadd($monthlyPaymentPrincipal, $monthlyInterest, $this->decimalDigits);// 本期还款总额

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