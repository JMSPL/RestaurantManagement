<?php

namespace App\Policies;

use App\User;
use App\Worker;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkerPolicy
{
    use HandlesAuthorization;

    public function block(User $worker, Worker $workerToBlock){
        return $worker->id !== $workerToBlock->id;
    }

    public function unblock(User $worker, Worker $workerToUnblock){
        return $worker->id !== $workerToUnblock->id;
    }

    public function destroy(User $worker, Worker $workerToDelete){
        return $worker->id !== $workerToDelete->id;
    }

    public function updatePassword(User $user, Worker $worker){
        return $user->id === $worker->id;
    }

    public function statistics(User $user, Worker $worker){
        return $worker->id == $user->id && $worker->type === 'manager';
    }

    public function change(User $worker, Worker $workerToBlock){
        return $worker->id !== $workerToBlock->id;
    }
}
