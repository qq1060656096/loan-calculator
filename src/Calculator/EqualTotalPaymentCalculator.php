<?php
namespace Zwei\LoanCalculator\Calculator;

use Zwei\LoanCalculator\Helper;
use Zwei\LoanCalculator\PaymentCalculatorAbstract;

/**
 * 等额本息还款方式计算器
 *
 * Class EqualTotalPaymentCalculator
 * @package Zwei\LoanCalculator\Calculator
 *
 * @note 等额本息还款，也称定期付息，即借款人每月按相等的金额偿还贷款本息，
 * @note 其中每月贷款利息按月初剩余贷款本金计算并逐月结清。把按揭贷款的本金总额与利息总额相加，
 * @note 然后平均分摊到还款期限的每个月中。作为还款人，每个月还给银行固定金额，
 * @note 但每月还款额中的本金比重逐月递增、利息比重逐月递减。
 *
 * @note 计算公式：
 * @note 每月月供额 =〔贷款本金×月利率×(1＋月利率)＾还款月数〕÷〔(1＋月利率)＾还款月数-1〕
 * @note 每月应还利息 = 贷款本金×月利率×〔(1+月利率)^还款月数-(1+月利率)^(还款月序号-1)〕÷〔(1+月利率)^还款月数-1〕
 * @note 每月应还本金 = 贷款本金×月利率×(1+月利率)^(还款月序号-1)÷〔(1+月利率)^还款月数-1〕
 * @note 总利息 = 还款月数×每月月供额-贷款本金
 *
 * @note 等额本息还款法的优缺点
 * @note 方便，还款压力小。每月还款额相等，便于购房者计算和安排每期的资金支出。
 * @note 因为平均分摊了还款金额，所以还款压力也平均分摊，特别适合前期收入较低，
 * @note 经济压力大，每月还款负担较重的人士。
 *
 * @note 等额本息还款法的缺点
 * @note 利息总支出高。在每期还款金额中，前期利息占比较大，
 * @note 后期本金还款占比逐渐增大。总体计算下来，利息总支出是所有还款方式中最高的。
 *
 * @note 等额本息还款法的适合人群
 * @note 家庭收入较为稳定的人群，特别是暂时收入比较少，经济压力比较大的人士。
 * @note 因为虽然每个月还款金额相同，但是所含本金和利息的比例不同，初期所还部分，
 * @note 利息占较大比例，而贷款本金所占的比例较低，不适合有提前还贷打算的人士。
 */
class EqualTotalPaymentCalculator extends PaymentCalculatorAbstract
{

    /**
     * @inheritdoc
     *
     */
    public function getTotalInterest()
    {
        $interest = $this->calcMonthlyPaymentMoney($this->principal, $this->months, $this->yearInterestRate) * $this->months - $this->principal;
        $interest = Helper::formatMoney($interest);
        return $interest;
    }

    /**
     * 计算每月还款利息
     * @param float $principal 本金
     * @param int $period 还款期数(第几期还款)
     * @param int $months 还款期数
     * @param float $yearInterestRate 年利率
     * @return float
     */
    public function calcMonthlyInterest($principal, $period, $months, $yearInterestRate)
    {
        $monthInterestRate = $yearInterestRate / 12;
        // 本期还款利息
        $interest = ($principal * $monthInterestRate * pow(1 + $monthInterestRate, $period - 1)) / pow(1 + $monthInterestRate, $months - 1);
        $interest = Helper::formatMoney($interest);
        return $interest;
    }

    /**
     * 计算每月还款本金
     *
     * @param float $principal 本金
     * @param int $period 还款期数(第几期还款)
     * @param int $months 还款期数
     * @param float $yearInterestRate 年利率
     * @return float
     */
    public function calcMonthlyPrincipal($principal, $period, $months, $yearInterestRate)
    {
        $monthInterestRate = $yearInterestRate / 12;
        // 本期还款本金  每月应还本金 = 贷款本金×月利率×(1+月利率)^(还款月序号-1)÷〔(1+月利率)^还款月数-1〕
        $monthlyPrincipal = ($principal * $monthInterestRate * pow(1 + $monthInterestRate, $period - 1)) / (pow(1 + $monthInterestRate, $months) -1);
        $monthlyPrincipal = Helper::formatMoney($monthlyPrincipal);
        return $monthlyPrincipal;
    }

