# Grade Management System Enhancement

## Overview
Enhance the existing grade management system to provide comprehensive tools for managing student grades, including all non-academic assessments (Nilai Sikap, Ekstrakurikuler, PKL) restricted to Wali Kelas, academic grades with manual override, and a centralized dashboard.

## User Review Required
### IMPORTANT

Role-Based Access for Non-Academic Assessments:

- Nilai Sikap → Wali Kelas only
- Nilai Ekstrakurikuler → Wali Kelas only
- Nilai PKL → Wali Kelas only
Academic Grades: Subject teachers can input component grades, but final grade override will be available for both teachers and Wali Kelas.

Proposed Changes
1. Non-Academic Assessment Access Control
[MODIFY] 
NilaiSikapController.php
Add middleware/check: User must be Wali Kelas
Filter classes to show only classes where user is Wali Kelas
Redirect with error if non-Wali Kelas tries to access

[MODIFY] 
NilaiEkstrakurikulerController.php
Add same Wali Kelas restriction
Filter classes based on Wali Kelas assignment
Add authorization checks in all methods

[MODIFY] 
NilaiPklController.php
Add Wali Kelas restriction
Filter students based on Wali Kelas's class
Add authorization checks

2. Academic Grades with Manual Override
[MODIFY] 
NilaiController.php
Add rekapNilai method for viewing all grades per student/class
Add overrideNilaiAkhir method for manual final grade input
Filter subjects based on teacher assignment (Guru Mapel can see their subjects)
Add role check: Guru Mapel OR Wali Kelas can override

[NEW] Migration: Add override columns to raport_detail table
$table->decimal('nilai_akhir_manual', 5, 2)->nullable()->after('nilai_akhir');
$table->boolean('is_manual_override')->default(false)->after('nilai_akhir_manual');
$table->text('override_reason')->nullable()->after('is_manual_override');
[NEW] 
nilai/rekap.blade.php
Matrix view showing all grades per student per subject
Columns: Student, Components (Tugas, UH, UTS, UAS, etc), Auto-calculated Final, Manual Override
Edit button for override (visible to Guru Mapel & Wali Kelas)
[NEW] 
nilai/override.blade.php
Form for manual final grade input
Show auto-calculated grade vs manual input
Reason/notes field (required)
Confirmation before saving
3. Centralized Grade Dashboard (Wali Kelas)
[NEW] 
DashboardNilaiController.php
index
: Show all students in Wali Kelas's class with completion status
show($siswa_id)
: Detailed view of all grades for one student
Filter by semester
Restrict access to Wali Kelas only

[NEW] 
pembelajaran/dashboard-nilai/index.blade.php
Table showing all students with:
Nilai Akademik completion (%)
Nilai Sikap status (✓/✗)
Nilai Ekstrakurikuler status (✓/✗)
Nilai PKL status (✓/✗ - if applicable)
Quick action buttons:
Input Nilai Sikap
Input Nilai Ekstrakurikuler
Input Nilai PKL
View Detail
Export to Excel functionality

[NEW] 
pembelajaran/dashboard-nilai/show.blade.php
Comprehensive view of one student's all grades:
Akademik: Grouped by subject, show all components + final grade
Sikap: Spiritual & Sosial
Ekstrakurikuler: Activity + Predikat
PKL: If applicable
Presensi: Summary (H, I, S, A)
Edit buttons for each section (redirect to respective input forms)

4. Menu Integration
[MODIFY] 
MenuSeeder.php
Add new menu structure:

Pembelajaran
├── Nilai Akademik (Guru, Wali Kelas, Admin)
│   ├── Input Nilai
│   └── Rekap & Override
├── Nilai Sikap (Wali Kelas, Admin)
├── Nilai Ekstrakurikuler (Wali Kelas, Admin)
├── Nilai PKL (Wali Kelas, Admin)
└── Dashboard Nilai (Wali Kelas, Admin) ← NEW
5. Routes
[MODIFY] 
web.php
Route::group(['middleware' => ['auth']], function () {
    // Nilai Akademik (Guru Mapel, Wali Kelas, Admin)
    Route::get('nilai/rekap', [NilaiController::class, 'rekap'])->name('nilai.rekap');
    Route::post('nilai/override', [NilaiController::class, 'overrideNilaiAkhir'])->name('nilai.override');
    Route::resource('nilai', NilaiController::class)->only(['index', 'create', 'store']);
    // Non-Academic Assessments (Wali Kelas, Admin only)
    Route::resource('nilai-sikap', NilaiSikapController::class)->only(['index', 'create', 'store']);
    Route::resource('nilai-ekstrakurikuler', NilaiEkstrakurikulerController::class)->only(['index', 'create', 'store']);
    Route::resource('nilai-pkl', NilaiPklController::class)->only(['index', 'create', 'store']);
    // Dashboard Nilai (Wali Kelas, Admin only)
    Route::get('dashboard-nilai', [DashboardNilaiController::class, 'index'])->name('dashboard-nilai.index');
    Route::get('dashboard-nilai/{siswa}', [DashboardNilaiController::class, 'show'])->name('dashboard-nilai.show');
});

## Verification Plan
### Automated Tests
Test Wali Kelas access control for all non-academic assessments
Test Guru Mapel can only access academic grades for their subjects
Test manual override calculation and storage
Test dashboard data aggregation
Manual Verification
Login as Wali Kelas:

✓ Can access Dashboard Nilai
✓ Can input Nilai Sikap, Ekstrakurikuler, PKL
✓ Can override academic final grades
✓ Can see all students in their class
Login as Guru Mapel (non-Wali Kelas):

✓ Can input academic component grades for their subjects
✓ Can override final grades for their subjects
✗ Cannot access Nilai Sikap, Ekstrakurikuler, PKL
✗ Cannot access Dashboard Nilai
Login as Admin:

✓ Can access all features
Test Override:

Input component grades → verify auto-calculation
Apply manual override → verify it takes precedence
Check raport generation uses override value