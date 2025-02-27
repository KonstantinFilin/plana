<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    public function addGet() {
        $task = new \App\Models\Task();
        $task->duration_plan = 30;
        $task->priority = Task::PRIORITY_B;
        
        return view('task.add', [
            'task' => $task,
            'groupList' => \App\Models\TaskGroup::getAsSelectList()
        ]);
    }

    public function addPost(TaskRequest $request) {
        $task = new Task();
        $this->fillAndSave($request, $task);
        return redirect('/');
    }
    
    public function addBatch() {
        $request = request();
        $taskList = explode("\n", $request->post('task-list'));
        
        foreach ($taskList as $short) {
            $task = new Task();
            $task->group_id = $request->post('group_id');
            $task->short = $short;
            $task->save();
        }

        return redirect('/');
    }
    
    public function editGet(Task $task) {
        
        return view('task.edit', [
            'task' => $task,
            'groupList' => \App\Models\TaskGroup::getAsSelectList()
        ]);
    }
    
    public function editPost(TaskRequest $request, Task $task) {
        $this->fillAndSave($request, $task);
        return redirect('/');
    }
    
    public function delete(Task $task) {
        $task->delete();
        return redirect('/');
    }
    
    public function planDt(Task $task, int $dt) {
        $task->plan_dt = \DateTime::createFromFormat("Ymd", $dt);
        $task->save();
        
        return redirect('/');
    }
    
    public function close(Task $task, int $dt) {
        $task->dt_closed = \DateTime::createFromFormat("Ymd", $dt);
        $task->save();
        
        return redirect('/');
    }
    
    public function setDuration(Task $task, int $duration) {
        $task->duration_plan = $duration;
        $task->save();
        
        return redirect('/');
    }
    
    public function setPriority(Task $task, string $priority) {
        $task->priority = $priority;
        $task->save();
        
        return redirect('/');
    }
    
    private function fillAndSave(TaskRequest $request, Task $task) {
        
        $t = null;
        
        if ($request->post('plan_time_h') && $request->post('plan_time_m')) {
            $t = $request->post('plan_time_h') . ":" . $request->post('plan_time_m');
        }
        
        $task->group_id = $request->post('group_id');
        $task->short = $request->post('short');
        $task->description = $request->post('description');
        $task->duration_plan = $request->post('duration_plan', 60);
        $task->duration_fact = $request->post('duration_fact');
        $task->priority = $request->post('priority', Task::PRIORITY_B);
        $task->sum_paid = $request->post('sum_paid', 0);
        $task->sum_rest = $request->post('sum_rest', 0);
        $task->plan_dt = $request->post('plan_dt');
        $task->plan_time = $t;
        $task->dt_closed = $request->post('dt_closed');
        $task->save();
    }
}
