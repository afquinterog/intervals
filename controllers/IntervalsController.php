<?php

namespace App\Controllers;

use App\Core\App;
use App\Views\View;
use App\Core\Request;
use App\Models\Interval;

class IntervalsController
{
    public function index()
    {
        $request = Request::queryString();

        if ($request['edit']) {
            $intervalForUpdate = Interval::find($request['edit']);
        }

        if ($request['delete']) {
            $intervalDeleted = Interval::delete($request['delete']);
            $request['msg-danger'] = $intervalDeleted ? "The interval has been deleted" : null;
        }

        if ($request['deleteAll']) {
            $intervalDeletedAll = Interval::deleteAll();
            $request['msg-danger'] = $intervalDeletedAll ? "All the intervals has been deleted" : null;
        }

        $intervals = new Interval();
        $data = $intervals->all()->order('date_start')->get();

        $parameters = [
            'data' => $data,
            'request' => $request,
            'interval' => $intervalForUpdate ?? null,
            'delete' => $intervalDeleted ?? null,
            'deletedAll' => $intervalDeletedAll
        ];

        View::open('views/index', $parameters);
    }

    public function saveInterval()
    {
        $data = Request::post();

        $interval = new Interval(Request::post());

        if ($interval->validate()) {
            $interval->save();
            $msg = "msg=Interval saved/updated";
        } else {
            $msg = "msg-danger=Please verify the interval information";
        }

        Request::redirect("intervals?{$msg}");
    }
}
