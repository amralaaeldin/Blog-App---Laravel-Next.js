<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return response()->json(
            User::select('id', 'name', 'email')
                ->role('admin')
                ->get()
        );
    }

    public function show($id)
    {
        return response()->json(
            User::where('id', $id)->select('id', 'name', 'email')->get()
        );
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $admin->assignRole('admin');

        return response()->json([
            'admin' => $admin,
            'message' => 'Admin created successfully',
        ]);
    }

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
                'message' => 'Admin updated successfully',
            ]
        );
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return response()->json([
            'message' => 'Admin deleted successfully',
        ]);
    }
}
