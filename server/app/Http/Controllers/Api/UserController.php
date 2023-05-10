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
                ->whereNotNull('accepted_at')
                ->select('id', 'name', 'email')
                ->withCount('posts')->get()
        );
    }

    public function indexPending()
    {
        return response()->json(
            User::doesntHave('roles')
                ->whereNull('accepted_at')
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

        User::where('id', $id)->update([
            'name' => $request->name,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(
            [
                'message' => 'User updated successfully',
            ]
        );
    }

    public function accept($id)
    {
        $user = User::where('id', $id)->select('id', 'name', 'email', 'accepted_at')->first();
        if ($user->accepted_at) {
            return response()->json([
                'message' => 'User already accepted',
            ], 400);
        }

        $user->update([
            'accepted_at' => date("Y-m-d H:i:s"),
        ]);

        return response()->json(
            [
                'message' => 'User accepted successfully',
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
