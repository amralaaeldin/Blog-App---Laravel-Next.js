<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        try {
            return response()->json(
                User::select('id', 'name', 'email')
                    ->role('admin')
                    ->get()
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function show($id)
    {
        try {
            return response()->json(User::role('admin')->select('id', 'name', 'email')->findOrFail($id));
        } catch (\Exception $e) {
            throw new \App\Exceptions\NotFoundException(__('Not found.'));
        }
    }

    public function create(StoreAdminRequest $request)
    {
        try {
            $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $admin->assignRole('admin');
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json([
            'admin' => $admin,
            'message' => __('Created successfully.'),
        ], 201);
    }

    public function update(UpdateAdminRequest $request, $id)
    {
        $user = User::role('admin')->where('id', $id)->first();
        if (!$user) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $user->update([
                'name' => $request->name,
                'password' => bcrypt($request->password),
            ]);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Updated successfully.'),
            ]
        );
    }

    public function destroy($id)
    {
        $user = User::role('admin')->where('id', $id)->first();
        if (!$user) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $user->delete();
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json([
            'message' => __('Deleted successfully.'),
        ]);
    }
}
