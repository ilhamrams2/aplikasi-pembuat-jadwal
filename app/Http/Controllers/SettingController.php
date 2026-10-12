<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SchoolSetting::pluck('value', 'key')->toArray();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'school_name', 'school_address', 'academic_year', 'semester',
            'principal_name', 'principal_nip', 'waka_kurikulum'
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                SchoolSetting::set($field, $request->input($field));
            }
        }

        return back()->with('success', 'Pengaturan madrasah berhasil disimpan.');
    }
}
