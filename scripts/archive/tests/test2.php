<?php
$c = app()->make('App\Http\Controllers\Api\PosApiController');
$m = new ReflectionMethod($c, 'cashierCandidates');
$m->setAccessible(true);
echo $m->invoke($c, 6, 'noname', null)->count();
