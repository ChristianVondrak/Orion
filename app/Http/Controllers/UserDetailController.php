<?php

namespace App\Http\Controllers;

use App\Enums\Country;
use App\Models\worksnapUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class UserDetailController extends Controller
{
    public function store(Request $request, worksnapUser $user)
    {
        $data = $request->validate([
            'country'        => ['nullable', new Enum(Country::class)],
            'phone'          => 'nullable|string|max:50',
            'position'       => 'nullable|string|max:100',
            'gender'         => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'date_of_birth'  => 'nullable|date',
        ]);

        $data['user_id'] = $user->id;
        $user->detail()->create($data);

        return redirect()
            ->route('user.show', $user->id)
            ->with('success', 'Details added.');
    }

    public function update(Request $request, worksnapUser $user)
    {
        $data = $request->validate([
            'country'        => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'position'       => 'nullable|string|max:100',
            'gender'         => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'date_of_birth'  => 'nullable|date',
        ]);

        $user->detail()->update($data);

        return redirect()
            ->route('user.show', $user->id)
            ->with('success', 'Details updated.');
    }
}
