<?php

use Illuminate\Support\Facades\Route;

Route::get('/{period?}', function (?int $period = 7) {
    
    $dayObj = new \DateTimeImmutable(); // \DateTimeImmutable::createFromFormat("Y-m-d", "2025-02-09");
    $dtList = getDtList($dayObj, $period);
    
    $content = [
        "dtList" => $dtList,
        "today" => $dayObj->format("d.m.Y"),
        "period" => $period
    ];

    return view('index', $content);
})->where("period", "3|7|30");

Route::get('/reports', function () {
    return view('reports');
})->name("reports");

function getDtList(\DateTimeImmutable $dayObj, int $period): array {
    
    $cnt = $period - 1;
    $p = new \DateInterval("P1D");
    $curDt = $dayObj;
    $dtList = [];
    
    if ($period == 30) {
        $mgs = new App\Services\MonthGeneratorService($dayObj);
        $dtList = $mgs->run();        
    } elseif ($period == 7) {
        $wgs = new App\Services\WeekGeneratorService($dayObj);
        $dtList = $wgs->run();
    } else {
        $dtList[] = $dayObj->format("d.m.Y");

        while ($cnt > 0) {
            $curDt = $curDt->add($p);
            $dtList[] = $curDt->format("d.m.Y");
            $cnt--;
        }
    }
    
    return $dtList;    
}
