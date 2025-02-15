<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskGroup;
use App\Http\Requests\TaskGroupRequest;

class TaskGroupController extends Controller
{
    public function getList() {        
        $q = \App\Models\TaskGroup::orderBy('name');
        $ts = \App\Services\TreeService::buildFromQuery($q);

        return response()->json($ts->getTree());       
    }
    
    public function editGet(TaskGroup $taskGroup) {
        return view('task-group.edit', [
            'taskGroup' => $taskGroup
        ]);
    }
    
    public function delete(TaskGroup $taskGroup) {
        $taskGroup->delete();
        return redirect(route('task-group'));
    }
    
    public function editPost(TaskGroupRequest $request, TaskGroup $taskGroup) {
        $this->fillAndSave($request, $taskGroup);
        
        return redirect(route('task-group'));
    }
    
    public function add(TaskGroupRequest $request) {
        $taskGroup = new TaskGroup();
        $taskGroup->pid = $request->post('pid');
        $this->fillAndSave($request, $taskGroup);
        
        return redirect(route('task-group'));
    }
    
    public function fillAndSave(TaskGroupRequest $request, TaskGroup $taskGroup) {
        $taskGroup->name = $request->post('name');
        $taskGroup->abbr = $request->post('abbr');
        $taskGroup->save();
    }
}
