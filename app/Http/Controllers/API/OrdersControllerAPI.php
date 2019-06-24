<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\MealResource;
use App\Http\Resources\OrderResource;
use App\Invoice;
use App\Meal;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrdersControllerAPI extends Controller
{

    public function index(Request $request){
        $state = $request->state == null ? ["pending", "confirmed"] : str_split($request->state, strlen($request->state));
        if ($request->sort) {
            $sortBy = explode('|', $request->sort);
            $query = Order::whereIn('state', $state)->with("orders.item")->with('waiter')->orderBy($sortBy[0], $sortBy[1]);
        }else{
            $query = Order::whereIn('state', $state)->with('waiter')->with("orders.item");
        }

        if($request->waiter){
            $query = $query->where("responsible_waiter_id", $request->waiter);
        }

        if($request->date){
            $query = $query->whereDate("start", date('Y-m-d', strtotime($request->date)));
        }
        return $query->paginate($request->per_page);
    }

    function store(Request $request)
    {

        $validatedData = $request->validated();
        $order = Order::create($validatedData);

        return response()->json(new OrderResource($order), 201);
    }

    public function show($id)
    {
        return new OrderResource(Order::find($id));

    }

    public function update(Request $request, $id)
    {
        $meal = Order::findOrFail($id);
        $meal->update($request->validated());

        return response()->json(new OrderResource($meal), 201);
    }


    public function destroy($id)
    {
        try{
            Order::findOrFail($id)->forceDelete();
        }catch (\Exception $exception){
            Order::findOrFail($id)->delete();
        }
        return response()->json(null, 204);
    }
}
