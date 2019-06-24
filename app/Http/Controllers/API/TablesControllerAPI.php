<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\MealResource;
use App\Http\Resources\TableResource;
use App\Meal;
use App\Table;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TablesControllerAPI extends Controller
{
    public function index()
    {
        return new TableResource(Table::orderBy('table_number', 'asc')->get());
    }

    public function availableTables(){
        $tables= Meal::where('state','active')->pluck('table_number')->toArray();
        $availableTables = Table::whereNotIn('table_number',$tables)->get();
        return new TableResource($availableTables);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'table_number' => 'required|numeric'
        ]);

        $table = null;

        try{
            $table = Table::create($validatedData);
        }catch(\Exception $ex){
            return response()->json("This table already exists!", 500);
        }
        return new TableResource($table);
    }

    public function show($id){
        return new TableResource(Table::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'table_number' => 'required|numeric'
        ]);

        $table = Table::findOrFail($id);

        try{
            $table->update($validatedData);
        }catch (\Exception $ex){
            return response()->json("This table cannot be updated or table is already taken!", 500);
        }

        return new TableResource($table);
    }

    public function destroy($id)
    {
        try{
            Table::findOrFail($id)->forceDelete();
        }catch (\Exception $exception){
            Table::findOrFail($id)->delete();
        }

        return response()->json(null,204);
    }
}
