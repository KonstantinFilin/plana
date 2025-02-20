<?php

namespace App\Models;

class PlannerView {
    /**
     * 
     * @var int
     */
    private $periodDuration;
    
    /**
     * 
     * @var \DateTime
     */
    private $selectedDt;
    
    /**
     * 
     * @var \DateTime
     */
    private $todayDt;
    
    /**
     * 
     * @var \DateTime
     */
    private $yesterdayDt;
    
    public function __construct(int $periodDuration, ?\DateTime $startDt = null) {
        $this->periodDuration = $periodDuration;
        $this->selectedDt = $startDt ?? new \DateTime();
        $this->todayDt = new \DateTime();
        $p = new \DateInterval("P1D");
        
        $this->yesterdayDt = (clone $this->todayDt)->sub($p);
    }

    private function getDtList30(): array {
        return (
            new \App\Services\MonthGeneratorService($this->selectedDt)
        )->run();        
    }
    
    private function getDtList7(): array {
        return (
            new \App\Services\WeekGeneratorService($this->selectedDt)
        )->run();        
    }
    
    private function getDtList3(): array {
        $dtList = [];
        $curDt = clone $this->selectedDt;
        $cnt = $this->periodDuration - 1;
        $dtList[] = $this->selectedDt;
        $p = new \DateInterval("P1D");
        
        while ($cnt > 0) {
            $curDt = $curDt->add($p);
            $dtList[] = clone $curDt;
            $cnt--;
        }
        
        return $dtList;
    }
    
    public function getDtList(): array {
        if ($this->periodDuration == 30) {
            return $this->getDtList30();    
        } elseif ($this->periodDuration == 7) {
            return $this->getDtList7();
        }

        return $this->getDtList3();
    }
    
    public function getTaskListPlanned(): array {
        $dtListObj = $this->getDtList();
        $dtList = [];
        
        foreach ($dtListObj as $do) {
            $dtList[] = $do->format('Y-m-d');
        }
        
        $taskList = Task::whereIn("plan_dt", $dtList)->get();
        $items = []; // collect($taskList)->groupBy("plan_dt");
        
        foreach ($taskList as $t) {
            $items[$t->plan_dt][substr($t->plan_time, 0, 2)][] = $t;
        }
        
        return $items;
    }
    
    public function getPeriodDuration(): int {
        return $this->periodDuration;
    }

    public function getSelectedDt(): \DateTime {
        return $this->selectedDt;
    }

    public function getTodayDt(): \DateTime {
        return $this->todayDt;
    }
    
    public function getYesterdayDt(): \DateTime {
        return $this->yesterdayDt;
    }
        
    public function getPrevDt(): \DateTime {
        return (clone $this->selectedDt)->sub(
            new \DateInterval("P" . $this->periodDuration . "D")
        );
    }
    
    public function getNextDt(): \DateTime {
        return (clone $this->selectedDt)->add(
            new \DateInterval("P" . $this->periodDuration . "D")
        );
    }
}
