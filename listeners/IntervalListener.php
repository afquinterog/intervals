<?php

namespace App\Listeners;

use App\Models\Interval;

class IntervalListener
{

     /**
     * Handle the event.
     *
     * @param  \App\Models\Interval $event
     * @return void
     */
    public function handle(Interval $model)
    {
        //Call the intervals functions
        $model->checkRanges();
    }
}
