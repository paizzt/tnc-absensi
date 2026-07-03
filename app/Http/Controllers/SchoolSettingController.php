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
            if (!$selectedSchoolId) abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun.');
        }

        $setting = null;
        if ($selectedSchoolId) {
            $setting = SchoolSetting::firstOrCreate(
                ['school_id' => $selectedSchoolId],
                [
                    'time_in' => '07:00:00',
                    'time_late' => '07:15:00',
                    'time_out' => '15:00:00',
                    'notify_in' => true,
                    'notify_out' => true,
                    'lesson_duration' => 45,
                    'break_duration' => 30,
                    'break_after_lesson' => 4,
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
            'lesson_duration' => 'required|integer|min:15|max:120',
            'break_duration' => 'required|integer|min:5|max:60',
            'break_after_lesson' => 'required|integer|min:1|max:8',
        ]);

        $setting = SchoolSetting::where('school_id', $request->school_id)->first();
        
        if ($setting) {
            $setting->update([
                'time_in' => $request->time_in,
                'time_late' => $request->time_late,
                'time_out' => $request->time_out,
                'notify_in' => $request->has('notify_in'),
                'notify_out' => $request->has('notify_out'),
                'lesson_duration' => $request->lesson_duration,
                'break_duration' => $request->break_duration,
                'break_after_lesson' => $request->break_after_lesson,
                'fonnte_token' => $request->fonnte_token,
            ]);
        }

        return redirect()->route('admin.settings.index', ['school_id' => $request->school_id])
            ->with('success', 'Pengaturan sekolah berhasil diperbarui.');
    }
}