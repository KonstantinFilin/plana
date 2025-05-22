<?php

namespace App\Services;

class Month
{
    protected $y;
    protected $m;

    public static function createFromPeriod($period)
    {
        if (strlen($period) != 6) {
            throw new \InvalidArgumentException("Period must be 6 chars long: " . $period);
        }

        $y = substr($period, 0, 4);
        $m = substr($period, -2);

        return new self($y, $m);
    }

    public static function createFromDt($dt)
    {
        list($y, $m, $d) = explode('-', $dt, 3);
        return new self($y, $m);
    }

    function __construct($y = 0, $m = 0) {

        if (!$y) {
            $y = date("Y");
        }

        if (!$m) {
            $m = date("m");
        }

        $this->y = intval($y);
        $this->m = intval($m);

        if ($m > 12 || $m < 1) {
            throw new \InvalidArgumentException("Month must be between 1 and 12 [current: " . $m . "]");
        }
    }

    public function getFullFromDayNum($dayNum)
    {
        $dayNum = intval($dayNum);

        return $this->getY() . "-" . $this->getM() . "-" . sprintf("%02.u", $dayNum );
    }

    public function isWeekend($dayNum) {
        return $this->isSaturday($dayNum) || $this->isSunday($dayNum);
    }

    public function isSunday($dayNum)
    {
        return $this->getWeekdayNum($dayNum) == 7;
    }

    public function isSaturday($dayNum)
    {
        return $this->getWeekdayNum($dayNum) == 6;
    }

    public function getWeekdayName($dayNum)
    {
        $weekdayNum = $this->getWeekdayNum($dayNum);
        $weekdayNames = $this->getWeekdayNamesShort();

        return $weekdayNames[$weekdayNum];
    }

    public function getWeekdayNamesShort()
    {
        return [
            1 => "Пн",
            2 => "Вт",
            3 => "Ср",
            4 => "Чт",
            5 => "Пт",
            6 => "Сб",
            7 => "Вс",
        ];
    }

    public static function getNameFromPeriod($period)
    {
        $m = intval(substr($period, 4));
        $y = substr($period, 0, 4);
        $monthNames = self::getMonthNames();
        $monthName = isset($monthNames[$m]) ? $monthNames[$m] : "???";
        return $monthName . " " . $y;
    }

    public static function generateDtList($dtFrom, $dtTo, $format = "Y-m-d")
    {
        $dtObjFrom = new \DateTime($dtFrom);
        $dtObjTo = new \DateTime($dtTo);

        if (!$dtObjFrom || !$dtObjTo) {
            return [];
        }

        if ($dtObjFrom > $dtObjTo) {
            $d = $dtObjTo;
            $dtObjTo = $dtObjFrom;
            $dtObjFrom = $d;
        }

        $ret = [ ];
        $interval1d = new \DateInterval("P1D");

        while ($dtObjFrom <= $dtObjTo) {
            $ret[] = $dtObjFrom->format($format);
            $dtObjFrom->add($interval1d);
        }

        return $ret;
    }

    public static function getMonthNames()
    {
        return [
            1 => "Январь",
            2 => "Февраль",
            3 => "Март",
            4 => "Апрель",
            5 => "Май",
            6 => "Июнь",
            7 => "Июль",
            8 => "Август",
            9 => "Сентябрь",
            10 => "Октябрь",
            11 => "Ноябрь",
            12 => "Декабрь",
        ];
    }

    public static function getMonthNamesGen()
    {
        return [
            1 => "Января",
            2 => "Февраля",
            3 => "Марта",
            4 => "Апреля",
            5 => "Мая",
            6 => "Июня",
            7 => "Июля",
            8 => "Августа",
            9 => "Сентября",
            10 => "Октября",
            11 => "Ноября",
            12 => "Декабря",
        ];
    }

    public function getWeekdayNum($day)
    {
        $curDt = sprintf(
            "%u-%u-%u",
            $this->y,
            $this->m,
            $day
        );
        $dt = new \DateTime($curDt);
        return $dt->format("N");
    }

    public function getWeekdayNumList()
    {
        $ret = [];
        $maxDay = $this->getMaxDay();

        for($day=1; $day<= $maxDay; $day++) {
            $ret[$day] = $this->getWeekdayNum($day);
        }

        return $ret;
    }

    public function getMinDt()
    {
        return $this->getY() . "-" . $this->getM() . "-01";
    }

