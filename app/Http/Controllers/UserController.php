<?php

namespace App\Http\Controllers;

use App\Models\worksnapUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($id){
        $startDate = Carbon::now()->startOfWeek()->shiftTimezone('UTC')->timestamp;
        $endDate = Carbon::now()->endOfWeek()->shiftTimezone('UTC')->timestamp;

        $user = worksnapUser::with(['timmings' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('from_timestamp', [$startDate, $endDate]);
        }])->find($id);

        return view('users.show', compact('user'));
    }
}
