<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreateMealRequest;
use App\Http\Resources\MealResource;
use App\Invoice;
use App\Meal;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MealsControllerAPI extends Controller
{

    public function index(Request $request){
        $state = $request->state == null ? ["active", "terminated"] : str_split($request->state, strlen($request->state));

        if ($request->sort) {
            $sortBy = explode('|', $request->sort);
                $query = Meal::whereIn('state', $state)->with("orders.item")->with('waiter')->orderBy($sortBy[0], $sortBy[1]);
        }else{
            $query = Meal::whereIn('state', $state)->with('waiter')->with("orders.item");
        }

        if($request->waiter){
            $query = $query->where("responsible_waiter_id", $request->waiter);
        }

        if($request->date){
            $query = $query->whereDate("start", date('Y-m-d', strtotime($request->date)));
        }
        return $query->paginate($request->per_page);
    }

    function store(CreateMealRequest $request)
    {

        $validatedData = $request->validated();
        $validatedData["start"] = Carbon::now()->toDateTimeString();
        $meal = Meal::create($validatedData);

        return response()->json(new MealResource($meal), 201);
    }

    public function show($id)
    {
        return new MealResource(Meal::find($id));

    }

    public function update(Request $request, $id)
    {
        $meal = Meal::findOrFail($id);
        $meal->update($request->validated());

        return response()->json(new MealResource($meal), 201);
    }

    public function notPaid($id){
        $meal = Meal::findOrFail($id);
        $this->authorize('terminated', $meal);

        $meal->update(['state' => 'not paid']);
        Invoice::where('meal_id', $meal->id)->update(['state' => 'not paid']);
        Order::where('meal_id', $meal->id)->where('state', '!=', 'delivered')->update(['state', 'not delivered']);

        return new MealResource($meal);
    }

    public function paid($id){

    }

    public function destroy($id)
    {
        try{
            Meal::findOrFail($id)->forceDelete();
        }catch (\Exception $exception){
            Meal::findOrFail($id)->delete();
        }
        return response()->json(null, 204);
    }
}
