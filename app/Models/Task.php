<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = TBL_TASK;
    
    const PRIORITY_A = "A";    
    const PRIORITY_B = "B";    
    const PRIORITY_C = "C";    
    const PRIORITY_Z = "Z";

    public static function priorityList() {
        return [
            self::PRIORITY_A,
            self::PRIORITY_B,
            self::PRIORITY_C,
            self::PRIORITY_Z
        ];
    }
    
    public function getGroupNameAttribute() {
        return $this->group->abbr ?? $this->group->name ?? "";
    }
    
    public function group() {
        return $this->hasOne(TaskGroup::class, 'id', 'group_id');
    }
}
