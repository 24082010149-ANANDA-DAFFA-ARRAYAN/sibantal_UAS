<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use PDOException;

class LegacyController extends Controller
{
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function conn(): PDO
    {
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $database = env('DB_DATABASE', 'si_bantal');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        try {
            $pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            die("Koneksi Database Gagal: " . $e->getMessage());
        }

        // Seeder admin otomatis
        $email_admin = 'admin@sibantal.com';
        $cek = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $cek->execute([$email_admin]);
        if ($cek->rowCount() == 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, 'admin')")
                ->execute(['Admin SI BanTal', $email_admin, $hash]);
        }

        return $pdo;
    }

    private function q(PDO $pdo, string $sql, array $params = []): \PDOStatement
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    private function fetchOne(PDO $pdo, string $sql, array $params = []): ?array
    {
        return $this->q($pdo, $sql, $params)->fetch() ?: null;
    }

    private function fetchAll(PDO $pdo, string $sql, array $params = []): array
    {
        return $this->q($pdo, $sql, $params)->fetchAll();
    }

    private function wajibLogin(?string $role = null)
    {
        $this->startSession();
        if (!isset($_SESSION['user_id'])) return redirect('login.php');
        if ($role !== null && ($_SESSION['role'] ?? null) !== $role) return redirect('index.php');
        return null;
    }

    private function uploadFile(string $field, string $prefix = ''): string
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return '';
        $uploadDir = public_path('uploads');
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
        $safeName = preg_replace('/[^A-Za-z0-9._ -]/', '_', basename($_FILES[$field]['name']));
        $fileName = time() . '_' . $prefix . $safeName;
        move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $fileName);
        return $fileName;
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

    public function index()
    {
        $this->startSession();
        $pdo = $this->conn();

        $total_kk = (int) ($this->fetchOne($pdo, "SELECT COALESCE(SUM(jumlah_kk),0) as total FROM permintaan_bantuan WHERE is_funded = 1")['total'] ?? 0);
        $total_program_aktif = (int) ($this->fetchOne($pdo, "SELECT (SELECT COUNT(*) FROM permintaan_bantuan WHERE status='approved' AND is_funded=0)+(SELECT COUNT(*) FROM penawaran_bantuan WHERE status='approved' AND is_funded=0) as total")['total'] ?? 0);
        $total_desa = (int) ($this->fetchOne($pdo, "SELECT COUNT(DISTINCT desa) as total FROM permintaan_bantuan WHERE is_funded = 1")['total'] ?? 0);

        // Data grafik donut — status program gabungan
        $status_pending  = (int)(($this->fetchOne($pdo, "SELECT (SELECT COUNT(*) FROM permintaan_bantuan WHERE status='pending')+(SELECT COUNT(*) FROM penawaran_bantuan WHERE status='pending') as total")['total']) ?? 0);
        $status_approved = (int)(($this->fetchOne($pdo, "SELECT (SELECT COUNT(*) FROM permintaan_bantuan WHERE status='approved')+(SELECT COUNT(*) FROM penawaran_bantuan WHERE status='approved') as total")['total']) ?? 0);
        $status_rejected = (int)(($this->fetchOne($pdo, "SELECT (SELECT COUNT(*) FROM permintaan_bantuan WHERE status='rejected')+(SELECT COUNT(*) FROM penawaran_bantuan WHERE status='rejected') as total")['total']) ?? 0);
        $status_funded   = (int)(($this->fetchOne($pdo, "SELECT (SELECT COUNT(*) FROM permintaan_bantuan WHERE is_funded=1)+(SELECT COUNT(*) FROM penawaran_bantuan WHERE is_funded=1) as total")['total']) ?? 0);
        $chart_status = json_encode([$status_pending, $status_approved, $status_rejected, $status_funded]);

        // Data grafik bar — jenis bantuan donatur (top 6)
        $rows_jenis = $this->fetchAll($pdo, "SELECT jenis_penawaran as label, COUNT(*) as total FROM penawaran_bantuan GROUP BY jenis_penawaran ORDER BY total DESC LIMIT 6");
        $chart_jenis_labels = json_encode(array_column($rows_jenis, 'label'));
        $chart_jenis_data   = json_encode(array_column($rows_jenis, 'total'));

        $btn_link = 'login.php'; $btn_text = 'Mulai Sekarang';
        if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] === 'desa') { $btn_link = 'contact.php'; $btn_text = 'Mulai Pengajuan Bantuan'; }
            elseif ($_SESSION['role'] === 'donatur') { $btn_link = 'contact.php'; $btn_text = 'Tawarkan Bantuan'; }
            elseif ($_SESSION['role'] === 'admin') { $btn_link = 'dashboard-admin.php'; $btn_text = 'Buka Panel Admin'; }
        }
        return view('legacy.index', compact(
            'btn_link', 'btn_text', 'total_kk', 'total_program_aktif', 'total_desa',
            'chart_status', 'chart_jenis_labels', 'chart_jenis_data'
        ));
    }

    public function staticPage(string $page)
    {
        $this->startSession();
        if ($page === 'about') {
            $pdo = $this->conn();
            $total_program = (int) ($this->fetchOne($pdo, "SELECT (SELECT COUNT(*) FROM permintaan_bantuan)+(SELECT COUNT(*) FROM penawaran_bantuan) as total")['total'] ?? 0);
            $total_approved = (int) ($this->fetchOne($pdo, "SELECT (SELECT COUNT(*) FROM permintaan_bantuan WHERE status='approved')+(SELECT COUNT(*) FROM penawaran_bantuan WHERE status='approved') as total")['total'] ?? 0);
            $total_funded = (int) ($this->fetchOne($pdo, "SELECT (SELECT COUNT(*) FROM permintaan_bantuan WHERE is_funded=1)+(SELECT COUNT(*) FROM penawaran_bantuan WHERE is_funded=1) as total")['total'] ?? 0);
            $total_kk_terdata = (int) ($this->fetchOne($pdo, "SELECT COALESCE(SUM(jumlah_kk),0) as total FROM permintaan_bantuan")['total'] ?? 0);
            $total_mitra = (int) ($this->fetchOne($pdo, "SELECT COUNT(*) as total FROM users WHERE role='donatur'")['total'] ?? 0);
            $persen_approved = $total_program > 0 ? round(($total_approved / $total_program) * 100) : 0;
            $persen_realisasi = $total_approved > 0 ? round(($total_funded / $total_approved) * 100) : 0;
            return view('legacy.about', compact('persen_approved', 'persen_realisasi', 'total_kk_terdata', 'total_mitra'));
        }
        return view('legacy.' . $page);
    }

    public function login(Request $request)
    {
        $this->startSession();
        $pdo = $this->conn();
        if (isset($_SESSION['user_id']) && $request->isMethod('get')) return redirect($this->dashboardByRole($_SESSION['role'] ?? ''));
        $error = ''; $is_auth_page = true;
        if ($request->isMethod('post') && $request->has('login')) {
            $email = $request->input('email');
            $row = $this->fetchOne($pdo, "SELECT * FROM users WHERE email = ?", [$email]);
            if ($row && password_verify($request->input('password'), $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['asal_desa'] = $row['asal_desa'];
                $_SESSION['provinsi'] = $row['provinsi'] ?? '';
                $_SESSION['kota'] = $row['kota'] ?? '';
                $_SESSION['kecamatan'] = $row['kecamatan'] ?? '';
                $_SESSION['nama_organisasi'] = $row['nama_organisasi'];
                return redirect($this->dashboardByRole($row['role']));
            }
            $error = $row ? 'Password salah!' : 'Email tidak ditemukan!';
        }
        return view('legacy.login', compact('error', 'is_auth_page'));
    }

    public function register(Request $request)
    {
        $this->startSession();
        $pdo = $this->conn();
        $error = ''; $success = ''; $is_auth_page = true;
        if ($request->isMethod('post') && $request->has('register')) {
            $nama = $request->input('nama_lengkap');
            $email = $request->input('email');
            $password = $request->input('password');
            $role = $request->input('role');
            $asal_desa = $request->filled('asal_desa') ? $request->input('asal_desa') : null;
            $provinsi = $request->filled('provinsi') ? $request->input('provinsi') : null;
            $kota = $request->filled('kota') ? $request->input('kota') : null;
            $kecamatan = $request->filled('kecamatan') ? $request->input('kecamatan') : null;
            $nama_org = $request->filled('nama_organisasi') ? $request->input('nama_organisasi') : null;
            if ($password !== $request->input('konfirmasi')) {
                $error = 'Konfirmasi password tidak cocok!';
            } elseif ($this->fetchOne($pdo, "SELECT id FROM users WHERE email = ?", [$email])) {
                $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $this->q($pdo, "INSERT INTO users (nama_lengkap, email, password, role, asal_desa, provinsi, kota, kecamatan, nama_organisasi) VALUES (?,?,?,?,?,?,?,?,?)", [$nama, $email, $hash, $role, $asal_desa, $provinsi, $kota, $kecamatan, $nama_org]);
                    $success = 'Pendaftaran berhasil! Silakan Login.';
                } catch (PDOException $e) { $error = 'Terjadi kesalahan: ' . $e->getMessage(); }
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
        $pdo = $this->conn();
        $success = ''; $error = '';

        if ($request->isMethod('post') && $request->has('submit_donatur')) {
            $uid = $_SESSION['user_id'];
            $f = $this->uploadFile('dokumen_donatur');
            try {
                $this->q($pdo, "INSERT INTO penawaran_bantuan (user_id,nama_instansi,pj_donatur,jabatan_donatur,kontak_donatur,jenis_penawaran,detail_bantuan,dokumen_donatur,status,is_funded) VALUES (?,?,?,?,?,?,?,?,'pending',0)", [
                    $uid, $request->input('nama_instansi'), $request->input('pj_donatur'),
                    $request->input('jabatan_donatur'), $request->input('kontak_donatur'),
                    $request->input('jenis_penawaran'), $request->input('detail_bantuan'), $f
                ]);
                $success = "Penawaran bantuan beserta dokumen berhasil dikirim!";
            } catch (PDOException $e) { $error = "Gagal mengirim penawaran: " . $e->getMessage(); }
        }

        if ($request->isMethod('post') && $request->has('submit_desa')) {
            $uid = $_SESSION['user_id'];
            $jkk = $request->filled('jumlah_kk') ? intval($request->input('jumlah_kk')) : null;
            $f = $this->uploadFile('dokumen_desa', 'desa_');
            try {
                $this->q($pdo, "INSERT INTO permintaan_bantuan (user_id,nama_pj,jabatan,kontak_pj,target_bantuan,jumlah_kk,provinsi,kota,kecamatan,desa,alasan,dokumen_desa,status,is_funded) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,'pending',0)", [
                    $uid, $request->input('nama_pj'), $request->input('jabatan'),
                    $request->input('kontak_pj'), $request->input('target_bantuan'), $jkk,
                    $request->input('provinsi'), $request->input('kota'), $request->input('kecamatan'),
                    $request->input('desa'), $request->input('alasan'), $f
                ]);
                $success = "Permintaan bantuan beserta dokumen berhasil diajukan!";
            } catch (PDOException $e) { $error = "Gagal mengajukan permintaan: " . $e->getMessage(); }
        }

        $wilayah_desa = [
            'provinsi'  => $_SESSION['provinsi']  ?? '',
            'kota'      => $_SESSION['kota']      ?? '',
            'kecamatan' => $_SESSION['kecamatan'] ?? '',
            'desa'      => $_SESSION['asal_desa'] ?? '',
        ];

        return view('legacy.contact', compact('success', 'error', 'wilayah_desa'));
    }

    public function portfolio(Request $request)
    {
        $this->startSession();
        $pdo = $this->conn();
        $role = $_SESSION['role'] ?? 'guest';
        if ($role === 'admin') return redirect('dashboard-admin.php');

        $perPage = 6;
        $search = $request->query('search', '');
        $kategori_penawaran = $request->query('kategori_penawaran', '');
        $kategori_permintaan = $request->query('kategori_permintaan', '');
        $query_penawaran = null; $query_permintaan = null;
        $total_penawaran = 0; $total_permintaan = 0;
        $page_penawaran = max(1, intval($request->query('page_penawaran', 1)));
        $page_permintaan = max(1, intval($request->query('page_permintaan', 1)));
        $total_pages_penawaran = 1; $total_pages_permintaan = 1;

        if ($role === 'desa' || $role === 'guest') {
            $where1 = "status='approved' AND is_funded=0";
            $params1 = [];
            if ($search) { $where1 .= " AND (nama_instansi LIKE ? OR detail_bantuan LIKE ?)"; $params1[] = "%$search%"; $params1[] = "%$search%"; }
            if ($kategori_penawaran) { $where1 .= " AND jenis_penawaran=?"; $params1[] = $kategori_penawaran; }
            $total_penawaran = (int) ($this->fetchOne($pdo, "SELECT COUNT(*) as total FROM penawaran_bantuan WHERE $where1", $params1)['total'] ?? 0);
            $total_pages_penawaran = max(1, (int) ceil($total_penawaran / $perPage));
            $page_penawaran = min($page_penawaran, $total_pages_penawaran);
            $offset1 = ($page_penawaran - 1) * $perPage;
            $query_penawaran = $this->fetchAll($pdo, "SELECT * FROM penawaran_bantuan WHERE $where1 ORDER BY created_at DESC LIMIT $perPage OFFSET $offset1", $params1);
        }

        if ($role === 'donatur' || $role === 'guest') {
            $where2 = "status='approved' AND is_funded=0";
            $params2 = [];
            if ($search) { $where2 .= " AND (desa LIKE ? OR alasan LIKE ?)"; $params2[] = "%$search%"; $params2[] = "%$search%"; }
            if ($kategori_permintaan) { $where2 .= " AND target_bantuan=?"; $params2[] = $kategori_permintaan; }
            $total_permintaan = (int) ($this->fetchOne($pdo, "SELECT COUNT(*) as total FROM permintaan_bantuan WHERE $where2", $params2)['total'] ?? 0);
            $total_pages_permintaan = max(1, (int) ceil($total_permintaan / $perPage));
            $page_permintaan = min($page_permintaan, $total_pages_permintaan);
            $offset2 = ($page_permintaan - 1) * $perPage;
            $query_permintaan = $this->fetchAll($pdo, "SELECT * FROM permintaan_bantuan WHERE $where2 ORDER BY created_at DESC LIMIT $perPage OFFSET $offset2", $params2);
        }

        return view('legacy.portofolio', compact('role','search','kategori_penawaran','kategori_permintaan','query_penawaran','query_permintaan','total_penawaran','total_permintaan','page_penawaran','page_permintaan','total_pages_penawaran','total_pages_permintaan'));
    }

    public function dashboardAdmin(Request $request)
    {
        if ($r = $this->wajibLogin('admin')) return $r;
        $pdo = $this->conn();
        $pesan = ''; $perPage = 8;

        if ($request->isMethod('post') && $request->has('action_status')) {
            $id = $request->input('id'); $tipe = $request->input('tipe');
            $status_baru = $request->input('action_status');
            $tabel = ($tipe === 'desa') ? 'permintaan_bantuan' : 'penawaran_bantuan';
            try {
                $this->q($pdo, "UPDATE $tabel SET status=?, updated_at=NOW() WHERE id=?", [$status_baru, $id]);
                $warna = ($status_baru === 'approved') ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200';
                $teks = ($status_baru === 'approved') ? 'disetujui' : 'ditolak';
                $pesan = "<div class='mb-6 p-4 $warna border rounded-lg font-semibold flex items-center justify-between'><span>Status data berhasil diubah menjadi $teks!</span><button onclick=\"this.parentElement.style.display='none'\">&times;</button></div>";
            } catch (PDOException $e) {}
        }

        if ($request->isMethod('post') && $request->has('hapus_data')) {
            $id = $request->input('id'); $tipe = $request->input('tipe');
            $tabel = ($tipe === 'desa') ? 'permintaan_bantuan' : 'penawaran_bantuan';
            $kolom_file = ($tipe === 'desa') ? 'dokumen_desa' : 'dokumen_donatur';
            $row_file = $this->fetchOne($pdo, "SELECT $kolom_file FROM $tabel WHERE id=?", [$id]);
            if ($row_file && !empty($row_file[$kolom_file])) {
                $file = public_path('uploads/' . $row_file[$kolom_file]);
                if (file_exists($file)) unlink($file);
            }
            try {
                $this->q($pdo, "DELETE FROM $tabel WHERE id=?", [$id]);
                $pesan = "<div class='mb-6 p-4 bg-slate-800 text-white border border-slate-700 rounded-lg font-semibold flex items-center justify-between'><span>🗑️ Data program berhasil dihapus permanen!</span><button onclick=\"this.parentElement.style.display='none'\">&times;</button></div>";
            } catch (PDOException $e) {}
        }

        $sr = $this->fetchOne($pdo, "SELECT SUM(status='pending') as pending, SUM(status='approved') as approved, SUM(status='rejected') as rejected, SUM(is_funded=1) as funded FROM permintaan_bantuan");
        $sd = $this->fetchOne($pdo, "SELECT SUM(status='pending') as pending, SUM(status='approved') as approved, SUM(status='rejected') as rejected, SUM(is_funded=1) as funded FROM penawaran_bantuan");
        $stats_desa = ['pending'=>(int)($sr['pending']??0),'approved'=>(int)($sr['approved']??0),'rejected'=>(int)($sr['rejected']??0),'funded'=>(int)($sr['funded']??0)];
        $stats_donatur = ['pending'=>(int)($sd['pending']??0),'approved'=>(int)($sd['approved']??0),'rejected'=>(int)($sd['rejected']??0),'funded'=>(int)($sd['funded']??0)];
        $count_desa = $stats_desa['pending']; $count_donatur = $stats_donatur['pending'];

        $total_desa = (int)($this->fetchOne($pdo,"SELECT COUNT(*) as total FROM permintaan_bantuan")['total']??0);
        $total_donatur = (int)($this->fetchOne($pdo,"SELECT COUNT(*) as total FROM penawaran_bantuan")['total']??0);
        $total_pages_desa = max(1,(int)ceil($total_desa/$perPage));
        $total_pages_donatur = max(1,(int)ceil($total_donatur/$perPage));
        $page_desa = min($total_pages_desa,max(1,intval($request->query('page_desa',1))));
        $page_donatur = min($total_pages_donatur,max(1,intval($request->query('page_donatur',1))));
        $od = ($page_desa-1)*$perPage; $odon = ($page_donatur-1)*$perPage;

        $query_desa = $this->fetchAll($pdo, "SELECT * FROM permintaan_bantuan ORDER BY FIELD(status,'pending','approved','rejected'), created_at DESC LIMIT $perPage OFFSET $od");
        $query_donatur = $this->fetchAll($pdo, "SELECT * FROM penawaran_bantuan ORDER BY FIELD(status,'pending','approved','rejected'), created_at DESC LIMIT $perPage OFFSET $odon");

        return view('legacy.dashboard-admin', compact('pesan','query_desa','query_donatur','count_desa','count_donatur','stats_desa','stats_donatur','total_desa','total_donatur','page_desa','page_donatur','total_pages_desa','total_pages_donatur'));
    }

    public function dashboardDesa()
    {
        if ($r = $this->wajibLogin('desa')) return $r;
        $pdo = $this->conn();
        $uid = $_SESSION['user_id'];
        $rows_apply = $this->fetchAll($pdo, "SELECT hp.created_at as tgl_apply, pb.* FROM history_penyaluran hp JOIN penawaran_bantuan pb ON hp.program_id=pb.id WHERE hp.user_id=? AND hp.tipe_program='penawaran' ORDER BY hp.created_at DESC", [$uid]);
        $rows_my_req = $this->fetchAll($pdo, "SELECT * FROM permintaan_bantuan WHERE user_id=? ORDER BY created_at DESC", [$uid]);
        $stats_desa_self = ['total'=>0,'pending'=>0,'approved'=>0,'rejected'=>0,'funded'=>0];
        foreach ($rows_my_req as $row) { $stats_desa_self['total']++; $stats_desa_self[$row['status']] = ($stats_desa_self[$row['status']]??0)+1; if($row['is_funded']==1) $stats_desa_self['funded']++; }
        return view('legacy.dashboard-desa', compact('rows_apply','rows_my_req','stats_desa_self'));
    }

    public function dashboardDonatur()
    {
        if ($r = $this->wajibLogin('donatur')) return $r;
        $pdo = $this->conn();
        $uid = $_SESSION['user_id'];
        $rows_danai = $this->fetchAll($pdo, "SELECT hp.created_at as tgl_danai, mb.* FROM history_penyaluran hp JOIN permintaan_bantuan mb ON hp.program_id=mb.id WHERE hp.user_id=? AND hp.tipe_program='permintaan' ORDER BY hp.created_at DESC", [$uid]);
        $rows_my_offer = $this->fetchAll($pdo, "SELECT * FROM penawaran_bantuan WHERE user_id=? ORDER BY created_at DESC", [$uid]);
        $stats_donatur_self = ['total'=>0,'pending'=>0,'approved'=>0,'rejected'=>0,'funded'=>0];
        foreach ($rows_my_offer as $row) { $stats_donatur_self['total']++; $stats_donatur_self[$row['status']] = ($stats_donatur_self[$row['status']]??0)+1; if($row['is_funded']==1) $stats_donatur_self['funded']++; }
        return view('legacy.dashboard-donatur', compact('rows_danai','rows_my_offer','stats_donatur_self'));
    }

    public function detail(Request $request)
    {
        $this->startSession();
        $pdo = $this->conn();
        $role = $_SESSION['role'] ?? 'guest';
        $user_id_sekarang = $_SESSION['user_id'] ?? 0;
        $id = intval($request->query('id', 0));
        $tipe = $request->query('tipe', '');
        if ($id === 0 || $tipe === '') die("<div class='p-10 text-center text-xl text-slate-500'>Data tidak ditemukan. <a href='index.php' class='text-teal-500 underline'>Kembali</a></div>");

        $pesan_sukses = ''; $pesan_error = '';

        if ($request->isMethod('post') && $request->has('ambil_program')) {
            if ($role === 'guest') return redirect('login.php');
            $program_id = intval($request->input('program_id'));
            $tipe_program = $request->input('tipe_program');
            $table = ($tipe_program === 'permintaan') ? 'permintaan_bantuan' : 'penawaran_bantuan';
            $row_f = $this->fetchOne($pdo, "SELECT is_funded FROM $table WHERE id=?", [$program_id]);
            if (!$row_f || $row_f['is_funded'] == 1) {
                $pesan_error = "Mohon maaf, program ini baru saja diambil/didanai oleh pihak lain.";
            } else {
                try {
                    $this->q($pdo, "INSERT INTO history_penyaluran (user_id,program_id,tipe_program) VALUES (?,?,?)", [$user_id_sekarang,$program_id,$tipe_program]);
                    $this->q($pdo, "UPDATE $table SET is_funded=1, updated_at=NOW() WHERE id=?", [$program_id]);
                    $pesan_sukses = ($tipe_program==='permintaan') ? "Terima kasih! Anda telah berhasil mendanai program desa ini." : "Berhasil! Anda telah mengklaim penawaran donatur ini untuk desa Anda.";
                } catch (PDOException $e) { $pesan_error = "Terjadi kesalahan sistem."; }
            }
        }

        if ($tipe === 'permintaan') {
            $data = $this->fetchOne($pdo, "SELECT pb.*, u.nama_lengkap as pembuat_akun, u.asal_desa as akun_asal, u.email as email_pembuat FROM permintaan_bantuan pb JOIN users u ON pb.user_id=u.id WHERE pb.id=?", [$id]);
        } else {
            $data = $this->fetchOne($pdo, "SELECT pb.*, u.nama_lengkap as pembuat_akun, u.nama_organisasi as akun_asal, u.email as email_pembuat FROM penawaran_bantuan pb JOIN users u ON pb.user_id=u.id WHERE pb.id=?", [$id]);
        }
        if (!$data) die("<div class='p-10 text-center text-xl text-slate-500'>Data tidak ditemukan di database.</div>");

        $taker_data = null;
        if ($data['is_funded'] == 1) {
            $taker_data = $this->fetchOne($pdo, "SELECT u.nama_lengkap, u.asal_desa, u.nama_organisasi, u.email, hp.created_at as tgl_transaksi, hp.user_id as taker_id FROM history_penyaluran hp JOIN users u ON hp.user_id=u.id WHERE hp.program_id=? AND hp.tipe_program=?", [$id,$tipe]);
        }

        return view('legacy.detail', compact('role','user_id_sekarang','id','tipe','pesan_sukses','pesan_error','data','taker_data'));
    }

    public function apply(Request $request)
    {
        if ($r = $this->wajibLogin()) return $r;
        $pdo = $this->conn();

        if ($request->query('id') && $request->query('tipe')) {
            $program_id = intval($request->query('id'));
            $tipe_program = $request->query('tipe');
            $uid = $_SESSION['user_id'];
            $role = $_SESSION['role'];
            if (($role==='desa' && $tipe_program!=='penawaran') || ($role==='donatur' && $tipe_program!=='permintaan')) die("Akses ilegal!");
            $tabel = ($tipe_program==='permintaan') ? 'permintaan_bantuan' : 'penawaran_bantuan';
            $row_f = $this->fetchOne($pdo, "SELECT is_funded FROM $tabel WHERE id=?", [$program_id]);
            if (!$row_f || $row_f['is_funded']==1) return response("<script>alert('Mohon maaf, program ini tidak ditemukan atau baru saja diambil/didanai oleh pihak lain.'); window.location.href='portofolio.php';</script>");
            try {
                $this->q($pdo, "INSERT INTO history_penyaluran (user_id,program_id,tipe_program) VALUES (?,?,?)", [$uid,$program_id,$tipe_program]);
                $this->q($pdo, "UPDATE $tabel SET is_funded=1, updated_at=NOW() WHERE id=?", [$program_id]);
                return response("<script>alert('Berhasil! Anda telah terhubung dengan program ini.'); window.location.href='portofolio.php';</script>");
            } catch (PDOException $e) {
                return response("<script>alert('Mohon maaf, program ini baru saja diambil.'); window.location.href='portofolio.php';</script>");
            }
        }
        return redirect('portofolio.php');
    }

    public function edit(Request $request)
    {
        if ($r = $this->wajibLogin('admin')) return $r;
        $pdo = $this->conn();
        if (!$request->query('id') || !$request->query('tipe')) return redirect('dashboard-admin.php');
        $id = intval($request->query('id'));
        $tipe = $request->query('tipe');
        $tabel = ($tipe==='desa') ? 'permintaan_bantuan' : 'penawaran_bantuan';
        $pesan = '';

        $data = $this->fetchOne($pdo, "SELECT * FROM $tabel WHERE id=?", [$id]);
        if (!$data) die("Data tidak ditemukan.");

        if ($request->isMethod('post')) {
            try {
                if ($tipe==='donatur') {
                    $f = $data['dokumen_donatur'];
                    if (isset($_FILES['dokumen_donatur']) && $_FILES['dokumen_donatur']['error']===UPLOAD_ERR_OK) $f = $this->uploadFile('dokumen_donatur','edit_');
                    $this->q($pdo, "UPDATE penawaran_bantuan SET nama_instansi=?,pj_donatur=?,jabatan_donatur=?,kontak_donatur=?,jenis_penawaran=?,detail_bantuan=?,dokumen_donatur=?,updated_at=NOW() WHERE id=?", [
                        $request->input('nama_instansi'),$request->input('pj_donatur'),$request->input('jabatan_donatur'),
                        $request->input('kontak_donatur'),$request->input('jenis_penawaran'),$request->input('detail_bantuan'),$f,$id
                    ]);
                } elseif ($tipe==='desa') {
                    $jkk = $request->filled('jumlah_kk') ? intval($request->input('jumlah_kk')) : null;
                    $f = $data['dokumen_desa'];
                    if (isset($_FILES['dokumen_desa']) && $_FILES['dokumen_desa']['error']===UPLOAD_ERR_OK) $f = $this->uploadFile('dokumen_desa','edit_desa_');
                    $this->q($pdo, "UPDATE permintaan_bantuan SET nama_pj=?,jabatan=?,kontak_pj=?,desa=?,target_bantuan=?,jumlah_kk=?,alasan=?,dokumen_desa=?,updated_at=NOW() WHERE id=?", [
                        $request->input('nama_pj'),$request->input('jabatan'),$request->input('kontak_pj'),
                        $request->input('desa'),$request->input('target_bantuan'),$jkk,$request->input('alasan'),$f,$id
                    ]);
                }
                $pesan = "<div class='mb-6 p-4 bg-green-100 text-green-700 border border-green-200 rounded-lg font-bold'>Data berhasil diperbarui! <a href='dashboard-admin.php' class='underline'>Kembali ke Dashboard</a></div>";
                $data = $this->fetchOne($pdo, "SELECT * FROM $tabel WHERE id=?", [$id]);
            } catch (PDOException $e) {
                $pesan = "<div class='mb-6 p-4 bg-red-100 text-red-700 border border-red-200 rounded-lg'>Gagal memperbarui: " . $e->getMessage() . "</div>";
            }
        }
        return view('legacy.edit', compact('id','tipe','tabel','pesan','data'));
    }

    public function editProgram(Request $request)
    {
        if ($r = $this->wajibLogin()) return $r;
        $pdo = $this->conn();
        $uid = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $id = intval($request->query('id', 0));
        $tipe = $request->query('tipe', '');
        $success = ''; $error = '';

        $tabel = ($tipe==='permintaan') ? 'permintaan_bantuan' : 'penawaran_bantuan';
        $data_lama = $this->fetchOne($pdo, "SELECT * FROM $tabel WHERE id=? AND user_id=?", [$id,$uid]);
        if (!$data_lama) die("<div class='p-10 text-center text-xl text-slate-500'>Data tidak ditemukan atau Anda tidak berhak mengedit data ini. <a href='index.php' class='text-teal-500 underline'>Kembali</a></div>");
        if ($data_lama['status']!=='pending') die("<div class='p-10 text-center text-xl text-red-500'>Program ini sudah diproses sehingga tidak dapat diedit lagi. <a href='index.php' class='text-teal-500 underline'>Kembali</a></div>");

        if ($request->isMethod('post')) {
            try {
                if ($tipe==='penawaran' && $request->has('update_donatur')) {
                    $f = $data_lama['dokumen_donatur'];
                    if (isset($_FILES['dokumen_donatur']) && $_FILES['dokumen_donatur']['error']===UPLOAD_ERR_OK) $f = $this->uploadFile('dokumen_donatur','edit_');
                    $this->q($pdo, "UPDATE penawaran_bantuan SET jabatan_donatur=?,kontak_donatur=?,jenis_penawaran=?,detail_bantuan=?,dokumen_donatur=?,updated_at=NOW() WHERE id=?", [
                        $request->input('jabatan_donatur'),$request->input('kontak_donatur'),
                        $request->input('jenis_penawaran'),$request->input('detail_bantuan'),$f,$id
                    ]);
                    $success = "Data penawaran berhasil diperbarui!";
                }
                if ($tipe==='permintaan' && $request->has('update_desa')) {
                    $jkk = $request->filled('jumlah_kk') ? intval($request->input('jumlah_kk')) : null;
                    $f = $data_lama['dokumen_desa'];
                    if (isset($_FILES['dokumen_desa']) && $_FILES['dokumen_desa']['error']===UPLOAD_ERR_OK) $f = $this->uploadFile('dokumen_desa','edit_desa_');
                    $this->q($pdo, "UPDATE permintaan_bantuan SET jabatan=?,kontak_pj=?,target_bantuan=?,jumlah_kk=?,provinsi=?,kota=?,kecamatan=?,alasan=?,dokumen_desa=?,updated_at=NOW() WHERE id=?", [
                        $request->input('jabatan'),$request->input('kontak_pj'),$request->input('target_bantuan'),
                        $jkk,$request->input('provinsi'),$request->input('kota'),$request->input('kecamatan'),
                        $request->input('alasan'),$f,$id
                    ]);
                    $success = "Data pengajuan desa berhasil diperbarui!";
                }
                $data_lama = $this->fetchOne($pdo, "SELECT * FROM $tabel WHERE id=? AND user_id=?", [$id,$uid]) ?? $data_lama;
            } catch (PDOException $e) { $error = "Gagal memperbarui: " . $e->getMessage(); }
        }
        return view('legacy.edit_program', compact('uid','role','id','tipe','success','error','data_lama'));
    }
}