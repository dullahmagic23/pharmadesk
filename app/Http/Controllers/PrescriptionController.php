<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Dosage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PrescriptionController extends Controller
{
    public function index(): Response
    {
        $prescriptions = Prescription::with(['patient', 'doctor'])->latest()->paginate(10);
        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $prescriptions
        ]);
    }

    public function create(): Response
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        $medicines = Medicine::all();
        $dosages = Dosage::all();
        $isDoctor = Auth::user()->hasRole('doctor');

        return Inertia::render('Prescriptions/Create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'medicines' => $medicines,
            'dosages' => $dosages,
            'isDoctor' => $isDoctor
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'prescribed_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'medicines' => 'required|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.dosage_id' => 'nullable|exists:dosages,id',
        ]);

        DB::transaction(function () use ($request) {
            $prescription = Prescription::create([
                'id' => Str::uuid(),
                'patient_id' => $request->patient_id,
                'doctor_id' => Auth::user()->hasRole('doctor') ? Auth::user()->doctor->id : $request->doctor_id,
                'prescribed_at' => $request->prescribed_at ?? now(),
                'notes' => $request->notes,
            ]);

            foreach ($request->medicines as $med) {
                $prescription->medicines()->attach($med['id'], [
                    'id' => Str::uuid(),
                    'dosage_id' => $med['dosage_id'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('prescriptions.index')->with('success', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription): Response
    {
        $prescription->load(['patient', 'doctor', 'medicines', 'medicines.dosages']);
        return Inertia::render('Prescriptions/Show', [
            'prescription' => $prescription
        ]);
    }

    public function edit(Prescription $prescription): Response
    {
        // Optional implementation
        return Inertia::render('Prescriptions/Edit', [
            'prescription' => $prescription
        ]);
    }

    public function update(Request $request, Prescription $prescription)
    {
        // Optional implementation
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return Inertia::location(url()->previous());
    }

    public function download(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'medicines.dosages']);

        $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription'));
        return $pdf->stream('prescription_' . $prescription->id . '.pdf');
    }
}
