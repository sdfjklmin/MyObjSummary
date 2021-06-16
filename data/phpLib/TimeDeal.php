<?php
namespace lib;

/** 时间操作类：没有验证规则请正常使用
 * Class TimeDeal
 * @package app\common\lib
 */
class TimeDeal
{
    /** @var object \DateTime  */
    protected $date_time ;

    /** @var object \DateInterval */
    /** DateInterval(P1M) P固定格式 1M 对应下面的参数 1个月
     * Y	years P
     * M	months P
     * D	days P
     * W	weeks. P
     * H	hours PT
     * I	minutes PT
     * S	seconds PT
     */
    protected $date_interval;

    /** @var string  DateInterval fixed */
    protected $interval_spec ;

    /** @var bool is string */
    protected $string_format = false ;

    /** @var string default string format */
    protected $format = 'Y-m-d H:i:s';

    /**
     * TimeDeal constructor.
     * @param string $init
     * @param bool $is_string
     */
    private function __construct(string $init = 'now',$is_string=true)
    {
        if(!$is_string) {
            $init = date('Y-m-d H:i:s',$init);
        }
        try{
            $this->date_time = new \DateTime($init);

        }catch (\Exception $exception){
           echo 'DateTime init error';die;
        }
    }

    /** base unit
     * @return array
     */
    protected function unitBase()
    {
        return ['Y','M','D','W','H','I','S'];
    }

    /** set the fixed  DateInterval interval_spec
     * @param $unit
     */
    protected function setFixed($unit)
    {
       $fixed =  [
            'Y' => 'P',
            'M' => 'P',
            'D' => 'P',
            'W' => 'P',
            'H' => 'PT',
            'I' => 'PT',
            'S' => 'PT'
        ] ;
       $this->interval_spec = $fixed[$unit] ;
    }

    /** filter this unit by unitBase
     * @param $unit
     * @return string
     */
    protected function unit($unit)
    {
        if(!in_array($unit,$this->unitBase())) {
            $unit =  'D';
        }

        $this->setFixed($unit);

        if($unit == 'I') {
            $unit = 'M' ;
        }
        return $unit;
    }

    /** DateInterval
     * @param $num
     * @param $unit
     * @throws \Exception
     */
    protected function interval($num, $unit)
    {
        $unit = $this->unit($unit) ;

        $spec = $this->interval_spec.$num.$unit;

        $this->date_interval = new \DateInterval($spec);
    }

    /** Adds an amount of days, months, years, hours, minutes and seconds to a DateTime object
     * @param  $num
     * @param string $unit
     * @return $this
     */
    public function add($num, $unit='D')
    {
        try{
            $this->interval($num,$unit);

            $this->date_time->add($this->date_interval);

            return $this;

        }catch (\Exception $exception){
            echo 'add interval error';die;
        }

    }

    /** Subtracts an amount of days, months, years, hours, minutes and seconds from a DateTime object
     * @param $num
     * @param string $unit
     * @return $this
     */
    public function sub($num, $unit='D')
    {
        try{
            $this->interval($num,$unit);

            $this->date_time->sub($this->date_interval);

            return $this;
        }catch (\Exception $exception){
            echo 'sub interval error';die;
        }

    }

    /** get this date object
     * @return \DateTime|object
     */
    public function getDateObj()
    {
        return $this->date_time;
    }

    /** get this time for format
     * @return int|string
     */
    public function getTime()
    {
        if($this->string_format) {
            return $this->date_time->format($this->format);
        }else{
            return $this->date_time->getTimestamp();
        }
    }

    /** get this time with start and end
     * @return array
     */
    public function getTimeStartEnd()
    {
        $timestamp   = $this->date_time->getTimestamp();
        if($timestamp > time()) {
            //add
            $start_stamp = date('Y-m-d 00:00:00',time());
            $end_stamp   = date('Y-m-d 23:59:59',$timestamp);
        }else{
            //sub
            $start_stamp = date('Y-m-d 00:00:00',$timestamp);
            $end_stamp   = date('Y-m-d 23:59:59',time());
        }
        if($this->string_format) {
            return [$start_stamp,$end_stamp];
        }else{
            return [strtotime($start_stamp),strtotime($start_stamp)];
        }
    }

    /** getBaseUnit
     * @return array
     */
    public function getBaseUnit()
    {
        return [
            'Y'=>'years',
            'M'=>'months',
            'D'=>'days',
            'W'=>'weeks. These get converted into days, so can not be combined with D.',
            'H'=>'hours',
            'I'=>'minutes',
            'S'=>'seconds',
        ] ;
    }

    /**
     * @param bool $isString
     * @return $this
     */
    public function setFormat(bool $isString = true)
    {
        $this->string_format = $isString;
        return $this;
    }

    /** 当天
     * @param string $init
     * @param bool $is_string
     * @return TimeDeal
     */
    public static function now(string $init = 'now',bool $is_string=true)
    {
        $timeDeal = new self($init,$is_string);

        return $timeDeal;
    }

    /** 本周
     * @param bool $isString
     * @return array
     */
    public static function week(bool $isString = false)
    {
        $week = [1,2,3,4,5,6,0] ;
        $thisWeek = date('w');
        $subDay = array_search($thisWeek,$week);
        $addDay = count($week) - $thisWeek;
        if($addDay == 0) {
            $end = TimeDeal::now()->setFormat()->setFormat($isString)->getTimeStartEnd();
        }else{
            $end = TimeDeal::now()->add($addDay)->setFormat($isString)->getTimeStartEnd();
        }
        if($subDay == 0) {
            $start = TimeDeal::now()->setFormat($isString)->getTimeStartEnd();
        }else{
            $start = TimeDeal::now()->sub($subDay)->setFormat($isString)->getTimeStartEnd();
        }
        return [$start[0],$end[1]];
    }

    /** 本月
     * @param bool $isString
     * @return array
     */
    public static function month(bool $isString = false)
    {
        $start = date('Y-m-1 00:00:00');
        $end   = date('Y-m-t 23:59:59');
        if(!$isString) {
            $start = strtotime($start);
            $end   = strtotime($end);
        }
        return [$start,$end];
    }
}

//demo
$now   = TimeDeal::now()->setFormat()->getTimeStartEnd();
$week  = TimeDeal::week(true);
$month = TimeDeal::month(true);
var_dump($now,$week,$month);



