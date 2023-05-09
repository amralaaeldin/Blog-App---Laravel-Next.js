<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            User::doesntHave('roles')
                ->whereNotNull('accepted')
                ->withCount('posts')
                ->select('id', 'name', 'email')->get()
        );
    }

    public function indexPending()
    {
        return response()->json(
            User::doesntHave('roles')
                ->whereNull('accepted')
                ->select('id', 'name', 'email')->get()
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(
            User::where('id', $id)->select('id', 'name', 'email')->get()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        return response()->json(
            User::where('id', $id)->update([
                'name' => $request->name,
                'password' => bcrypt($request->password),
            ])
        );
    }

    public function accept($id)
    {
        $user = User::where('id', $id)->select('id', 'name', 'email', 'accepted')->first();
        if ($user->accepted) {
            return response()->json([
                'message' => 'User already accepted',
            ], 400);
        }

        return response()->json(
            [
                'user' => $user->update([
                    'accepted' => true,
                ]),
                'message' => 'User accepted successfully',
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return response()->json(User::where('id', $id)->delete());
    }
}
