<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Auth;

class SchoolSettingController extends Controller
{
    public function index()
    {
        // Kunci data: Hanya ambil pengaturan milik sekolah user yang sedang login
        $setting = SchoolSetting::where('school_id', Auth::user()->school_id)->firstOrFail();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = SchoolSetting::where('school_id', Auth::user()->school_id)->firstOrFail();

        $validated = $request->validate([
            'timezone' => 'required|string',
            'time_in' => 'required',
            'time_late' => 'required',
            'time_out' => 'required',
            'late_light_max' => 'required|integer|min:1',
            'late_medium_max' => 'required|integer|min:1',
        ]);

        $setting->update($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Konfigurasi operasional sekolah berhasil diperbarui.');
    }
}