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
    
    private function fillAndSave(TaskRequest $request, Task $task) {
        $task->group_id = $request->post('group_id');
        $task->short = $request->post('short');
        $task->description = $request->post('description');
        $task->duration_plan = $request->post('duration_plan', 60);
        $task->duration_fact = $request->post('duration_fact');
        $task->priority = $request->post('priority');
        $task->sum_paid = $request->post('sum_paid', 0);
        $task->sum_rest = $request->post('sum_rest', 0);
        $task->plan_dt = $request->post('plan_dt');
        $task->plan_time = $request->post('plan_time');
        $task->dt_closed = $request->post('dt_closed');
        $task->save();
    }
}
