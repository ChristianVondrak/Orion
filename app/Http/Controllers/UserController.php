<?php

namespace App\Http\Controllers;

use App\Models\worksnapUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request, $id){
        // variables to filter between 2 dates
        $startDate = Carbon::now()->startOfWeek()->shiftTimezone('UTC')->timestamp;
        $endDate = Carbon::now()->endOfWeek()->shiftTimezone('UTC')->timestamp;

        // If the filter comes by request, the given one is used
        if ($request->has('start') && $request->has('end')) {
            $startDate = Carbon::createFromFormat('Y/m/d', $request->input('start'))
                ->startOfDay()
                ->shiftTimezone('UTC')
                ->timestamp;
            $endDate = Carbon::createFromFormat('Y/m/d', $request->input('end'))
                ->endOfDay()
                ->shiftTimezone('UTC')
                ->timestamp;
        }

        //search for a user by their id
        $user = worksnapUser::find($id);

        if ($user) {
            $perPage = 10;
            //search for timings associated with a user between 2 dates
            $timmings = $user->timmings()
                ->whereBetween('from_timestamp', [$startDate, $endDate])
                ->where('user_id', $id)
                //Add start and end parameters to pagination links to maintain these filters during page navigation.
                ->get();

            // Agrupar los timmings por día y sumar los tiempos
            $timmingsByDay = $timmings->groupBy(function($item) {
                return Carbon::createFromTimestamp($item->from_timestamp)->format('Y-m-d');
            })->map(function($dayGroup) {
                return $dayGroup->count() * 10 * 60; // 10 minutos en segundos
            });

            return view('users.show', compact('user', 'timmings', 'timmingsByDay'));
        } else {
            // Handle user not found scenario
            return abort(404);
        }
    }
}
