<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::all()->groupBy('kategori');
        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $settings = $request->except('_token', 'logo_sekolah');

        foreach ($settings as $key => $value) {
            Pengaturan::where('kunci', $key)->update(['nilai' => $value]);
        }

        // Handle Logo Upload
        if ($request->hasFile('logo_sekolah')) {
            $logo = Pengaturan::where('kunci', 'logo_sekolah')->first();
            
            // Delete old logo if exists
            if ($logo && $logo->nilai) {
                Storage::disk('public')->delete($logo->nilai);
            }

            $path = $request->file('logo_sekolah')->store('assets/logo', 'public');
            Pengaturan::where('kunci', 'logo_sekolah')->update(['nilai' => $path]);
        }

        return redirect()->route('admin.pengaturan.index')->with('success', 'Pengaturan sistem berhasil diperbarui');
    }
}
