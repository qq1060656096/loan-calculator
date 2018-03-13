<?php
namespace Zwei\LoanCalculator;

/**
 * 助手类
 *
 * Class Helper
 * @package Zwei\LoanCalculator
 */
class Helper
{
    /**
     * 根据数字保留指定位小数(注意本方法小数位直接舍去)
     *
     * @param float $amount 数字
     * @param integer $decimal_len 默认保留2位
     * @return string 返回保留2位小数后的数字字符串
     */
    public static function formatMoney($amount, $decimalLen = 2)
    {
        if(empty($amount)){
            $amount = 0;
        }
        $number = "$amount";
        //获取整数部分和小数部分
        $tmpArr         = explode('.',"$number",2);
        $integerPart    = $tmpArr[0];
        $decimalPart    = isset($tmpArr[1]) ? $tmpArr[1] : '';
        $decimalPartLen = strlen($decimalPart);
        //操作位数=保留位数-小数部分位数
        $opLen             = $decimalLen - $decimalPartLen;
        switch (true) {
            case $decimalLen <= 0:
                $newNumber = $integerPart;
                break;
            case $opLen > 0:// 保留位数大于(>)小数部分位数,就直接在后面加"0"
                $newDecimalPart    = $decimalPart.str_repeat('0',$opLen);
                $newNumber = $integerPart.'.'.$newDecimalPart;
                break;
            default:// 直接截取字符
                $newDecimalPart   = substr($decimalPart,0,$decimalLen);
                $newNumber = $integerPart.'.'.$newDecimalPart;
                break;
        }
        return $newNumber;
    }
}