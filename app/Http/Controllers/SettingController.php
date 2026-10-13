<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

        // Image uploads (Tanda Tangan & Stempel)
        $imageFields = ['principal_signature', 'school_stamp', 'academic_head_signature'];
        foreach ($imageFields as $imgField) {
            // Check removal request
            if ($request->boolean("remove_{$imgField}")) {
                $oldPath = SchoolSetting::get($imgField);
                if ($oldPath && File::exists(public_path($oldPath))) {
                    File::delete(public_path($oldPath));
                }
                SchoolSetting::set($imgField, '');
            } elseif ($request->hasFile($imgField)) {
                $request->validate([
                    $imgField => 'image|mimes:jpeg,png,jpg,webp,svg|max:3072',
                ]);

                $file = $request->file($imgField);
                $ext = $file->getClientOriginalExtension();
                $filename = $imgField . '_' . time() . '.' . $ext;
                $destinationPath = public_path('uploads/signatures');
                
                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                // Delete old file if exists
                $oldPath = SchoolSetting::get($imgField);
                if ($oldPath && File::exists(public_path($oldPath))) {
                    File::delete(public_path($oldPath));
                }

                $file->move($destinationPath, $filename);
                SchoolSetting::set($imgField, 'uploads/signatures/' . $filename);
            }
        }

        return back()->with('success', 'Pengaturan madrasah & gambar tanda tangan/stempel berhasil disimpan.');
    }
}
