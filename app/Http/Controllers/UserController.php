<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::with('roles')->latest()->get()
        ]);
    }


    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($validated['role']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->attach($role->id);

        return redirect()->back()->with('success', 'User registered successfully.');
    }


    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user->load('roles'),
            'roles' => Role::all()
        ]);
    }
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,id',
            'is_active' => 'required'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active
        ]);

        $user->syncRoles(Role::findById($request->role)->name);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }



    public function userActivity(Request $request)
    {
        $query = DB::table('activity_log')
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->select('activity_log.*', 'users.name as user_name');

        if ($request->filled('user')) {
            $query->where('users.id', $request->user);
        }

        $activities = $query->orderByDesc('activity_log.created_at')->get();

        // Decode properties for each activity
        $activities->transform(function ($activity) {
            $activity->properties = json_decode($activity->properties ?? '{}');
            return $activity;
        });

        $users = DB::table('users')->select('id', 'name')->get();

        return Inertia::render('Users/Activities', [
            'activities' => $activities,
            'users' => $users,
            'filters' => $request->only('user'),
        ]);
    }


    public function show(User $user)
    {

    }

}
