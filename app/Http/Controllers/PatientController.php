<?php
namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index()
    {
        return Inertia::render('Patients/Index', [
            'patients' => Patient::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Patients/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'gender'     => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|unique:patients,email',
            'address'    => 'nullable|string',
        ]);

        Patient::create($validated);

        return redirect()->route('patients.index')->with('success', 'Patient created successfully.');
    }

    public function show(Patient $patient)
    {
        return Inertia::render('Patients/Show', [
            'patient' => $patient
        ]);
    }

    public function edit(Patient $patient)
    {
        return Inertia::render('Patients/Edit', [
            'patient' => $patient
        ]);
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'gender'     => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|unique:patients,email,' . $patient->id,
            'address'    => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Patient deleted.');
    }
}
