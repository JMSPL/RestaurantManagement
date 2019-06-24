<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreateMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\MenuResource;
use App\Menu;
use App\User;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Storage;

class MenusControllerAPI extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    public function index()
    {
        return MenuResource::collection(Menu::all());
    }

    public function store(CreateMenuRequest $request)
    {
        $menu = Menu::create($request->validated());

        return response()->json(new MenuResource($menu), 201);
    }

    public function createPhoto(Request $request){
        $validatedData = $request->validate([
            'file' => 'required|image',
        ]);

        $filename = basename(Storage::disk('public')->putFile('items', $validatedData->file));

        return response()->json($filename, 201);
    }


    public function show($id)
    {
        return new MenuResource(Menu::find($id));
    }

    public function update(UpdateMenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($request->validated());

        return response()->json(new MenuResource($menu), 201);
    }

    public function updatePhoto(Request $request, $id){
        if($request->file != null){
            $menu = Menu::findOrFail($id);
            $filename = basename(Storage::disk('public')->putFile('items', $request->file));

            Storage::disk('public')->delete('items/'.$menu->photo_url);

            $menu->photo_url = $filename;
            $menu->save();

            return response()->json(new MenuResource($menu), 201);
        }
    }

    public function destroy($id)
    {
        try{
            Menu::findOrFail($id)->forceDelete();
        }catch (\Exception $exception){
            Menu::findOrFail($id)->delete();
        }
        return response()->json(null, 204);
    }
}
