<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;
use App\Models\KelasModel;
use App\Models\MapelKelasModel; // [FIX] Tambahkan model pivot
use App\Models\SiswaModel;
use App\Models\PenilaianHeaderModel;
use App\Models\PenilaianKolomModel;
use App\Models\PenilaianDetailModel;

class Penilaian extends BaseController
{
    protected $taModel;
    protected $kelasModel;
    protected $mapelKelasModel; // [FIX] Tambahkan properti
    protected $siswaModel;
    protected $headerModel;
    protected $kolomModel;
    protected $detailModel;

    public function __construct()
    {
        $this->taModel         = new TahunAjaranModel();
        $this->kelasModel      = new KelasModel();
        $this->mapelKelasModel = new MapelKelasModel(); // [FIX] Load model
        $this->siswaModel      = new SiswaModel();
        $this->headerModel     = new PenilaianHeaderModel();
        $this->kolomModel      = new PenilaianKolomModel();
        $this->detailModel     = new PenilaianDetailModel();
    }

    /**
     * Halaman daftar form penilaian
     */
    public function index()
    {
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif.');
            return redirect()->to('admin/tahun-ajaran');
        }

        $data = [
            'title'  => 'Daftar Form Penilaian',
            'forms'  => $this->headerModel->getAllForm($ta_aktif['id_tahun_ajaran']),
        ];
        return view('admin/penilaian_list', $data);
    }

    /**
     * Halaman membuat header form penilaian (Langkah 1)
     */
    public function create()
    {
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif.');
            return redirect()->to('admin/tahun-ajaran');
        }

        $data = [
            'title'      => 'Buat Form Penilaian Baru (Langkah 1 dari 2)',
            'kelas_list' => $this->kelasModel->getKelasByTahunAjaran($ta_aktif['id_tahun_ajaran']),
            'ta_aktif'   => $ta_aktif
        ];
        return view('admin/penilaian_create', $data);
    }

    /**
     * [AJAX - FIX] Fungsi yang hilang untuk 'penilaian/create'
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

        $mapel = $this->mapelKelasModel->getMapelByKelas($id_kelas);

        return $this->response->setJSON(['status' => 'success', 'mapel' => $mapel]);
    }


    /**
     * Simpan header form, lalu redirect ke form input nilai
     */
    public function saveHeader()
    {
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi Tahun Ajaran Aktif tidak ditemukan.']);
        }

        $data = [
            'id_tahun_ajaran' => $ta_aktif['id_tahun_ajaran'],
            'id_kelas'        => $this->request->getPost('id_kelas'),
            'id_mapel'        => $this->request->getPost('id_mapel'),
            'judul_penilaian' => $this->request->getPost('judul_penilaian'),
        ];

        $rules = $this->headerModel->getValidationRules();
        if (!$this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id_header = $this->headerModel->insert($data);

        $this->kolomModel->insert([
            'id_header'  => $id_header,
            'nama_kolom' => 'N1',
            'urutan'     => 1,
        ]);

        session()->setFlashdata('success', 'Form penilaian berhasil dibuat. Silakan isi nilai siswa.');
        return redirect()->to('admin/penilaian/form/' . $id_header);
    }

    /**
     * Halaman form input nilai dinamis (Langkah 2)
     */
    public function form($id_header)
    {
        $header = $this->headerModel->getDetailForm($id_header);

        if (!$header) {
            session()->setFlashdata('error', 'Form penilaian tidak ditemukan.');
            return redirect()->to('admin/penilaian');
        }

        // 1. Ambil Siswa
        $siswa_list = $this->siswaModel->getSiswaByKelas($header['id_kelas']);
        // 2. Ambil Kolom
        $kolom_list = $this->kolomModel->where('id_header', $id_header)->orderBy('urutan', 'ASC')->findAll();
        // 3. Ambil Nilai yang sudah ada
        $nilai_tercatat = $this->detailModel->where('id_header', $id_header)->findAll();

        // 4. Buat "Nilai Map" agar mudah dicari di View
        // $nilai_map[id_siswa][id_kolom] = nilai
        $nilai_map = [];
        foreach ($nilai_tercatat as $nilai) {
            $nilai_map[$nilai['id_siswa']][$nilai['id_kolom']] = $nilai['nilai'];
        }

        $data = [
            'title'      => 'Input Nilai: ' . esc($header['judul_penilaian']),
            'header'     => $header,
            'siswa_list' => $siswa_list,
            'kolom_list' => $kolom_list,
            'nilai_map'  => $nilai_map,
        ];
        return view('admin/penilaian_form_input', $data);
    }

    /**
     * [AJAX] Tambah kolom nilai (N2, N3, dst.)
     */
    public function addKolom()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_header  = (int) $this->request->getPost('id_header');
        $nama_input = trim((string) $this->request->getPost('nama_kolom'));

        $kolom_terakhir = $this->kolomModel
            ->where('id_header', $id_header)
            ->orderBy('urutan', 'DESC')
            ->first();

        $urutan_baru = (int) (($kolom_terakhir['urutan'] ?? 0) + 1);
        $nama_kolom_baru = $nama_input !== '' ? $nama_input : ('N' . $urutan_baru);

        // (Opsional) Cegah duplikat nama per header
        if ($this->kolomModel->where(['id_header' => $id_header, 'nama_kolom' => $nama_kolom_baru])->first()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Nama kolom sudah dipakai.'
            ]);
        }

        $id_kolom = $this->kolomModel->insert([
            'id_header'  => $id_header,
            'nama_kolom' => $nama_kolom_baru,
            'urutan'     => $urutan_baru,
        ]);

        if ($id_kolom) {
            return $this->response->setJSON([
                'status'     => 'success',
                'id_kolom'   => $id_kolom,
                'nama_kolom' => $nama_kolom_baru,
                'urutan'     => $urutan_baru,
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menambah kolom.']);
    }
    
    /**
     * [AJAX] Hapus kolom nilai
     */
    public function deleteKolom()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_kolom = $this->request->getPost('id_kolom');

        // Hapus nilai terkait
        $this->detailModel->where('id_kolom', $id_kolom)->delete();
        // Hapus kolom
        $this->kolomModel->delete($id_kolom);

        return $this->response->setJSON(['status' => 'success']);
    }


    /**
     * [AJAX] Auto-save nilai
     */
    public function saveNilai()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $data = [
            'id_header' => $this->request->getPost('id_header'),
            'id_kolom'  => $this->request->getPost('id_kolom'),
            'id_siswa'  => $this->request->getPost('id_siswa'),
            'nilai'     => $this->request->getPost('nilai')
        ];

        // Validasi nilai
        if (!is_numeric($data['nilai']) || $data['nilai'] < 0 || $data['nilai'] > 100) {
            if (empty($data['nilai'])) {
                // Jika nilai dihapus (string kosong), hapus dari DB
                $this->detailModel->where([
                    'id_header' => $data['id_header'],
                    'id_kolom'  => $data['id_kolom'],
                    'id_siswa'  => $data['id_siswa']
                ])->delete();
                return $this->response->setJSON(['status' => 'deleted']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nilai harus antara 0-100.']);
        }

        // Cek apakah data sudah ada (UPSERT)
        $existing = $this->detailModel->where([
            'id_header' => $data['id_header'],
            'id_kolom'  => $data['id_kolom'],
            'id_siswa'  => $data['id_siswa']
        ])->first();

        if ($existing) {
            // UPDATE
            $this->detailModel->update($existing['id_detail'], ['nilai' => $data['nilai']]);
            return $this->response->setJSON(['status' => 'updated']);
        } else {
            // INSERT
            $this->detailModel->insert($data);
            return $this->response->setJSON(['status' => 'created']);
        }
    }

    /**
     * [AJAX] Hapus seluruh form penilaian
     */
    public function deleteForm()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_header = $this->request->getPost('id_header');

        // Hapus (transaksi akan lebih baik, tapi ini cukup)
        $this->detailModel->where('id_header', $id_header)->delete();
        $this->kolomModel->where('id_header', $id_header)->delete();
        $this->headerModel->delete($id_header);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Form penilaian berhasil dihapus.']);
    }
}
