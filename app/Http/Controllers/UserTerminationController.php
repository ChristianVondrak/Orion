<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserTerminationRequest;
use App\Models\UserTermination;
use App\Models\worksnapUser;
use Illuminate\Http\RedirectResponse;

class UserTerminationController extends Controller
{
    /**
     * Store a new UserTermination record.
     */
    public function store(StoreUserTerminationRequest $request, $userId): RedirectResponse
    {
        $user = worksnapUser::findOrFail($userId);

        // Crear la baja
        UserTermination::create([
            'user_id'           => $user->id,
            'termination_date'  => $request->input('termination_date'),
            'reason'            => $request->input('reason'),
        ]);

        return back()->with('success', 'User has been terminated successfully.');
    }
}

