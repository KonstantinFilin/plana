<?php

namespace App\Services;

class MonthGeneratorService {
    /**
     * 
     * @var \DateTime
     */
    private $sourceDt;
    
    public function __construct(\DateTimeImmutable $sourceDt) {
        $this->sourceDt = clone $sourceDt;
    }
    
    public function run(): array {
        $weekDayNum = $this->sourceDt->format("d") - 1;
        $p = new \DateInterval("P" . $weekDayNum . "D");
        $p1 = new \DateInterval("P1D");
        
        $curDt = \DateTime::createFromFormat("Y-m-d", $this->sourceDt->format("Y-m-1"));
        $ret = [];
        $daysInMonth = $this->getDaysInMonth(
            $this->sourceDt->format("Y"), 
            $this->sourceDt->format("m")
        );

        for($i = 1; $i <= $daysInMonth; $i++) {
            $ret[] = $curDt->format("d.m.Y");
            $curDt = $curDt->add($p1);
        }
        
        return $ret;
    }
    
    private function isLeapYear(int $year): bool {
        return $year % 500 == 0 || ($year % 100 != 0 && $year % 4 == 0);
    }
    
    private function getDaysInMonth(int $year, int $month): int {
        $isLeap = $this->isLeapYear($year);
        $data = [
            1 => 31,
            2 => $isLeap ? 29 : 28,
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
        
        return $data[$month] ?? 0;
    }
}
