<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Employee;
use TCG\Voyager\Facades\Voyager;

class TrainingRecords extends Controller
{
    
    public function index()
    {
        $newEmps = Employee::all(); 
        return view('training-records', compact('newEmps'));
    }
    
    public function create()
    {
        $newEmps = Employee::all();
        return view('training-records', compact('newEmps'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'emp_id' => 'required|exists:employees,emp_id',
            'emp_name' => 'required',
            'training_hours' => 'required|numeric',
            'training_date' => 'required|date',
            'expired_date' => 'required|date'
        ]);

        $existingEmp = Employee::where('emp_id', $validatedData['emp_id'])->first();

        if (!$existingEmp) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        // Update employee details
        $existingEmp->fill([
            'emp_name' => $validatedData['emp_name'],
            'training_hours' => $existingEmp->training_hours + $validatedData['training_hours'],
            'training_date' => $validatedData['training_date'],
            'expired_date' => $validatedData['expired_date']
        ]);

        // Determine status based on training hours and trainer/employee type
        if ($existingEmp->trainer_emp == 'emp') {
            if ($existingEmp->training_hours >= 40) {
                $existingEmp->status = 'Complete';
            } else {
                $existingEmp->status = 'Incomplete';
            }
        } elseif ($existingEmp->trainer_emp == 'trainer') {
            if ($existingEmp->training_hours >= 8) {
                $existingEmp->status = 'Complete';
            } else {
                $existingEmp->status = 'Incomplete';
            }
        }

        $existingEmp->save();

        return redirect()->back()->with('success', 'Training record updated successfully.');
    }

}
