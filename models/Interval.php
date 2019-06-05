<?php

namespace App\Models;

use App\Core\App;
use Carbon\Carbon;
use App\Core\Database\Model;
use App\Core\Database\Inflect;
use App\Listeners\IntervalListener;

class Interval extends Model
{

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => IntervalListener::class,
        'updated' => IntervalListener::class,
    ];

    /**
     * Model validations
     *
     * @var array
     */
    protected $validations = [
        'date_start' => "date",
        'date_end' => "date",
        'price' => 'number'
    ];


    /**
     * Check the new interval against the actual intervals in the database and verify if intervals are overlapped or if
     * can be merged.
     *
     * @return void
     */
    public function checkRanges()
    {
        $data = App::get("database")->all("intervals")->order("date_start")->get();

        foreach ($data as $interval) {
            $interval = new Interval((array) $interval);

            if ($interval->id != $this->id) {
                $this->adjustIntervalsWhenAreContained($this, $interval);

                $this->adjustIntervalsWhenAreOverlapped($this, $interval);

                $this->mergeIfPossible($this, $interval);
            }
        }
    }

    /**
     * Merge two intervals if are consecutives and have the same price
     *
     * @param  Interval $newInterval
     * @param  Interval $actualInterval
     * @return void
     */
    public function mergeIfPossible($newInterval, $actualInterval)
    {
        if ($this->consecutives($newInterval->date_end, $actualInterval->date_start) &&
            $newInterval->price == $actualInterval->price) {
            $newInterval->date_end = $actualInterval->date_end;
            App::get("database")->delete("intervals", $actualInterval->id);
            App::get("database")->update("intervals", $newInterval->getAttributes());
        } elseif ($this->consecutives($actualInterval->date_end, $newInterval->date_start) &&
            $newInterval->price == $actualInterval->price) {
            $newInterval->date_start = $actualInterval->date_start;
            App::get("database")->delete("intervals", $actualInterval->id);
            App::get("database")->update("intervals", $newInterval->getAttributes());
        }
    }

    /**
     * Check if two dates are consecutives
     *
     * @param  string $date1
     * @param  string $date2
     * @return boolean
     */
    public function consecutives($date1, $date2)
    {
        return Carbon::parse($date1)->addDays(+1) == Carbon::parse($date2);
    }

    /**
     * Adjust two interval when exists an overlap
     *
     * @param  Interval $newInterval
     * @param  Interval $actualInterval
     * @return void
     */
    public function adjustIntervalsWhenAreOverlapped($newInterval, $actualInterval)
    {
        if ($this->overlaps($newInterval, $actualInterval)) {
            $this->adjustOverlap($newInterval, $actualInterval);
        }
    }

    /**
     * Check if two intervals overlaps
     *
     * @param  Interval $interval1
     * @param  Interval $interval2
     * @return boolean
     */
    public function overlaps($interval1, $interval2)
    {
        if ($interval1->date_start <= $interval2->date_end && $interval1->date_end >= $interval2->date_start) {
            return true;
        }
        return false;
    }

    /**
     * Adjust two intervals if one of them contains the other
     *
     * @param  Interval $newInterval
     * @param  Interval $actualInterval
     * @return void
     */
    public function adjustIntervalsWhenAreContained($newInterval, $actualInterval)
    {
        if ($this->contains($newInterval, $actualInterval)) {
            App::get("database")->delete("intervals", $actualInterval->id);
        } elseif ($this->contains($actualInterval, $newInterval)) {
            if ($actualInterval->price === $newInterval->price) {
                App::get("database")->delete("intervals", $newInterval->id);
            } else {
                $date_start = $actualInterval->date_start;
                $date_end = $actualInterval->date_end;
                $actualInterval->date_end = Carbon::parse($newInterval->date_start)->addDays(-1);

                $interval = new Interval();
                $interval->date_start = Carbon::parse($newInterval->date_end)->addDays(+1);
                $interval->date_end = Carbon::parse($date_end);
                $interval->price = $actualInterval->price;

                App::get("database")->update("intervals", $actualInterval->getAttributes());
                App::get("database")->save("intervals", $interval->getAttributes());
            }
        }
    }

    /**
     * Check if one interval contains the other
     *
     * @param  Interval $interval1
     * @param  Interval $interval2
     * @return boolean
     */
    public function contains($bigInterval, $lowInterval)
    {
        if ($bigInterval->date_start <= $lowInterval->date_start && $bigInterval->date_end >= $lowInterval->date_end) {
            return true;
        }

        return false;
    }

    /**
    * Adjust the overlap between two intervals, new intervals have precedence over actual intervals.
    *
    * @param $newInterval The new interval
    * @param $actualInterval The actual interval
    */
    public function adjustOverlap($newInterval, $actualInterval)
    {
        if ($actualInterval->date_start > $newInterval->date_start) {
            if ($actualInterval->price === $newInterval->price) {
                $newInterval->date_end = $actualInterval->date_end;
                App::get("database")->delete("intervals", $actualInterval->id);
                App::get("database")->update("intervals", $newInterval->getAttributes());
            } else {
                $actualInterval->date_start = Carbon::parse($newInterval->date_end)->addDays(+1);
                App::get("database")->update("intervals", $actualInterval->getAttributes());
            }
        } else {
            if ($actualInterval->price === $newInterval->price) {
                $newInterval->date_start = $actualInterval->date_start;
                App::get("database")->delete("intervals", $actualInterval->id);
                App::get("database")->update("intervals", $newInterval->getAttributes());
            } else {
                $actualInterval->date_end = Carbon::parse($newInterval->date_start)->addDays(-1);
                App::get("database")->update("intervals", $actualInterval->getAttributes());
            }
        }
    }

    /**
     * Return the interval size
     * @param  Interval $interval
     * @return int
     */
    public function size($interval)
    {
        return strtotime($interval->date_end) - strtotime($interval->date_start);
    }
}
