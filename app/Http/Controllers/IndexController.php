<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index() {
        $pv = new \App\Models\PlannerView(3);
        $taskList = \App\Models\Task::whereNull('plan_dt')->whereNull('dt_closed')->orderBy('priority')->get();
        $taskListGrouped = [];
        
        foreach($taskList as $task) {
            $groupName = $task->group ? $task->group->name : 'Без группы';
            $taskListGrouped[$groupName][] = $task;
        }
        
        ksort($taskListGrouped);

        return view('index', [
            'pv' => $pv,
            'groupList' => \App\Models\TaskGroup::getAsSelectList(),
            'taskListGrouped' => $taskListGrouped
        ]);
    }

    public function schedule(string $dt, int $period) {
        
        $dtObj = \DateTime::createFromFormat("Ymd", $dt);
        $pv = new \App\Models\PlannerView($period, $dtObj);
        $tpl = $period == 30 ? 'planner30' : 'planner';
        
        return view($tpl, [
            'pv' => $pv
        ]);
    }
}
