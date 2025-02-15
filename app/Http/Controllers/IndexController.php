<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index() {
        $pv = new \App\Models\PlannerView(3);

        return view('index', [
            'pv' => $pv
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
