<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreateWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Http\Resources\WorkerResource;
use App\Mail\SetPassword;
use App\PasswordReset;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class WorkersControllerAPI extends Controller
{

    use SendsPasswordResetEmails;

    public function index(Request $request)
    {
        if ($request->sort) {
            $sortBy = explode('|', $request->sort);
            return Worker::orderBy($sortBy[0], $sortBy[1])->paginate($request->per_page);
        }
        return Worker::paginate($request->per_page);
    }

    public function show($id){
        return new WorkerResource(Worker::find($id));
    }

    public function store(CreateWorkerRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData["password"] = Hash::make(Carbon::now()->getTimestamp());
        $worker = Worker::create($validatedData);
        Mail::to($worker)->send(new SetPassword($worker,str_random(60)));
        return response()->json(new WorkerResource($worker),201);
    }

    public function storePhoto(Request $request){
        $validatedData = $request->validate([
            'file' => 'required|image',
        ]);

        $filename = basename(Storage::disk('public')->putFile('profiles', $validatedData["file"]));

        return response()->json($filename, 201);
    }

    public function update(UpdateWorkerRequest $request, $id)
    {
        $worker = Worker::findOrFail($id);
        $worker->update($request->validated());
        return response(new WorkerResource($worker),201);
    }

    public function updatePhoto(Request $request, $id){
        if($request->file != null){
            $worker = Worker::findOrFail($id);
            $filename = basename(Storage::disk('public')->putFile('profiles', $request->file));

            Storage::disk('public')->delete('profiles/'.$worker->photo_url);

            $worker->photo_url = $filename;
            $worker->save();

            return response()->json(new WorkerResource($worker), 201);
        }
    }

    public function password(Request $request, $token){
        $reset = PasswordReset::where('token', $token)->first();
        if($reset != null){
            $worker = Worker::where('email', $reset->email)->first();
            $worker->update(['password' => Hash::make($request->password),'email_verified_at' => Carbon::now()->toDateTimeString()]);
            PasswordReset::where('email', $worker->email)->delete();
            return response()->json($worker, 201);
        }
        return response()->json("Request unavailable!", 404);
    }

    public function checkPassword(Request $request){
        if(Hash::check($request->password,$request->user()->password)){
            return response()->json("success", 200);
        };
        return response()->json("Wrong old password!", 500);
    }

    public function updatePassword(Request $request, $id){
        $worker = Worker::findOrFail($id);
        $this->authorize('updatePassword', $worker);
        $worker->update(['password' => Hash::make($request->password)]);
        return response()->json($worker, 201);
    }

    public function block($id){
        $worker = Worker::findOrfail($id);
        $this->authorize('block', $worker);

        $worker->blocked = 1;
        $worker->save();

        return response()->json($worker,200);
    }

    public function unblock($id){
        $worker = Worker::findOrfail($id);
        $this->authorize('unblock', $worker);

        $worker->blocked = 0;
        $worker->save();

        return response()->json($worker,200);
    }

    public function waiters(){
        return new WorkerResource(Worker::where("type", "waiter")->get());
    }

    public function startShift(Worker $worker){
        $worker->update(['last_shift_start' => Carbon::now()->toDateTimeString(), 'shift_active' => 1]);
        return response()->json($worker, 200);
    }

    public function endShift(Worker $worker){
        $worker->update(['last_shift_end' => Carbon::now()->toDateTimeString(), 'shift_active' => 0]);
        return response()->json($worker, 200);
    }

    public function destroy($id)
    {
        $worker = Worker::findOrFail($id);
        $this->authorize('destroy', $worker);
        try{
            $worker->forceDelete();
            Storage::disk('public')->delete('profiles/'.$worker->photo_url);
            $reset = PasswordReset::findOrFail($worker->email);
            $reset->delete();
        }catch (\Exception $ex){
            $worker->delete();
        }
    }

    public function changeStatus($id){
        $worker = Worker::findOrfail($id);
        $this->authorize('change', $worker);

        if($worker->intern == 0)
            $worker->intern=1;
        else
            $worker->intern=0;
        $worker->save();

        return response()->json($worker,200);
    }
}
