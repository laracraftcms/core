<?php

namespace Laracraft\Core\Http\Controllers;

use Laracraft\Core\Entities\EditLock;
use Auth;
use Redirect;
use Request;

class EditLockController extends Controller
{

    /**
     * Maintain an existing lock by extending its expiry
     *
     * @param EditLock $lock
     * @return \Illuminate\Http\JsonResponse
     */
    public function maintain(EditLock $lock){

        $this->authorize($lock);

        if($lock->incrementExpiry()){
            $lock->save();
        };

        return response()->json(['Lock valid until ' . $lock->expires_at ]);
    }

    /**
     * Release a lock
     *
     * @param $lock
     * @return \Illuminate\Http\Response
     */
    public function release(EditLock $lock)
    {
        $this->authorize($lock);

        $lock->delete();
        if(Request::has('redirect_to')){
            return Redirect::to(Request::get('redirect_to'));
        }else {
            return response()->json('Lock released');
        }
    }

    /**
     * Release all existing locks for a given model or globally.
     */
    public function releaseAll()
    {
        $type = Request::get('lockable_type');

        if(!empty($type) && class_exists($type)){
            $this->authorize('manageLocks',new $type);
        }else {
            $this->authorize('manageLocks');
        }

        if(!empty($type) && class_exists($type)){
            EditLock::where('lockable_type',$type)->delete();
        }else{
            /*
             * Not truncate as we don't want to reset auto increments as old locks attempting to
             * renew could potentially be confused with new
             */
            EditLock::query()->delete();
        }
    }

    /**
     * Take over a lock with the current authenticated user.
     *
     * @param EditLock $lock
     */
    public function takeover(EditLock $lock){

        $this->authorize($lock);

        $lock->created_by = Auth::user();

        $lock->incrementExpiry();

        $lock->save();

    }

}
