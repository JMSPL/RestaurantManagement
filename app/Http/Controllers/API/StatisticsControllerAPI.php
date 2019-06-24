<?php

namespace App\Http\Controllers\API;

use App\Meal;
use App\Worker;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatisticsControllerAPI extends Controller
{
    public function getWaiterStatistics(Request $request){
        $worker = Worker::findOrFail($request->user()->id);
        $this->authorize('statistics', $worker);
        $query = "
        SELECT u.name as waiter, meals_per_day, orders_per_day
        FROM users u
        JOIN (
              (SELECT waiter, AVG(contador) as meals_per_day
              FROM (
                SELECT responsible_waiter_id as waiter, COUNT(*) as contador FROM meals
                WHERE responsible_waiter_id IN (
                  SELECT id FROM users WHERE type = 'waiter')
                GROUP BY responsible_waiter_id, DATE_FORMAT(end, '%Y-%m-%d')) as t
              GROUP BY waiter) as t
            ) ON waiter = u.id
        JOIN (
          (SELECT waiter2, AVG(contador) as orders_per_day
              FROM (
                SELECT m.responsible_waiter_id as waiter2, COUNT(*) as contador
                FROM orders o
                JOIN meals m ON o.meal_id = m.id
                GROUP BY m.responsible_waiter_id, DATE_FORMAT(m.end, '%Y-%m-%d')) as l
              GROUP BY waiter2) as orders
        ) ON waiter2 = u.id";

        return response()->json(DB::select(DB::raw($query)));
    }

    public function getCookStatistics(Request $request){
        $worker = Worker::findOrFail($request->user()->id);
        $this->authorize('statistics', $worker);
        $query = "
          SELECT name as cook, orders_per_day
          FROM users
          JOIN ((
              SELECT cook, AVG(contador) as orders_per_day
              FROM ((
                SELECT responsible_cook_id as cook, count(*) as contador
                FROM orders
                GROUP BY responsible_cook_id, DATE_FORMAT(end, '%Y-%m-%d')) as t)
              GROUP BY cook) as o) ON id = cook";

        return response()->json(DB::select(DB::raw($query)));
    }
}