    public function getMaxDt()
    {
        return $this->getY() . "-" . $this->getM() . "-" . $this->getMaxDay();
    }

    public function getMaxDay()
    {
        $ret = [
            1 => 31,
            2 => 28,
            3 => 31,
            4 => 30,
            5 => 31,
            6 => 30,
            7 => 31,
            8 => 31,
            9 => 30,
            10 => 31,
            11 => 30,
            12 => 31
        ];

        if ($this->y%400 == 0 || ($this->y%4 == 0 && $this->y%100 !== 0)) {
            $ret[2] = 29;
        }

        return $ret[$this->m];
    }

    public function isPast()
    {
        $curY = intval(date("Y"));
        $curM = intval(date("m"));

        return $curY > $this->y || ($curY == $this->y && $curM > $this->m);
    }

    public function isFuture()
    {
        $curY = intval(date("Y"));
        $curM = intval(date("m"));

        return $curY < $this->y || ($curY == $this->y && $curM < $this->m);
    }

    public function isCurrent()
    {
        $curY = intval(date("Y"));
        $curM = intval(date("m"));

        return $this->y == $curY && $this->m == $curM;
    }

    public function isToday($dt)
    {
        $today = new \DateTime(date("Y-m-d"));
        $dtObj = new \DateTime($dt);

        return $dtObj > $today;
    }

    public function isDtInFuture($dt)
    {
        $today = new \DateTime(date("Y-m-d"));
        $dtObj = new \DateTime($dt);

        return $dtObj > $today;
    }

    public function getPeriod(): string
    {
        return $this->getY() . $this->getM();
    }
    
    public function getName()
    {
        return self::getNameFromPeriod($this->y . $this->m);
    }

    public function getNameShort()
    {
        $monthNames = self::getMonthNames();
        $monthName = isset($monthNames[$this->m]) ? $monthNames[$this->m] : "???";
        return mb_substr($monthName, 0, 3) . " " . $this->y;
    }

    public function getPrev()
    {
        return $this->getPrevMonth();
    }
    
    public function jumpBack(int $times): Month {
        $month = clone $this;
        
        while ($times > 0) {
            $times--;
            $month = $month->getPrev();
        }
        
        return $month;
    }
    
    public function jumpForward(int $times): Month {
        $month = clone $this;
        
        while ($times > 0) {
            $times--;
            $month = $month->getNext();
        }
        
        return $month;
    }

    public function getPrevMonth()
    {
        $m = $this->m;
        $y = $this->y;

        if ($m == 1) {
            $m = 12;
            $y--;
        } else {
            $m--;
        }

        return new self($y, sprintf("%02.u", $m));
    }

    public function getNext()
    {
        return $this->getNextMonth();
    }

    public function getNextMonth()
    {
        $m = $this->m;
        $y = $this->y;

        if ($m == 12) {
            $m = 1;
            $y++;
        } else {
            $m++;
        }

        return new self($y, sprintf("%02.u", $m));
    }

    public function __toString()
    {
        return $this->getY() . $this->getM();
    }

    function getY() {
        return $this->y;
    }

    function getM() {
        return sprintf("%02.u", $this->m);
    }

    public function hasDt($dt)
    {
        $monthOth = self::createFromDt($dt);
        return $this->getY() == $monthOth->getY() && $this->getM() == $monthOth->getM();
    }
    
    public static function getDaysAmountBetweenDates(\DateTime $dt1, \DateTime $dt2) : int {
        return ($dt2->format("U") - $dt1->format("U")) / (24*3600);
    }

    public function periodContains($dt, $dtFrom, $dtTo)
    {
        if (!$dt instanceof \DateTime) {
            $dt = new \DateTime($dt);
        }

        if (!$dt) {
            throw new \InvalidArgumentException("Invalid argument \$dt");
        }

        $dtFromObj = new \DateTime($dtFrom);

        if (!$dtFromObj) {
            throw new \InvalidArgumentException("Invalid argument \$dtFrom");
        }

        if ($dtFromObj > $dt) {
            return false;
        }

        if (!$dtTo) {
            return true;
        }

        $dtToObj = new \DateTime($dtTo);

        if (!$dtToObj) {
            throw new \InvalidArgumentException("Invalid argument \$dtTo");
        }

        if ($dtToObj >= $dt) {
            return true;
        }

        return false;
    }
}
