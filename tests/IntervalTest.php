<?php
declare(strict_types=1);

require 'core/bootstrap.php';

use App\Models\Interval;
use PHPUnit\Framework\TestCase;

final class IntervalTest extends TestCase
{
    public function cleanDatabase()
    {
        Interval::deleteAll();
    }

    /**
     * @test
     */
    public function an_interval_can_be_saved() :void
    {
        $this->cleanDatabase();

        $interval = new Interval([
            'date_start' => '2019-06-01',
            'date_end' => '2019-06-15',
            'price' => 30
        ]);

        $interval = $interval->save();

        $this->assertIsInt((int) $interval['id']);
    }

     /**
     * @test
     */
    public function an_interval_can_be_deleted() :void
    {
        $this->cleanDatabase();

        $interval = new Interval([
            'date_start' => '2019-06-01',
            'date_end' => '2019-06-15',
            'price' => 30
        ]);

        $interval = $interval->save();

        Interval::delete($interval['id']);

        $data = (new Interval())->all()->order('date_start')->get();

        $this->assertEquals(count($data), 0);
    }

    /**
     * @test
     */
    public function intervals_are_merged_if_is_possible() :void
    {
        $this->cleanDatabase();

        $interval1 = new Interval([
            'date_start' => '2019-06-15',
            'date_end' => '2019-06-20',
            'price' => 30
        ]);

        $interval2 = new Interval([
            'date_start' => '2019-06-21',
            'date_end' => '2019-06-25',
            'price' => 30
        ]);

        $interval1 = $interval1->save();
        $interval2 = new Interval($interval2->save());

        $interval2->checkRanges();

        $data = (new Interval())->all()->order('date_start')->get();

        $this->assertEquals(count($data), 1);
    }

    /**
     * @test
     */
    public function new_interval_have_precendence_over_actual_intervals() :void
    {
        $this->cleanDatabase();

        $interval1 = new Interval([
            'date_start' => '2019-06-15',
            'date_end' => '2019-06-25',
            'price' => 30
        ]);

        $interval2 = new Interval([
            'date_start' => '2019-06-18',
            'date_end' => '2019-06-22',
            'price' => 20
        ]);

        $interval1 = $interval1->save();
        $interval2 = new Interval($interval2->save());

        $interval2->checkRanges();

        //Should contain three intervals
        $data = (new Interval())->all()->order('date_start')->get();

        $this->assertEquals(count($data), 3);
    }

    /**
     * @test
     */
    public function intervals_can_not_be_crossed() :void
    {
        $this->cleanDatabase();

        $interval1 = new Interval([
            'date_start' => '2019-06-15',
            'date_end' => '2019-06-25',
            'price' => 30
        ]);

        $interval2 = new Interval([
            'date_start' => '2019-06-15',
            'date_end' => '2019-06-25',
            'price' => 40
        ]);

        $interval1 = $interval1->save();
        $interval2 = new Interval($interval2->save());

        $interval2->checkRanges();

        $data = (new Interval())->all()->order('date_start')->get();

        $this->assertEquals(count($data), 1);
        $this->assertEquals($data[0]->price, $interval2->price);
    }
}
