<?php

namespace App\Services;

class MonthGeneratorService {
    /**
     * 
     * @var \DateTime
     */
    private $sourceDt;
    
    public function __construct(\DateTime $sourceDt) {
        $this->sourceDt = clone $sourceDt;
    }
    
    public function run(): array {
        $weekDayNum = $this->sourceDt->format("d") - 1;
        $p = new \DateInterval("P" . $weekDayNum . "D");
        $p1 = new \DateInterval("P1D");
        
        $curDt = \DateTime::createFromFormat("Y-m-d", $this->sourceDt->format("Y-m-1"));
        $ret = [];
        $daysInMonth = $this->sourceDt->format("t");

        for($i = 1; $i <= $daysInMonth; $i++) {
            $ret[] = clone $curDt;
            $curDt->add($p1);
        }
        
        return $ret;
    }
}
