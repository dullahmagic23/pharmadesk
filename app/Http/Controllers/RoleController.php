<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return Inertia::render('Roles/Index',[
            'roles' => $roles
        ]);
    }

    public function create()
    {
        return Inertia::render('Roles/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' =>'required|string|max:30|unique:roles,name'
        ]);

        Role::create($data);

        return to_route('roles.index')->with('success','Role added successfully');
    }

    public function edit(Role $role)
    {
        return Inertia::render('Roles/Edit',['role' => $role]);
    }

    public function update(Request $request, Role $role)
    {
        $data =  $request->validate([
            'name' => 'required|string|max:30'
        ]);
        $role->update($data);

        return to_route('roles.index')->with('success','Roles updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return to_route('roles.index')->with('success','Roles removed successfully');
    }
}
