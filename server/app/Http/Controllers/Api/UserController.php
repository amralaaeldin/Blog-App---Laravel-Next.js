<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        try {
            return response()->json(
                User::role('user')
                    ->whereNotNull('accepted_at')
                    ->select('id', 'name', 'email')
                    ->withCount('posts')->get()
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function indexPending()
    {
        try {
            return response()->json(
                User::role('user')
                    ->whereNull('accepted_at')
                    ->select('id', 'name', 'email', 'created_at')->get()
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function show($id = null)
    {
        try {
            return response()->json(
                User::role('user')->select('id', 'name', 'email')->findOrFail($id ?? auth()->user()->id)
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\NotFoundException(__('Not found.'));
        }
    }

    public function update(UpdateUserRequest $request, $id = null)
    {
        $user = User::role('user')->where('id', $id ?? auth()->user()->id)->first();
        if (!$user) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $user->update([
                'name' => $request->name ?? $user->name,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
            ]);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => 'User updated successfully',
            ]
        );
    }

    public function accept($id)
    {
        $user = User::role('user')->where('id', $id)->select('id', 'name', 'email', 'accepted_at')->first();
        if (!$user) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        if ($user->accepted_at) {
            throw new \App\Exceptions\BadRequestException(__('User already accepted.'));
        }

        try {
            $user->update([
                'accepted_at' => date("Y-m-d H:i:s"),
            ]);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => 'User accepted successfully',
            ]
        );
    }

    public function destroy($id)
    {
        $user = User::role('user')->where('id', $id)->first();
        if (!$user) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $user->delete();
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
