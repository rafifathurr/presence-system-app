<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Presence\Presence;
use App\Models\User;
use App\Models\Warrant\Warrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Return Home Page Display.
     */
    public function home()
    {
        /**
         * Validation Current User Has Role Access
         */
        if (User::find(Auth::user()->id)->hasRole('staff')) {
            /**
             * Warrant Check User
             */
            $warrant_check = Warrant::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('date_start', '<=', date('Y-m-d'))
                ->where('date_finish', '>=', date('Y-m-d'))
                ->whereHas('warrantUser', function ($query) {
                    $query->whereNull('deleted_at')->where('user_id', Auth::user()->id);
                })
                ->first();

            /**
             * Validation Warrant Has Current User
             */
            if (!is_null($warrant_check)) {
                /**
                 * Presence Check User
                 */
                $current_presence_check = Presence::whereNull('deleted_by')
                    ->whereNull('deleted_at')
                    ->whereDate('created_at', date('Y-m-d'))
                    ->where('created_by', Auth::user()->id)
                    ->first();

                /**
                 * Validation Presence Has Current User
                 */
                if (is_null($current_presence_check)) {
                    $data['presence'] = true;
                } else {
                    $data['presence'] = false;
                }
            } else {
                $data['presence'] = false;
            }
        } else {
            $data['presence'] = false;
        }
        return view('home', $data);
    }
}
