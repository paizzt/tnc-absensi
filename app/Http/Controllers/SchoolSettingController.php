<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolSetting;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class SchoolSettingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
        } else {
            $selectedSchoolId = $user->school_id;
            if (!$selectedSchoolId) {
                abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun.');
            }
        }

        $setting = null;
        if ($selectedSchoolId) {
            // firstOrCreate: Cari datanya, jika tidak ada, buatkan otomatis dengan nilai default
            $setting = SchoolSetting::firstOrCreate(
                ['school_id' => $selectedSchoolId],
                [
                    'time_in' => '07:00:00',
                    'time_late' => '07:15:00',
                    'time_out' => '15:00:00',
                    'notify_in' => true,
                    'notify_out' => true
                ]
            );
        }

        return view('admin.settings.index', compact('setting', 'schools', 'selectedSchoolId'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'time_in' => 'required',
            'time_late' => 'required',
            'time_out' => 'required',
        ]);

        $setting = SchoolSetting::where('school_id', $request->school_id)->first();
        
        if ($setting) {
            $setting->update([
                'time_in' => $request->time_in,
                'time_late' => $request->time_late,
                'time_out' => $request->time_out,
                'notify_in' => $request->has('notify_in'),
                'notify_out' => $request->has('notify_out'),
            ]);
        }

        return redirect()->route('admin.settings.index', ['school_id' => $request->school_id])
            ->with('success', 'Pengaturan sekolah berhasil diperbarui.');
    }
}