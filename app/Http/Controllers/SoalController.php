<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\BankSoal;
use App\Models\SoalOpsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SoalController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bankSoalId = $request->input('bank_soal_id');
        $bankSoal = BankSoal::findOrFail($bankSoalId);
        
        return view('pembelajaran.cbt.soal.create', compact('bankSoal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_soal_id' => 'required|exists:bank_soal,id',
            'tipe_soal' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,menjodohkan,isian_singkat,uraian',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
            'pertanyaan' => 'required|string',
            'bobot' => 'required|numeric|min:0',
            'file_media' => 'nullable|file|mimes:jpg,jpeg,png,mp3,wav,mp4,webm|max:10240', // Max 10MB
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;
            
            // Handle Media
            if ($request->hasFile('file_media')) {
                $file = $request->file('file_media');
                $mime = $file->getMimeType();
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/soal-media', $filename);
                
                // Determine Type
                if (str_starts_with($mime, 'image/')) {
                    $data['tipe_media'] = 'image';
                    $data['gambar'] = 'soal-media/' . $filename; // Just path after public/
                } elseif (str_starts_with($mime, 'audio/')) {
                    $data['tipe_media'] = 'audio';
                    $data['audio'] = 'soal-media/' . $filename;
                } elseif (str_starts_with($mime, 'video/')) {
                    $data['tipe_media'] = 'video';
                    $data['video'] = 'soal-media/' . $filename;
                }
            }

            if ($validated['tipe_soal'] == 'pilihan_ganda') {
                 $data['opsi_a'] = $request->opsi_a;
                 $data['opsi_b'] = $request->opsi_b;
                 $data['opsi_c'] = $request->opsi_c;
                 $data['opsi_d'] = $request->opsi_d;
                 $data['opsi_e'] = $request->opsi_e;
                 $data['kunci_jawaban'] = $request->kunci_jawaban; 
            }
            
            if ($validated['tipe_soal'] == 'isian_singkat') {
                $data['kunci_jawaban'] = $request->kunci_jawaban_text;
            }

            Soal::create($data);
            
            DB::commit();

            return redirect()->route('bank-soal.show', $validated['bank_soal_id'])
                ->with('success', 'Soal berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan soal: ' . $e->getMessage());
        }
    }

    public function edit(Soal $soal)
    {
        $bankSoal = $soal->bankSoal;
        return view('pembelajaran.cbt.soal.edit', compact('soal', 'bankSoal'));
    }

    public function update(Request $request, Soal $soal)
    {
        $validated = $request->validate([
            'tipe_soal' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,menjodohkan,isian_singkat,uraian',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
            'pertanyaan' => 'required|string',
            'bobot' => 'required|numeric|min:0',
            'file_media' => 'nullable|file|mimes:jpg,jpeg,png,mp3,wav,mp4,webm|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;

             // Handle Media
            if ($request->hasFile('file_media')) {
                // Determine Old File to Delete
                $oldFile = null;
                if ($soal->tipe_media == 'image') $oldFile = $soal->gambar;
                if ($soal->tipe_media == 'audio') $oldFile = $soal->audio;
                if ($soal->tipe_media == 'video') $oldFile = $soal->video;
                
                if ($oldFile) {
                    Storage::delete('public/' . $oldFile);
                }

                $file = $request->file('file_media');
                $mime = $file->getMimeType();
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/soal-media', $filename);
                
                // Clear old columns first to avoid confusion
                $data['gambar'] = null;
                $data['audio'] = null;
                $data['video'] = null;

                if (str_starts_with($mime, 'image/')) {
                    $data['tipe_media'] = 'image';
                    $data['gambar'] = 'soal-media/' . $filename; 
                } elseif (str_starts_with($mime, 'audio/')) {
                    $data['tipe_media'] = 'audio';
                    $data['audio'] = 'soal-media/' . $filename;
                } elseif (str_starts_with($mime, 'video/')) {
                    $data['tipe_media'] = 'video';
                    $data['video'] = 'soal-media/' . $filename;
                }
            }

             if ($validated['tipe_soal'] == 'pilihan_ganda') {
                 $data['opsi_a'] = $request->opsi_a;
                 $data['opsi_b'] = $request->opsi_b;
                 $data['opsi_c'] = $request->opsi_c;
                 $data['opsi_d'] = $request->opsi_d;
                 $data['opsi_e'] = $request->opsi_e;
                 $data['kunci_jawaban'] = $request->kunci_jawaban;
            }
            
            if ($validated['tipe_soal'] == 'isian_singkat') {
                $data['kunci_jawaban'] = $request->kunci_jawaban_text;
            }

            $soal->update($data);
            
            DB::commit();

            return redirect()->route('bank-soal.show', $soal->bank_soal_id)
                ->with('success', 'Soal berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal update soal: ' . $e->getMessage());
        }
    }

    /**
     * Remove.
     */
    public function destroy(Soal $soal)
    {
        $soal->delete();
        return redirect()->back()->with('success', 'Soal berhasil dihapus');
    }
}
