<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Traits\SendsNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    use SendsNotifications;

    public function index()
    {
        // Get unique announcements by title/message to show history
        // Since we don't have an Announcement table, we'll use Notifikasi but group them
        // In a real app, an Announcement table is better, but here we'll simulate.
        $announcements = Notifikasi::where('tipe', 'announcement')
            ->select('judul', 'pesan', DB::raw('MAX(created_at) as created_at'))
            ->groupBy('judul', 'pesan')
            ->latest('created_at')
            ->paginate(10);

        return view('kepalasekolah.announcement.index', compact('announcements'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['guru', 'siswa', 'admin'])->get();
        return view('kepalasekolah.announcement.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:100',
            'pesan' => 'required|string',
            'target_roles' => 'required|array',
        ]);

        $targets = $request->target_roles;
        
        // Log activity before sending
        // (Assuming trait handles this if we use a model, but here it's mass sending)
        
        DB::beginTransaction();
        try {
            if (in_array('all', $targets)) {
                $roleNames = ['guru', 'siswa', 'admin', 'kepala-sekolah'];
            } else {
                $roleNames = $targets;
            }

            $this->notifyRoles($roleNames, $request->judul, $request->pesan, 'announcement');

            DB::commit();
            return redirect()->route('admin.announcement.index')->with('success', 'Pengumuman resmi berhasil disebarkan ke ' . implode(', ', $roleNames));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim pengumuman: ' . $e->getMessage())->withInput();
        }
    }
}
