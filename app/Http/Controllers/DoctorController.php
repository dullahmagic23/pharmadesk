<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function index()
    {
        return Inertia::render('Doctors/Index', [
            'doctors' => Doctor::orderBy('first_name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Doctors/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'nullable|string|max:20',
            'gender'         => 'nullable|string|in:male,female',
            'date_of_birth'  => 'nullable|date',
            'specialization' => 'nullable|string|max:255',
            'address'        => 'nullable|string',
        ]);

        $user = User::create([
            'name'     => $validated['first_name'] . ' ' . $validated['last_name'],
            'email'    => $validated['email'],
            'password' => bcrypt('password'),
        ]);


        $user->assignRole('doctor');


        Doctor::create(array_merge($validated, [
            'user_id' => $user->id,
        ]));

        return redirect()->route('doctors.index')->with('success', 'Doctor and user account created successfully.');
    }

    public function show(Doctor $doctor)
    {
        return Inertia::render('Doctors/Show', [
            'doctor' => $doctor,
        ]);
    }

    public function edit(Doctor $doctor)
    {
        return Inertia::render('Doctors/Edit', [
            'doctor' => $doctor,
        ]);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone'          => 'nullable|string|max:20',
            'gender'         => 'nullable|string|in:male,female',
            'date_of_birth'  => 'nullable|date',
            'specialization' => 'nullable|string|max:255',
            'address'        => 'nullable|string',
        ]);

        $doctor->update($validated);

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}

