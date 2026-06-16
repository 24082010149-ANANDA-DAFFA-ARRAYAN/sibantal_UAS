<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegacyController extends Controller
{
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function conn()
    {
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $database = env('DB_DATABASE', 'si_bantal');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        $conn = @mysqli_connect($host, $username, $password, $database, (int) $port);
        if (!$conn) {
            die("Koneksi Database Gagal: " . mysqli_connect_error());
        }
        mysqli_set_charset($conn, 'utf8mb4');

        // Seeder admin otomatis seperti versi PHP native
        $email_admin_default = 'admin@sibantal.com';
        $cek_admin = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_admin_default'");
        if ($cek_admin && mysqli_num_rows($cek_admin) == 0) {
            $password_hashed = password_hash('admin123', PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (nama_lengkap, email, password, role) VALUES ('Admin SI BanTal', '$email_admin_default', '$password_hashed', 'admin')");
        }

        return $conn;
    }

    private function wajibLogin(?string $role = null)
    {
        $this->startSession();
        if (!isset($_SESSION['user_id'])) {
            return redirect('login.php');
        }
        if ($role !== null && ($_SESSION['role'] ?? null) !== $role) {
            return redirect('index.php');
        }
        return null;
    }

    private function uploadFile(string $field, string $prefix = ''): string
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        $uploadDir = public_path('uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $safeName = preg_replace('/[^A-Za-z0-9._ -]/', '_', basename($_FILES[$field]['name']));
        $fileName = time() . '_' . $prefix . $safeName;
        move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $fileName);
        return $fileName;
    }

    public function index()
    {
        $this->startSession();
        $conn = $this->conn();

        $total_kk = (int) (mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT COALESCE(SUM(jumlah_kk),0) as total FROM permintaan_bantuan WHERE is_funded = 1"
        ))['total'] ?? 0);

        $total_program_aktif = (int) (mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT
                (SELECT COUNT(*) FROM permintaan_bantuan WHERE status = 'approved' AND is_funded = 0) +
                (SELECT COUNT(*) FROM penawaran_bantuan WHERE status = 'approved' AND is_funded = 0) as total"
        ))['total'] ?? 0);

        $total_desa = (int) (mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT COUNT(DISTINCT desa) as total FROM permintaan_bantuan WHERE is_funded = 1"
        ))['total'] ?? 0);

        $btn_link = 'login.php';
        $btn_text = 'Mulai Sekarang';
        if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] === 'desa') {
                $btn_link = 'contact.php';
                $btn_text = 'Mulai Pengajuan Bantuan';
            } elseif ($_SESSION['role'] === 'donatur') {
                $btn_link = 'contact.php';
                $btn_text = 'Tawarkan Bantuan';
            } elseif ($_SESSION['role'] === 'admin') {
                $btn_link = 'dashboard-admin.php';
                $btn_text = 'Buka Panel Admin';
            }
        }
        return view('legacy.index', compact('btn_link', 'btn_text', 'total_kk', 'total_program_aktif', 'total_desa'));
    }

    public function staticPage(string $page)
    {
        $this->startSession();

        if ($page === 'about') {
            $conn = $this->conn();

            $total_program = (int) (mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT
                    (SELECT COUNT(*) FROM permintaan_bantuan) +
                    (SELECT COUNT(*) FROM penawaran_bantuan) as total"
            ))['total'] ?? 0);

            $total_approved = (int) (mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT
                    (SELECT COUNT(*) FROM permintaan_bantuan WHERE status = 'approved') +
                    (SELECT COUNT(*) FROM penawaran_bantuan WHERE status = 'approved') as total"
            ))['total'] ?? 0);

            $total_funded = (int) (mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT
                    (SELECT COUNT(*) FROM permintaan_bantuan WHERE is_funded = 1) +
                    (SELECT COUNT(*) FROM penawaran_bantuan WHERE is_funded = 1) as total"
            ))['total'] ?? 0);

            $total_kk_terdata = (int) (mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT COALESCE(SUM(jumlah_kk),0) as total FROM permintaan_bantuan"
            ))['total'] ?? 0);

            $total_mitra = (int) (mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT COUNT(*) as total FROM users WHERE role = 'donatur'"
            ))['total'] ?? 0);

            $persen_approved = $total_program > 0 ? round(($total_approved / $total_program) * 100) : 0;
            $persen_realisasi = $total_approved > 0 ? round(($total_funded / $total_approved) * 100) : 0;

            return view('legacy.about', compact('persen_approved', 'persen_realisasi', 'total_kk_terdata', 'total_mitra'));
        }

        return view('legacy.' . $page);
    }

    public function login(Request $request)
    {
        $this->startSession();
        $conn = $this->conn();

        if(isset($_SESSION['user_id']) && $request->isMethod('get')){
            return redirect($this->dashboardByRole($_SESSION['role'] ?? ''));
        }

        $error = '';
        $is_auth_page = true;

        if ($request->isMethod('post') && $request->has('login')) {
            $email = mysqli_real_escape_string($conn, $request->input('email'));
            $password = $request->input('password');
            $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

            if ($query && mysqli_num_rows($query) === 1) {
                $row = mysqli_fetch_assoc($query);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['asal_desa'] = $row['asal_desa'];
                    $_SESSION['nama_organisasi'] = $row['nama_organisasi'];
                    return redirect($this->dashboardByRole($row['role']));
                }
                $error = 'Password salah!';
            } else {
                $error = 'Email tidak ditemukan!';
            }
        }

        return view('legacy.login', compact('error', 'is_auth_page'));
    }

    private function dashboardByRole(string $role): string
    {
        return match ($role) {
            'admin' => 'dashboard-admin.php',
            'desa' => 'dashboard-desa.php',
            'donatur' => 'dashboard-donatur.php',
            default => 'index.php',
        };
    }

    public function register(Request $request)
    {
        $this->startSession();
        $conn = $this->conn();
        $error = '';
        $success = '';
        $is_auth_page = true;

        if ($request->isMethod('post') && $request->has('register')) {
            $nama_lengkap = mysqli_real_escape_string($conn, $request->input('nama_lengkap'));
            $email = mysqli_real_escape_string($conn, $request->input('email'));
            $password = $request->input('password');
            $konfirmasi = $request->input('konfirmasi');
            $role = mysqli_real_escape_string($conn, $request->input('role'));
            $asal_desa = $request->filled('asal_desa') ? "'" . mysqli_real_escape_string($conn, $request->input('asal_desa')) . "'" : "NULL";
            $nama_organisasi = $request->filled('nama_organisasi') ? "'" . mysqli_real_escape_string($conn, $request->input('nama_organisasi')) . "'" : "NULL";

            if ($password !== $konfirmasi) {
                $error = 'Konfirmasi password tidak cocok!';
            } else {
                $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
                if ($cek_email && mysqli_num_rows($cek_email) > 0) {
                    $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
                } else {
                    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                    $query = "INSERT INTO users (nama_lengkap, email, password, role, asal_desa, nama_organisasi) 
                              VALUES ('$nama_lengkap', '$email', '$password_hashed', '$role', $asal_desa, $nama_organisasi)";
                    $success = mysqli_query($conn, $query) ? 'Pendaftaran berhasil! Silakan Login.' : 'Terjadi kesalahan: ' . mysqli_error($conn);
                }
            }
        }

        return view('legacy.register', compact('error', 'success', 'is_auth_page'));
    }

    public function logout()
    {
        $this->startSession();
        session_destroy();
        return redirect('login.php');
    }

    public function contact(Request $request)
    {
        if ($r = $this->wajibLogin()) return $r;
        $conn = $this->conn();
        $success = '';
        $error = '';

        if ($request->isMethod('post') && $request->has('submit_donatur')) {
            $user_id = $_SESSION['user_id'];
            $nama_instansi = mysqli_real_escape_string($conn, $request->input('nama_instansi'));
            $pj_donatur = mysqli_real_escape_string($conn, $request->input('pj_donatur'));
            $jabatan_donatur = mysqli_real_escape_string($conn, $request->input('jabatan_donatur'));
            $kontak_donatur = mysqli_real_escape_string($conn, $request->input('kontak_donatur'));
            $jenis_penawaran = mysqli_real_escape_string($conn, $request->input('jenis_penawaran'));
            $detail_bantuan = mysqli_real_escape_string($conn, $request->input('detail_bantuan'));
            $nama_file_donatur = $this->uploadFile('dokumen_donatur');
            $query = "INSERT INTO penawaran_bantuan 
                      (user_id, nama_instansi, pj_donatur, jabatan_donatur, kontak_donatur, jenis_penawaran, detail_bantuan, dokumen_donatur, status, is_funded) 
                      VALUES 
                      ('$user_id', '$nama_instansi', '$pj_donatur', '$jabatan_donatur', '$kontak_donatur', '$jenis_penawaran', '$detail_bantuan', '$nama_file_donatur', 'pending', 0)";
            if (mysqli_query($conn, $query)) {
                $success = "Penawaran bantuan beserta dokumen berhasil dikirim!";
            } else {
                $error = "Gagal mengirim penawaran: " . mysqli_error($conn);
            }
        }

        if ($request->isMethod('post') && $request->has('submit_desa')) {
            $user_id = $_SESSION['user_id'];
            $nama_pj = mysqli_real_escape_string($conn, $request->input('nama_pj'));
            $jabatan = mysqli_real_escape_string($conn, $request->input('jabatan'));
            $kontak_pj = mysqli_real_escape_string($conn, $request->input('kontak_pj'));
            $target_bantuan = mysqli_real_escape_string($conn, $request->input('target_bantuan'));
            $jumlah_kk = $request->filled('jumlah_kk') ? intval($request->input('jumlah_kk')) : 'NULL';
            $provinsi = mysqli_real_escape_string($conn, $request->input('provinsi'));
            $kota = mysqli_real_escape_string($conn, $request->input('kota'));
            $kecamatan = mysqli_real_escape_string($conn, $request->input('kecamatan'));
            $desa = mysqli_real_escape_string($conn, $request->input('desa'));
            $alasan = mysqli_real_escape_string($conn, $request->input('alasan'));
            $nama_file_desa = $this->uploadFile('dokumen_desa', 'desa_');
            $query = "INSERT INTO permintaan_bantuan 
                      (user_id, nama_pj, jabatan, kontak_pj, target_bantuan, jumlah_kk, provinsi, kota, kecamatan, desa, alasan, dokumen_desa, status, is_funded) 
                      VALUES 
                      ('$user_id', '$nama_pj', '$jabatan', '$kontak_pj', '$target_bantuan', $jumlah_kk, '$provinsi', '$kota', '$kecamatan', '$desa', '$alasan', '$nama_file_desa', 'pending', 0)";
            if (mysqli_query($conn, $query)) $success = "Permintaan bantuan beserta dokumen berhasil diajukan!";
            else $error = "Gagal mengajukan permintaan: " . mysqli_error($conn);
        }

        return view('legacy.contact', compact('success', 'error'));
    }

    public function portfolio(Request $request)
    {
        $this->startSession();
        $conn = $this->conn();
        $role = $_SESSION['role'] ?? 'guest';
        if ($role === 'admin') return redirect('dashboard-admin.php');

        $perPage = 6;
        $search = mysqli_real_escape_string($conn, $request->query('search', ''));
        $kategori_penawaran = mysqli_real_escape_string($conn, $request->query('kategori_penawaran', ''));
        $kategori_permintaan = mysqli_real_escape_string($conn, $request->query('kategori_permintaan', ''));

        $query_penawaran = null;
        $query_permintaan = null;
        $total_penawaran = 0;
        $total_permintaan = 0;
        $page_penawaran = max(1, intval($request->query('page_penawaran', 1)));
        $page_permintaan = max(1, intval($request->query('page_permintaan', 1)));
        $total_pages_penawaran = 1;
        $total_pages_permintaan = 1;

        if ($role === 'desa' || $role === 'guest') {
            $where1 = "status = 'approved' AND is_funded = 0";
            if ($search != '') { $where1 .= " AND (nama_instansi LIKE '%$search%' OR detail_bantuan LIKE '%$search%')"; }
            if ($kategori_penawaran != '') { $where1 .= " AND jenis_penawaran = '$kategori_penawaran'"; }

            $total_penawaran = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM penawaran_bantuan WHERE $where1"))['total'] ?? 0);
            $total_pages_penawaran = max(1, (int) ceil($total_penawaran / $perPage));
            $page_penawaran = min($page_penawaran, $total_pages_penawaran);
            $offset1 = ($page_penawaran - 1) * $perPage;

            $sql1 = "SELECT * FROM penawaran_bantuan WHERE $where1 ORDER BY created_at DESC LIMIT $perPage OFFSET $offset1";
            $query_penawaran = mysqli_query($conn, $sql1);
        }

        if ($role === 'donatur' || $role === 'guest') {
            $where2 = "status = 'approved' AND is_funded = 0";
            if ($search != '') { $where2 .= " AND (desa LIKE '%$search%' OR alasan LIKE '%$search%')"; }
            if ($kategori_permintaan != '') { $where2 .= " AND target_bantuan = '$kategori_permintaan'"; }

            $total_permintaan = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM permintaan_bantuan WHERE $where2"))['total'] ?? 0);
            $total_pages_permintaan = max(1, (int) ceil($total_permintaan / $perPage));
            $page_permintaan = min($page_permintaan, $total_pages_permintaan);
            $offset2 = ($page_permintaan - 1) * $perPage;

            $sql2 = "SELECT * FROM permintaan_bantuan WHERE $where2 ORDER BY created_at DESC LIMIT $perPage OFFSET $offset2";
            $query_permintaan = mysqli_query($conn, $sql2);
        }

        return view('legacy.portofolio', compact(
            'role', 'search', 'kategori_penawaran', 'kategori_permintaan',
            'query_penawaran', 'query_permintaan',
            'total_penawaran', 'total_permintaan',
            'page_penawaran', 'page_permintaan',
            'total_pages_penawaran', 'total_pages_permintaan'
        ));
    }

    public function dashboardAdmin(Request $request)
    {
        if ($r = $this->wajibLogin('admin')) return $r;
        $conn = $this->conn();
        $pesan = '';
        $perPage = 8;

        if ($request->isMethod('post') && $request->has('action_status')) {
            $id = mysqli_real_escape_string($conn, $request->input('id'));
            $tipe = mysqli_real_escape_string($conn, $request->input('tipe'));
            $status_baru = mysqli_real_escape_string($conn, $request->input('action_status'));
            $tabel = ($tipe === 'desa') ? 'permintaan_bantuan' : 'penawaran_bantuan';
            if (mysqli_query($conn, "UPDATE $tabel SET status = '$status_baru', updated_at = NOW() WHERE id = '$id'")) {
                $warna_alert = ($status_baru === 'approved') ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200';
                $teks_status = ($status_baru === 'approved') ? 'disetujui' : 'ditolak';
                $pesan = "<div class='mb-6 p-4 $warna_alert border rounded-lg font-semibold flex items-center justify-between'><span>Status data berhasil diubah menjadi $teks_status!</span><button onclick=\"this.parentElement.style.display='none'\">&times;</button></div>";
            }
        }

        if ($request->isMethod('post') && $request->has('hapus_data')) {
            $id = mysqli_real_escape_string($conn, $request->input('id'));
            $tipe = mysqli_real_escape_string($conn, $request->input('tipe'));
            $tabel = ($tipe === 'desa') ? 'permintaan_bantuan' : 'penawaran_bantuan';
            $kolom_file = ($tipe === 'desa') ? 'dokumen_desa' : 'dokumen_donatur';
            $query_file = mysqli_query($conn, "SELECT $kolom_file FROM $tabel WHERE id = '$id'");
            if ($query_file && $row_file = mysqli_fetch_assoc($query_file)) {
                $file = public_path('uploads/' . $row_file[$kolom_file]);
                if (!empty($row_file[$kolom_file]) && file_exists($file)) unlink($file);
            }
            if (mysqli_query($conn, "DELETE FROM $tabel WHERE id = '$id'")) {
                $pesan = "<div class='mb-6 p-4 bg-slate-800 text-white border border-slate-700 rounded-lg font-semibold flex items-center justify-between'><span>🗑️ Data program berhasil dihapus permanen!</span><button onclick=\"this.parentElement.style.display='none'\">&times;</button></div>";
            }
        }

        // Statistik ringkasan: pending / approved / rejected / sudah tersalurkan
        $stats_desa_raw = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT
                SUM(status='pending') as pending,
                SUM(status='approved') as approved,
                SUM(status='rejected') as rejected,
                SUM(is_funded=1) as funded
             FROM permintaan_bantuan"
        ));
        $stats_donatur_raw = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT
                SUM(status='pending') as pending,
                SUM(status='approved') as approved,
                SUM(status='rejected') as rejected,
                SUM(is_funded=1) as funded
             FROM penawaran_bantuan"
        ));
        $stats_desa = [
            'pending' => (int) ($stats_desa_raw['pending'] ?? 0),
            'approved' => (int) ($stats_desa_raw['approved'] ?? 0),
            'rejected' => (int) ($stats_desa_raw['rejected'] ?? 0),
            'funded' => (int) ($stats_desa_raw['funded'] ?? 0),
        ];
        $stats_donatur = [
            'pending' => (int) ($stats_donatur_raw['pending'] ?? 0),
            'approved' => (int) ($stats_donatur_raw['approved'] ?? 0),
            'rejected' => (int) ($stats_donatur_raw['rejected'] ?? 0),
            'funded' => (int) ($stats_donatur_raw['funded'] ?? 0),
        ];
        $count_desa = $stats_desa['pending'];
        $count_donatur = $stats_donatur['pending'];

        // Pagination
        $total_desa = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM permintaan_bantuan"))['total'] ?? 0);
        $total_donatur = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM penawaran_bantuan"))['total'] ?? 0);
        $total_pages_desa = max(1, (int) ceil($total_desa / $perPage));
        $total_pages_donatur = max(1, (int) ceil($total_donatur / $perPage));
        $page_desa = min($total_pages_desa, max(1, intval($request->query('page_desa', 1))));
        $page_donatur = min($total_pages_donatur, max(1, intval($request->query('page_donatur', 1))));
        $offset_desa = ($page_desa - 1) * $perPage;
        $offset_donatur = ($page_donatur - 1) * $perPage;

        $query_desa = mysqli_query($conn, "SELECT * FROM permintaan_bantuan ORDER BY FIELD(status, 'pending', 'approved', 'rejected'), created_at DESC LIMIT $perPage OFFSET $offset_desa");
        $query_donatur = mysqli_query($conn, "SELECT * FROM penawaran_bantuan ORDER BY FIELD(status, 'pending', 'approved', 'rejected'), created_at DESC LIMIT $perPage OFFSET $offset_donatur");

        return view('legacy.dashboard-admin', compact(
            'pesan', 'query_desa', 'query_donatur', 'count_desa', 'count_donatur',
            'stats_desa', 'stats_donatur',
            'total_desa', 'total_donatur',
            'page_desa', 'page_donatur',
            'total_pages_desa', 'total_pages_donatur'
        ));
    }

    public function dashboardDesa()
    {
        if ($r = $this->wajibLogin('desa')) return $r;
        $conn = $this->conn();
        $user_id = $_SESSION['user_id'];

        $result_apply = mysqli_query($conn, "SELECT hp.created_at as tgl_apply, pb.* FROM history_penyaluran hp JOIN penawaran_bantuan pb ON hp.program_id = pb.id WHERE hp.user_id = '$user_id' AND hp.tipe_program = 'penawaran' ORDER BY hp.created_at DESC");
        $result_my_req = mysqli_query($conn, "SELECT * FROM permintaan_bantuan WHERE user_id = '$user_id' ORDER BY created_at DESC");

        $rows_apply = $result_apply ? mysqli_fetch_all($result_apply, MYSQLI_ASSOC) : [];
        $rows_my_req = $result_my_req ? mysqli_fetch_all($result_my_req, MYSQLI_ASSOC) : [];

        $stats_desa_self = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'funded' => 0];
        foreach ($rows_my_req as $row) {
            $stats_desa_self['total']++;
            $stats_desa_self[$row['status']] = ($stats_desa_self[$row['status']] ?? 0) + 1;
            if ($row['is_funded'] == 1) $stats_desa_self['funded']++;
        }

        return view('legacy.dashboard-desa', compact('rows_apply', 'rows_my_req', 'stats_desa_self'));
    }

    public function dashboardDonatur()
    {
        if ($r = $this->wajibLogin('donatur')) return $r;
        $conn = $this->conn();
        $user_id = $_SESSION['user_id'];

        $result_danai = mysqli_query($conn, "SELECT hp.created_at as tgl_danai, mb.* FROM history_penyaluran hp JOIN permintaan_bantuan mb ON hp.program_id = mb.id WHERE hp.user_id = '$user_id' AND hp.tipe_program = 'permintaan' ORDER BY hp.created_at DESC");
        $result_my_offer = mysqli_query($conn, "SELECT * FROM penawaran_bantuan WHERE user_id = '$user_id' ORDER BY created_at DESC");

        $rows_danai = $result_danai ? mysqli_fetch_all($result_danai, MYSQLI_ASSOC) : [];
        $rows_my_offer = $result_my_offer ? mysqli_fetch_all($result_my_offer, MYSQLI_ASSOC) : [];

        $stats_donatur_self = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'funded' => 0];
        foreach ($rows_my_offer as $row) {
            $stats_donatur_self['total']++;
            $stats_donatur_self[$row['status']] = ($stats_donatur_self[$row['status']] ?? 0) + 1;
            if ($row['is_funded'] == 1) $stats_donatur_self['funded']++;
        }

        return view('legacy.dashboard-donatur', compact('rows_danai', 'rows_my_offer', 'stats_donatur_self'));
    }

    public function detail(Request $request)
    {
        $this->startSession();
        $conn = $this->conn();
        $role = $_SESSION['role'] ?? 'guest';
        $user_id_sekarang = $_SESSION['user_id'] ?? 0;
        $id = intval($request->query('id', 0));
        $tipe = $request->query('tipe', '');
        if ($id === 0 || $tipe === '') {
            die("<div class='p-10 text-center text-xl text-slate-500'>Data tidak ditemukan atau URL tidak valid. <a href='index.php' class='text-teal-500 underline'>Kembali</a></div>");
        }

        $pesan_sukses = '';
        $pesan_error = '';

        if ($request->isMethod('post') && $request->has('ambil_program')) {
            if ($role === 'guest') return redirect('login.php');
            $program_id = intval($request->input('program_id'));
            $tipe_program = mysqli_real_escape_string($conn, $request->input('tipe_program'));
            $table = ($tipe_program === 'permintaan' ? 'permintaan_bantuan' : 'penawaran_bantuan');
            $cek_funded = mysqli_query($conn, "SELECT is_funded FROM $table WHERE id = $program_id");
            $status_funded = mysqli_fetch_assoc($cek_funded)['is_funded'] ?? 0;
            if ($status_funded == 1) {
                $pesan_error = "Mohon maaf, program ini baru saja diambil/didanai oleh pihak lain.";
            } else {
                $query_history = "INSERT INTO history_penyaluran (user_id, program_id, tipe_program) VALUES ('$user_id_sekarang', '$program_id', '$tipe_program')";
                $query_update = "UPDATE $table SET is_funded = 1, updated_at = NOW() WHERE id = '$program_id'";
                if (mysqli_query($conn, $query_history) && mysqli_query($conn, $query_update)) {
                    $pesan_sukses = ($tipe_program === 'permintaan') ? "Terima kasih! Anda telah berhasil mendanai program desa ini." : "Berhasil! Anda telah mengklaim penawaran donatur ini untuk desa Anda.";
                } else {
                    $pesan_error = "Terjadi kesalahan sistem: " . mysqli_error($conn);
                }
            }
        }

        if ($tipe === 'permintaan') {
            $query = "SELECT pb.*, u.nama_lengkap as pembuat_akun, u.asal_desa as akun_asal, u.email as email_pembuat FROM permintaan_bantuan pb JOIN users u ON pb.user_id = u.id WHERE pb.id = $id";
        } else {
            $query = "SELECT pb.*, u.nama_lengkap as pembuat_akun, u.nama_organisasi as akun_asal, u.email as email_pembuat FROM penawaran_bantuan pb JOIN users u ON pb.user_id = u.id WHERE pb.id = $id";
        }
        $result = mysqli_query($conn, $query);
        $data = $result ? mysqli_fetch_assoc($result) : null;
        if (!$data) die("<div class='p-10 text-center text-xl text-slate-500'>Data tidak ditemukan di database.</div>");

        $taker_data = null;
        if ($data['is_funded'] == 1) {
            $query_taker = "SELECT u.nama_lengkap, u.asal_desa, u.nama_organisasi, u.email, hp.created_at as tgl_transaksi, hp.user_id as taker_id FROM history_penyaluran hp JOIN users u ON hp.user_id = u.id WHERE hp.program_id = $id AND hp.tipe_program = '$tipe'";
            $res_taker = mysqli_query($conn, $query_taker);
            if ($res_taker && mysqli_num_rows($res_taker) > 0) $taker_data = mysqli_fetch_assoc($res_taker);
        }

        return view('legacy.detail', compact('role', 'user_id_sekarang', 'id', 'tipe', 'pesan_sukses', 'pesan_error', 'data', 'taker_data'));
    }

    public function apply(Request $request)
    {
        if ($r = $this->wajibLogin()) return $r;
        $conn = $this->conn();

        if ($request->query('id') && $request->query('tipe')) {
            $program_id = mysqli_real_escape_string($conn, $request->query('id'));
            $tipe_program = mysqli_real_escape_string($conn, $request->query('tipe'));
            $user_id = $_SESSION['user_id'];
            $role = $_SESSION['role'];

            if (($role === 'desa' && $tipe_program !== 'penawaran') || ($role === 'donatur' && $tipe_program !== 'permintaan')) {
                die("Akses ilegal! Peran Anda tidak sesuai dengan tindakan ini.");
            }

            $tabel = ($tipe_program === 'permintaan') ? 'permintaan_bantuan' : 'penawaran_bantuan';

            // Cek dulu apakah program sudah diambil pihak lain (mencegah klaim ganda / race condition)
            $cek_funded = mysqli_query($conn, "SELECT is_funded FROM $tabel WHERE id = '$program_id'");
            $row_funded = $cek_funded ? mysqli_fetch_assoc($cek_funded) : null;

            if (!$row_funded || $row_funded['is_funded'] == 1) {
                return response("<script>alert('Mohon maaf, program ini tidak ditemukan atau baru saja diambil/didanai oleh pihak lain.'); window.location.href = 'portofolio.php';</script>");
            }

            $query_history = "INSERT INTO history_penyaluran (user_id, program_id, tipe_program) VALUES ('$user_id', '$program_id', '$tipe_program')";
            if (mysqli_query($conn, $query_history)) {
                mysqli_query($conn, "UPDATE $tabel SET is_funded = 1, updated_at = NOW() WHERE id = '$program_id'");
                return response("<script>alert('Berhasil! Anda telah terhubung dengan program ini. Pihak Admin SI BanTal akan segera menghubungi Anda untuk tindak lanjut penyaluran.'); window.location.href = 'portofolio.php';</script>");
            }

            return response("<script>alert('Mohon maaf, program ini baru saja diambil/didanai oleh pihak lain.'); window.location.href = 'portofolio.php';</script>");
        }
        return redirect('portofolio.php');
    }

    public function edit(Request $request)
    {
        if ($r = $this->wajibLogin('admin')) return $r;
        $conn = $this->conn();
        if (!$request->query('id') || !$request->query('tipe')) return redirect('dashboard-admin.php');
        $id = mysqli_real_escape_string($conn, $request->query('id'));
        $tipe = mysqli_real_escape_string($conn, $request->query('tipe'));
        $tabel = ($tipe === 'desa') ? 'permintaan_bantuan' : 'penawaran_bantuan';
        $pesan = '';
        $query_update = '';

        $query_data = mysqli_query($conn, "SELECT * FROM $tabel WHERE id = '$id'");
        if (!$query_data || mysqli_num_rows($query_data) === 0) die("Data tidak ditemukan.");
        $data = mysqli_fetch_assoc($query_data);

        if ($request->isMethod('post')) {
            if ($tipe === 'donatur') {
                $nama_instansi = mysqli_real_escape_string($conn, $request->input('nama_instansi'));
                $pj_donatur = mysqli_real_escape_string($conn, $request->input('pj_donatur'));
                $jabatan_donatur = mysqli_real_escape_string($conn, $request->input('jabatan_donatur'));
                $kontak_donatur = mysqli_real_escape_string($conn, $request->input('kontak_donatur'));
                $jenis_penawaran = mysqli_real_escape_string($conn, $request->input('jenis_penawaran'));
                $detail_bantuan = mysqli_real_escape_string($conn, $request->input('detail_bantuan'));

                $nama_file_donatur = $data['dokumen_donatur'];
                if (isset($_FILES['dokumen_donatur']) && $_FILES['dokumen_donatur']['error'] === UPLOAD_ERR_OK) {
                    $nama_file_donatur = $this->uploadFile('dokumen_donatur', 'edit_');
                }

                $query_update = "UPDATE penawaran_bantuan SET nama_instansi = '$nama_instansi', pj_donatur = '$pj_donatur', jabatan_donatur = '$jabatan_donatur', kontak_donatur = '$kontak_donatur', jenis_penawaran = '$jenis_penawaran', detail_bantuan = '$detail_bantuan', dokumen_donatur = '$nama_file_donatur', updated_at = NOW() WHERE id = '$id'";
            } elseif ($tipe === 'desa') {
                $nama_pj = mysqli_real_escape_string($conn, $request->input('nama_pj'));
                $jabatan = mysqli_real_escape_string($conn, $request->input('jabatan'));
                $kontak_pj = mysqli_real_escape_string($conn, $request->input('kontak_pj'));
                $desa = mysqli_real_escape_string($conn, $request->input('desa'));
                $target_bantuan = mysqli_real_escape_string($conn, $request->input('target_bantuan'));
                $jumlah_kk = $request->filled('jumlah_kk') ? intval($request->input('jumlah_kk')) : 'NULL';
                $alasan = mysqli_real_escape_string($conn, $request->input('alasan'));

                $nama_file_desa = $data['dokumen_desa'];
                if (isset($_FILES['dokumen_desa']) && $_FILES['dokumen_desa']['error'] === UPLOAD_ERR_OK) {
                    $nama_file_desa = $this->uploadFile('dokumen_desa', 'edit_desa_');
                }

                $query_update = "UPDATE permintaan_bantuan SET nama_pj = '$nama_pj', jabatan = '$jabatan', kontak_pj = '$kontak_pj', desa = '$desa', target_bantuan = '$target_bantuan', jumlah_kk = $jumlah_kk, alasan = '$alasan', dokumen_desa = '$nama_file_desa', updated_at = NOW() WHERE id = '$id'";
            }

            if ($query_update && mysqli_query($conn, $query_update)) {
                $pesan = "<div class='mb-6 p-4 bg-green-100 text-green-700 border border-green-200 rounded-lg font-bold'>Data berhasil diperbarui! <a href='dashboard-admin.php' class='underline'>Kembali ke Dashboard</a></div>";
                $fresh = mysqli_query($conn, "SELECT * FROM $tabel WHERE id = '$id'");
                if ($fresh) $data = mysqli_fetch_assoc($fresh);
            } else {
                $pesan = "<div class='mb-6 p-4 bg-red-100 text-red-700 border border-red-200 rounded-lg'>Gagal memperbarui: " . mysqli_error($conn) . "</div>";
            }
        }

        return view('legacy.edit', compact('id', 'tipe', 'tabel', 'pesan', 'data'));
    }

    public function editProgram(Request $request)
    {
        if ($r = $this->wajibLogin()) return $r;
        $conn = $this->conn();
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $id = intval($request->query('id', 0));
        $tipe = $request->query('tipe', '');
        $success = '';
        $error = '';

        $tabel = ($tipe === 'permintaan') ? 'permintaan_bantuan' : 'penawaran_bantuan';
        $result_cek = mysqli_query($conn, "SELECT * FROM $tabel WHERE id = '$id' AND user_id = '$user_id'");
        if (!$result_cek || mysqli_num_rows($result_cek) === 0) {
            die("<div class='p-10 text-center text-xl text-slate-500'>Data tidak ditemukan atau Anda tidak berhak mengedit data ini. <a href='index.php' class='text-teal-500 underline'>Kembali</a></div>");
        }
        $data_lama = mysqli_fetch_assoc($result_cek);
        if ($data_lama['status'] !== 'pending') {
            die("<div class='p-10 text-center text-xl text-red-500'>Program ini sudah diproses (Disetujui/Ditolak) sehingga tidak dapat diedit lagi. <a href='index.php' class='text-teal-500 underline'>Kembali</a></div>");
        }

        if ($request->isMethod('post')) {
            if ($tipe === 'penawaran' && $request->has('update_donatur')) {
                $jabatan_donatur = mysqli_real_escape_string($conn, $request->input('jabatan_donatur'));
                $kontak_donatur = mysqli_real_escape_string($conn, $request->input('kontak_donatur'));
                $jenis_penawaran = mysqli_real_escape_string($conn, $request->input('jenis_penawaran'));
                $detail_bantuan = mysqli_real_escape_string($conn, $request->input('detail_bantuan'));
                $nama_file_donatur = $data_lama['dokumen_donatur'];
                if (isset($_FILES['dokumen_donatur']) && $_FILES['dokumen_donatur']['error'] === UPLOAD_ERR_OK) $nama_file_donatur = $this->uploadFile('dokumen_donatur', 'edit_');
                $query_update = "UPDATE penawaran_bantuan SET jabatan_donatur = '$jabatan_donatur', kontak_donatur = '$kontak_donatur', jenis_penawaran = '$jenis_penawaran', detail_bantuan = '$detail_bantuan', dokumen_donatur = '$nama_file_donatur', updated_at = NOW() WHERE id = '$id'";
                $success = mysqli_query($conn, $query_update) ? "Data penawaran berhasil diperbarui!" : '';
                $error = $success ? '' : "Gagal memperbarui: " . mysqli_error($conn);
            }

            if ($tipe === 'permintaan' && $request->has('update_desa')) {
                $jabatan = mysqli_real_escape_string($conn, $request->input('jabatan'));
                $kontak_pj = mysqli_real_escape_string($conn, $request->input('kontak_pj'));
                $target_bantuan = mysqli_real_escape_string($conn, $request->input('target_bantuan'));
                $jumlah_kk = $request->filled('jumlah_kk') ? intval($request->input('jumlah_kk')) : 'NULL';
                $provinsi = mysqli_real_escape_string($conn, $request->input('provinsi'));
                $kota = mysqli_real_escape_string($conn, $request->input('kota'));
                $kecamatan = mysqli_real_escape_string($conn, $request->input('kecamatan'));
                $alasan = mysqli_real_escape_string($conn, $request->input('alasan'));
                $nama_file_desa = $data_lama['dokumen_desa'];
                if (isset($_FILES['dokumen_desa']) && $_FILES['dokumen_desa']['error'] === UPLOAD_ERR_OK) $nama_file_desa = $this->uploadFile('dokumen_desa', 'edit_desa_');
                $query_update = "UPDATE permintaan_bantuan SET jabatan = '$jabatan', kontak_pj = '$kontak_pj', target_bantuan = '$target_bantuan', jumlah_kk = $jumlah_kk, provinsi = '$provinsi', kota = '$kota', kecamatan = '$kecamatan', alasan = '$alasan', dokumen_desa = '$nama_file_desa', updated_at = NOW() WHERE id = '$id'";
                $success = mysqli_query($conn, $query_update) ? "Data pengajuan desa berhasil diperbarui!" : '';
                $error = $success ? '' : "Gagal memperbarui: " . mysqli_error($conn);
            }

            $fresh = mysqli_query($conn, "SELECT * FROM $tabel WHERE id = '$id' AND user_id = '$user_id'");
            if ($fresh) $data_lama = mysqli_fetch_assoc($fresh);
        }

        return view('legacy.edit_program', compact('user_id', 'role', 'id', 'tipe', 'success', 'error', 'data_lama'));
    }
}
