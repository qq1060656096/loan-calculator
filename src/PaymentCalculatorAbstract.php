<?php
namespace Zwei\LoanCalculator;

/**
 * 还款计算器抽象类
 *
 * Class PaymentCalculatorAbstract
 * @package Zwei\LoanCalculator
 */
abstract class PaymentCalculatorAbstract extends Base
{

    /**
     * 还款计划列表 本期还款期数键名
     * @var string
     */
    const PLAN_LISTS_KEY_PERIOD = 'period';
    /**
     * 还款计划列表 本期还款本金键名
     * @var string
     */
    const PLAN_LISTS_KEY_PRINCIPAL = 'principal';
    /**
     * 还款计划列表 本期还款利息键名
     * @var string
     */
    const PLAN_LISTS_KEY_INTEREST = 'interest';

    /**
     * 本期还款总金额
     * @var string
     */
    const PLAN_LISTS_KEY_TOTAL_MONEY = 'total_money';

    /**
     * 还款计划列表 本期还款时间键名
     * @var string
     */
    const PLAN_LISTS_KEY_TIME = 'time';

    /**
     * 本期还款后 剩余还款本金
     * @var string
     */
    const PLAN_LISTS_KEY_REMAIN_PRINCIPAL = 'remain_principal';

    /**
     * 本期还款后 剩余还款利息
     * @var string
     */
    const PLAN_LISTS_KEY_REMAIN_INTEREST = 'remain_interest';

    /**
     * 本金
     * @var float
     */
    protected $principal = 0;

    /**
     * @var float 年利率
     */
    protected $yearInterestRate = 0;

    /**
     * 月份
     * @var int
     */
    protected $months = 0;

    /**
     * 总期数
     *
     * @var int
     */
    protected $totalPeriod = 0;
    /**
     * 借款时间
     *
     * @var int
     */
    protected $time = 0;

    /**
     * 构造方法
     * PaymentCalc constructor.
     * @param float $principal 本金
     * @param float $yearInterestRate 年利率
     * @param int $months 月数
     * @param int $time 借款时间
     */
    public function __construct($principal, $yearInterestRate, $months, $time)
    {
        $this->principal = $principal;
        $this->yearInterestRate = $yearInterestRate;
        $this->months = $months;
        $this->time = $time;
        $this->init();
    }

    /**
     * 构造方法初始化
     */
    public function init()
    {

    }

    /**
     * 获取总期数
     *
     * @return integer
     */
    public abstract function getTotalPeriod();

    /**
     * 获取总利息
     *
     * @return float
     */
    public abstract function getTotalInterest();

    /**
     * 每月还款利息
     *
     * @return float
     */
    //public abstract function calcMonthlyInterest();

    /**
     * 每月还款本金
     *
     * @return float
     */
    //public abstract function calcMonthlyPrincipal();

    /**
     * 还款计划
     *
     * @return array
     */
    public abstract function getPlanLists();
}