<?php

namespace App\Http\Controllers;

use App\Models\worksnapUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request,$id){
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

        //user with their timings between 2 dates
        $user = worksnapUser::find($id);

        if ($user) {
            $perPage = 10; // Ajusta la cantidad de elementos por página según sea necesario
            $timmings = $user->timmings()
                ->whereBetween('from_timestamp', [$startDate, $endDate])
                ->where('user_id', $id)
                ->paginate($perPage)->appends([
                    'start' => $startDate,
                    'end' => $endDate,
                ]);

            // Pass paginated timings to the view
            return view('users.show', compact('user', 'timmings'));
        } else {
            // Handle user not found scenario
            return abort(404);
        }
    }
}
