<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    //
    public function trainingPlanChartData() {
        $monthlyCounts = DB::table('training_plans')
        ->select(DB::raw('MONTH(training_date) as month'), DB::raw('count(*) as count'))
        ->groupBy('month')
        ->get();
        
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $data = array_fill(0, 12, 0); // Initialize array to hold counts for each month
    
        foreach ($monthlyCounts as $monthlyCount) {
            // Adjust month index by -1 since arrays are 0-indexed but months are 1-indexed
            $data[$monthlyCount->month - 1] = $monthlyCount->count;
        }
    
        return response()->json([
            'months' => $months,
            'data' => $data,
        ]);
    }
    public function completionChartData() {
        // Query to count 'complete' and 'incomplete' statuses
        $statusCounts = DB::table('employees')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Prepare data for the chart
        $data = [
            'Complete' => 0,
            'Incomplete' => 0,
        ];

        // Loop through the results and assign counts to the respective statuses
        foreach ($statusCounts as $statusCount) {
            if ($statusCount->status === 'Complete') {
                $data['Complete'] = $statusCount->total;
            } elseif ($statusCount->status === 'Incomplete') {
                $data['Incomplete'] = $statusCount->total;
            }
        }

        // Prepare the labels and the counts for the chart
        $labels = array_keys($data);
        $counts = array_values($data);

        return response()->json([
            'labels' => $labels, // 'Complete' and 'Incomplete'
            'data' => $counts,   // Totals for each status
        ]);
    }

    public function currentProgressData(Request $request) {
        $sortColumn = $request->input('sort', 'training_hours');
        $sortDirection = $request->input('direction', 'desc');
    
        if (!in_array($sortColumn, ['emp_id', 'emp_name', 'trainer_emp', 'department', 'training_date', 'expired_date', 'training_hours','status', 'created_at'])) {
            $sortColumn = 'training_hours';
        }
    
        $sortDirection = $sortDirection === 'asc' ? 'asc' : 'desc';
    
        $employees = DB::table('employees')
            ->select('emp_id', 'emp_name', 'trainer_emp', 'department', 'training_date', 'expired_date', 'training_hours', 'status', 'created_at')
            ->where('trainer_emp', '=', 'emp') // Add this line to filter only employees
            ->orderBy($sortColumn, $sortDirection)
            ->get();
    
        $progressData = $employees->map(function ($employee) {
            return [
                'emp_id' => $employee->emp_id,
                'emp_name' => $employee->emp_name,
                'trainer_emp' => $employee->trainer_emp,
                'department' => $employee->department,
                'training_date' => $employee->training_date,
                'expired_date' => $employee->expired_date,
                'training_hours' => $employee->training_hours,
                'status' => $employee->status,
                'color' => $employee->training_hours >= 40 ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)'
            ];
        });
    
        return response()->json($progressData);
    }
    public function currentProgressDataTrainer(Request $request) {
        $sortColumn = $request->input('sort', 'training_hours');
        $sortDirection = $request->input('direction', 'desc');
    
        if (!in_array($sortColumn, ['emp_id', 'emp_name', 'trainer_emp', 'department', 'training_date', 'expired_date', 'training_hours', 'status', 'created_at'])) {
            $sortColumn = 'training_hours';
        }
    
        $sortDirection = $sortDirection === 'asc' ? 'asc' : 'desc';
    
        $employees = DB::table('employees')
            ->select('emp_id', 'emp_name', 'trainer_emp', 'department', 'training_date', 'expired_date', 'training_hours', 'status', 'created_at')
            ->where('trainer_emp', '=', 'trainer') // Add this line to filter only employees
            ->orderBy($sortColumn, $sortDirection)
            ->get();
    
        $progressData = $employees->map(function ($employee) {
            return [
                'emp_id' => $employee->emp_id,
                'emp_name' => $employee->emp_name,
                'trainer_emp' => $employee->trainer_emp,
                'department' => $employee->department,
                'training_date' => $employee->training_date,
                'expired_date' => $employee->expired_date,
                'training_hours' => $employee->training_hours,
                'status' => $employee->status,
                'color' => $employee->training_hours >= 40 ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)'
            ];
        });
    
        return response()->json($progressData);
    }       
}
