<?php

namespace App\Services;

class WeekGeneratorService {
    /**
     * 
     * @var \DateTime
     */
    private $sourceDt;
    
    public function __construct(\DateTimeImmutable $sourceDt) {
        $this->sourceDt = clone $sourceDt;
    }
    
    public function run(): array {
        $weekDayNum = $this->sourceDt->format("N") - 1;
        $p = new \DateInterval("P" . $weekDayNum . "D");
        $p1 = new \DateInterval("P1D");
        
        $curDt = $this->sourceDt->sub($p);
        $ret = [];
        
        for($i = 1; $i <= 7; $i++) {
            $ret[] = $curDt->format("d.m.Y");
            $curDt = $curDt->add($p1);
        }
        
        return $ret;
    }
}
