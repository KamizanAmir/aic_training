<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingPlan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Carbon;

class TrainingScheduleController extends Controller
{
    public function index()
    {
        $trainingPlans = TrainingPlan::all();
        return view('training-schedule', compact('trainingPlans'));
    }
    public function showQr($id)
    {
        $trainingPlan = TrainingPlan::findOrFail($id);
        $details = http_build_query([
            'id' => $trainingPlan->id,
            'training_hours' => $trainingPlan->training_hours,
            'training_date' => $trainingPlan->training_date,
            'expired_date' => $trainingPlan->expired_date
        ]);
        
        // If you're encoding a URL, make sure the route and its handling method can process these parameters.
        $url = route('training.record.form', $id) . "?" . $details;
        $qrCode = QrCode::size(650)->generate($url);
    
        return view('show_qr', compact('qrCode'));
    }

}
