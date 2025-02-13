<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(?int $period = 3) {
        $pv = new \App\Models\PlannerView($period);

        return view('index', [
            'pv' => $pv
        ]);
    }

    public function schedule(string $dt, int $period) {
        
        $dtObj = \DateTime::createFromFormat("Ymd", $dt);
        $pv = new \App\Models\PlannerView($period, $dtObj);
        
        return view('planner', [
            'pv' => $pv
        ]);
    }
}