    /**
     * 每月还款金额(本金 + 利息)
     *
     * @param float $principal 本金
     * @param int $months 月数
     * @param float $yearInterestRate 年利率
     * @return float|int
     */
    public function calcMonthlyPaymentMoney($principal, $months, $yearInterestRate)
    {
        $monthInterestRate = $yearInterestRate / 12;
        // 每月月供额 =〔贷款本金×月利率×(1＋月利率)＾还款月数〕÷〔(1＋月利率)＾还款月数-1〕
        $monthlyPaymentMoney = ($principal * $monthInterestRate * pow(1 + $monthInterestRate, $months)) / (pow(1 + $monthInterestRate, $months) -1);
        $monthlyPaymentMoney = Helper::formatMoney($monthlyPaymentMoney);
        return $monthlyPaymentMoney;
    }

    /**
     * @inheritdoc
     */
    public function getPlanLists()
    {
        $paymentPlanLists = [];
        // 每月还款金额(本金 + 利息)
        $monthlyPaymentMoney = $this->calcMonthlyPaymentMoney($this->principal, $this->months, $this->yearInterestRate);
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
            // 每月还款利息
            $monthlyInterest = $this->calcMonthlyInterest($this->principal, $period, $this->months, $this->yearInterestRate);
            // 每月还款本金
            $monthlyPrincipal = $this->calcMonthlyPrincipal($this->principal, $period, $this->months, $this->yearInterestRate);
            // 每月还款利息
            $monthlyInterest = $monthlyPaymentMoney - $monthlyPrincipal;

            // 从新计算最后一期,还款本金和利息
            if ($period == $this->months) {
                $monthlyPrincipal = $this->principal - $hasPayPrincipal;
                $monthlyPrincipal = Helper::formatMoney($monthlyPrincipal);
                $monthlyInterest = $monthlyPaymentMoney - $monthlyPrincipal;
                $monthlyInterest = Helper::formatMoney($monthlyInterest);
            }
            $hasPayInterest += $monthlyInterest;
            $hasPayPrincipal += $monthlyPrincipal;

            // 剩余还款本金
            $rowRemainPrincipal = $this->principal - $hasPayPrincipal;
            $rowRemainPrincipal = Helper::formatMoney($rowRemainPrincipal);
            // 剩余还款利息
            $rowRemainInterest = $totalInterest - $hasPayInterest;
            $rowRemainInterest = Helper::formatMoney($rowRemainInterest);
            // 防止浮点数溢出
            if ($period == $this->months) {
                $rowRemainInterest = "0.00";
            }
            $rowPlan = [
                self::PLAN_LISTS_KEY_PERIOD => $period,// 本期还款第几期
                self::PLAN_LISTS_KEY_PRINCIPAL => $monthlyPrincipal,// 本期还款本金
                self::PLAN_LISTS_KEY_INTEREST => $monthlyInterest,// 本期还款利息
                self::PLAN_LISTS_KEY_TOTAL_MONEY => $monthlyPrincipal + $monthlyInterest,// 本期还款总额
                self::PLAN_LISTS_KEY_TIME => strtotime("+ {$period} month", $this->time),// 本期还款时间
                self::PLAN_LISTS_KEY_REMAIN_PRINCIPAL => $rowRemainPrincipal,// 剩余还款本金
                self::PLAN_LISTS_KEY_REMAIN_INTEREST => $rowRemainInterest,// 剩余还款利息
                $monthlyPaymentMoney,
            ];
            $paymentPlanLists[$period] = $rowPlan;
        }
        return $paymentPlanLists;
    }
}