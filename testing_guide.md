# SIAKAD Testing Guide

This guide provides instructions on how to test each module of the SIAKAD application using the seeded data.

## 1. Test Accounts & Credentials

| Role | Username | Password | Notes |
|------|----------|----------|-------|
| **Super Admin** | `nicecode` | `sembarang` | Full access to all settings and roles. |
| **Admin** | `admin` | `password` | General administrative access. |
| **Guru (Wali Kelas)** | `budisantoso` | `password123` | Wali Kelas X RPL 1. |
| **Siswa** | `ahmadricki1` | `password123` | Sample student in X RPL 1. |

> [!NOTE]
> All generic teacher/student passwords are `password123` unless specified in `UserSeeder`.

---

## 2. Module Testing Flows

### A. Academic & Assessment (Guru Workflow)
1. **Login as `budisantoso`**.
2. Go to **Penilaian > Input Nilai**.
3. Select Class (**X RPL 1**) and Subject.
4. Verify that you can see students and input grades.
5. Go to **Dashboard Nilai** to see the progress of your students.

### B. CBT (Exam) Module
1. **Login as `ahmadricki1`**.
2. Go to **CBT > Jadwal Ujian**.
3. You should see active exams seeded by `CbtSeeder`.
4. (Optional) Try to take an exam.

### C. E-Learning
1. **Login as `ahmadricki1`**.
2. Go to **E-Learning > Materi Ajar**.
3. Verify you can see materials for your subjects.
4. Go to **E-Learning > Tugas** to see assigned tasks.

### D. PKL (Magang)
1. **Login as Admin**.
2. Go to **PKL > Data Perusahaan**.
3. Verify PT. Teknologi Maju and others are listed.
4. Check **PKL > Penempatan** to see students assigned to companies.

### E. Raport Generation
1. **Login as `budisantoso` (Wali Kelas)**.
2. Go to **Laporan > Raport**.
3. Click "Generate Raport" for a student.
4. Verify the grades are pulled from the `NilaiSeeder` data.

---

## 3. How to Reset Test Data
If you want to start fresh with clean seeded data, run:
```bash
php artisan migrate:fresh --seed
```
