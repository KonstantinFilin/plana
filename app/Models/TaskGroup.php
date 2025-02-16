<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    protected $table = TBL_TASK_GROUP;
    public $timestamps = false;
    
    public static function getAsSelectList() :array {
        $ts = \App\Services\TreeService::buildFromQuery(
            TaskGroup::orderBy('pid')->orderBy('name')
        );
        
        $tree = $ts->getTree();
        $listItems = [];
        
        foreach ($tree as $ti) {
            $listItems[$ti['item']['id']] = $ti['item']['name'];
            
            if ($ti['children']) {
                foreach ($ti['children'] as $child) {
                    $listItems[$child['item']['id']] = "&nbsp;&nbsp;&nbsp;&nbsp;" . $child['item']['name'];
                }
            }
        }
        
        return $listItems;
    }
}
