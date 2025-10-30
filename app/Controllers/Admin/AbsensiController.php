<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;
use App\Models\KelasModel;
use App\Models\MapelKelasModel;
use App\Models\AbsensiModel;
use App\Models\SiswaModel;

class AbsensiController extends BaseController
{
    protected $taModel;
    protected $kelasModel;
    protected $mapelKelasModel;
    protected $absensiModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->taModel         = new TahunAjaranModel();
        $this->kelasModel      = new KelasModel();
        $this->mapelKelasModel = new MapelKelasModel();
        $this->absensiModel    = new AbsensiModel();
        $this->siswaModel      = new SiswaModel();
    }

    /**
     * Halaman utama Input Absensi
     */
    public function index()
    {
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif.');
            return redirect()->to('admin/tahun-ajaran');
        }

        $data = [
            'title'      => 'Input Absensi Siswa',
            'kelas_list' => $this->kelasModel->getKelasByTahunAjaran($ta_aktif['id_tahun_ajaran']),
        ];

        return view('admin/absensi_input', $data);
    }

    /**
     * [AJAX - UNTUK INPUT ABSENSI & REKAP ABSENSI]
     * Ambil Mata Pelajaran berdasarkan Kelas
     */
    public function ajaxGetMapel()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_kelas = $this->request->getPost('id_kelas');
        if (!$id_kelas) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Kelas tidak ada.']);
        }

        // Gunakan model pivot untuk mengambil mapel
        $mapel = $this->mapelKelasModel->getMapelByKelas($id_kelas);

        return $this->response->setJSON(['status' => 'success', 'mapel' => $mapel]);
    }

    /**
     * [AJAX] Ambil Siswa dan data absensi
     */
    public function getSiswaAbsensi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_kelas = $this->request->getPost('id_kelas');
        $id_mapel = $this->request->getPost('id_mapel');
        $tanggal  = $this->request->getPost('tanggal');
        $ta_aktif = $this->taModel->getTahunAjaranAktif();

        if (!$id_kelas || !$id_mapel || !$tanggal || !$ta_aktif) {
            return '<div class="alert alert-danger">Filter tidak lengkap.</div>';
        }

        $kriteria = [
            'id_tahun_ajaran' => $ta_aktif['id_tahun_ajaran'],
            'id_kelas'        => $id_kelas,
            'id_mapel'        => $id_mapel,
            'tanggal'         => $tanggal,
        ];

        // 1. Ambil daftar siswa
        $siswa_list = $this->siswaModel->getSiswaByKelas($id_kelas);

        // 2. Ambil data absensi yang sudah ada
        $absensi_data = $this->absensiModel->where($kriteria)->findAll();

        // 3. Buat map [id_siswa => data_absen] agar mudah dicari di view
        $absen_map = [];
        foreach ($absensi_data as $absen) {
            $absen_map[$absen['id_siswa']] = $absen;
        }

        $data = [
            'siswa_list' => $siswa_list,
            'absen_map'  => $absen_map,
            'kriteria'   => $kriteria
        ];

        return view('admin/partials/_absensi_tabel', $data);
    }

    public function detail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_ta    = $this->request->getPost('id_ta');
        $id_kelas = $this->request->getPost('id_kelas');
        $id_mapel = $this->request->getPost('id_mapel');
        $id_siswa = $this->request->getPost('id_siswa');
        $tanggal  = $this->request->getPost('tanggal');

        if (!$id_ta || !$id_kelas || !$id_mapel || !$id_siswa || !$tanggal) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        $absen = $this->absensiModel
            ->where([
                'id_tahun_ajaran' => $id_ta,
                'id_kelas' => $id_kelas,
                'id_mapel' => $id_mapel,
                'id_siswa' => $id_siswa,
                'tanggal' => $tanggal,
            ])->first();

        $buktiUrl = $absen && !empty($absen['bukti']) ? base_url($absen['bukti']) : null;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'id_absensi' => $absen['id_absensi'] ?? null,
                'status'     => $absen['status'] ?? null,
                'catatan'    => $absen['catatan'] ?? '',
                'bukti'      => $buktiUrl
            ]
        ]);
    }

    public function saveIzinSakit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_ta    = $this->request->getPost('id_ta');
        $id_kelas = $this->request->getPost('id_kelas');
        $id_mapel = $this->request->getPost('id_mapel');
        $id_siswa = $this->request->getPost('id_siswa');
        $tanggal  = $this->request->getPost('tanggal');
        $status   = $this->request->getPost('status'); // 'I' atau 'S'
        $catatan  = $this->request->getPost('catatan');
        $hapusBukti = $this->request->getPost('hapus_bukti'); // '1' kalau mau hapus

        if (!$id_ta || !$id_kelas || !$id_mapel || !$id_siswa || !$tanggal || !in_array($status, ['I', 'S'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap / status tidak valid']);
        }
        if (empty($catatan)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Catatan wajib diisi']);
        }

        $kriteria = [
            'id_tahun_ajaran' => $id_ta,
            'id_kelas' => $id_kelas,
            'id_mapel' => $id_mapel,
            'id_siswa' => $id_siswa,
            'tanggal' => $tanggal,
        ];
        $existing = $this->absensiModel->where($kriteria)->first();

        // handle file (opsional)
        $file = $this->request->getFile('bukti');
        $pathSimpan = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validasi manual sederhana
            $mime = $file->getMimeType();
            $okMime = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($mime, $okMime)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Bukti harus gambar JPG/PNG/WEBP']);
            }
            if ($file->getSizeByUnit('mb') > 4) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Bukti max 4MB']);
            }

            $subdir = 'uploads/absensi/' . date('Y/m') . '/';
            $targetDir = FCPATH . $subdir;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($targetDir, $newName);
            $pathSimpan = $subdir . $newName;

            // hapus file lama jika ada
            if ($existing && !empty($existing['bukti'])) {
                $old = FCPATH . $existing['bukti'];
                if (is_file($old)) @unlink($old);
            }
        } elseif ($hapusBukti === '1' && $existing && !empty($existing['bukti'])) {
            $old = FCPATH . $existing['bukti'];
            if (is_file($old)) @unlink($old);
            $pathSimpan = null; // set null nanti
        }

        $dataSave = array_merge($kriteria, [
            'status'  => $status,
            'catatan' => $catatan,
        ]);
        if ($pathSimpan !== null) {
            $dataSave['bukti'] = $pathSimpan;
        } elseif ($hapusBukti === '1') {
            $dataSave['bukti'] = null;
        }

        if ($existing) {
            $this->absensiModel->update($existing['id_absensi'], $dataSave);
            $idAbsensi = $existing['id_absensi'];
            $action = 'updated';
        } else {
            $this->absensiModel->insert($dataSave);
            $idAbsensi = $this->absensiModel->getInsertID();
            $action = 'inserted';
        }

        return $this->response->setJSON([
            'status' => 'success',
            'action' => $action,
            'id_absensi' => $idAbsensi,
            'bukti' => isset($dataSave['bukti']) && $dataSave['bukti'] ? base_url($dataSave['bukti']) : null
        ]);
    }
    /**
     * [AJAX] Auto-save data absensi
     */
    public function saveAbsen()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_ta    = $this->request->getPost('id_ta'); // FIX: seragamkan
        $id_kelas = $this->request->getPost('id_kelas');
        $id_mapel = $this->request->getPost('id_mapel');
        $id_siswa = $this->request->getPost('id_siswa');
        $tanggal  = $this->request->getPost('tanggal');
        $status   = $this->request->getPost('status'); // 'H' atau 'A' (I/S pakai modal terpisah)

        if (!$id_ta || !$id_kelas || !$id_mapel || !$id_siswa || !$tanggal) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }
        if ($status === 'I' || $status === 'S') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gunakan modal untuk Izin/Sakit.']);
        }

        $kriteria = [
            'id_tahun_ajaran' => $id_ta,
            'id_kelas' => $id_kelas,
            'id_mapel' => $id_mapel,
            'id_siswa' => $id_siswa,
            'tanggal' => $tanggal
        ];
        $existing = $this->absensiModel->where($kriteria)->first();

        try {
            if ($existing) {
                if (empty($status) || $status == 'null') {
                    $this->absensiModel->delete($existing['id_absensi']);
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Dihapus', 'action' => 'deleted']);
                } else {
                    $this->absensiModel->update($existing['id_absensi'], ['status' => $status]);
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Diupdate', 'action' => 'updated']);
                }
            } else {
                if (!empty($status) && $status != 'null') {
                    $data = $kriteria + ['status' => $status, 'catatan' => null, 'bukti' => null];
                    $this->absensiModel->insert($data);
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Disimpan', 'action' => 'inserted']);
                }
            }
            return $this->response->setJSON(['status' => 'success', 'message' => 'Tidak ada perubahan', 'action' => 'nothing']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
