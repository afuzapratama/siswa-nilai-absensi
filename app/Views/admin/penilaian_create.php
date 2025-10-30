<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <p>Silakan pilih kelas, mata pelajaran, dan beri judul untuk form penilaian ini. <br>
                        Setelah disimpan, Anda akan diarahkan ke halaman input nilai.</p>

                    <form action="<?= site_url('admin/penilaian/save-header') ?>" method="POST">
                        <?= csrf_field() ?>

                        <!-- 1. Pilih Kelas -->
                        <div class="form-group">
                            <label for="id_kelas">1. Pilih Kelas (Tahun Aktif)</label>
                            <select class="form-control" id="id_kelas" name="id_kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_list as $kelas) : ?>
                                    <option value="<?= $kelas['id_kelas'] ?>" <?= (old('id_kelas') == $kelas['id_kelas']) ? 'selected' : '' ?>>
                                        <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Pilih Mata Pelajaran -->
                        <div class="form-group">
                            <label for="id_mapel">2. Pilih Mata Pelajaran</label>
                            <select class="form-control" id="id_mapel" name="id_mapel" required>
                                <option value="">-- Pilih Kelas Dulu --</option>
                            </select>
                        </div>

                        <!-- 3. Beri Judul -->
                        <div class="form-group">
                            <label for="judul_penilaian">3. Beri Judul Penilaian</label>
                            <input type="text" class="form-control" id="judul_penilaian" name="judul_penilaian" value="<?= old('judul_penilaian') ?>" placeholder="Contoh: Ujian Praktek Instalasi Jaringan" required>
                        </div>

                        <?php if (session()->has('errors')) : ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach (session('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>

                        <hr>
                        <a href="<?= site_url('admin/penilaian') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan dan Lanjut Input Nilai
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        let selectKelas = $('#id_kelas');
        let selectMapel = $('#id_mapel');

        // 1. AJAX: Ambil Mata Pelajaran saat Kelas dipilih
        selectKelas.on('change', function() {
            let id_kelas = $(this).val();
            selectMapel.html('<option value="">Loading...</option>');

            if (!id_kelas) {
                selectMapel.html('<option value="">-- Pilih Kelas Dulu --</option>');
                return;
            }

            $.ajax({
                // [FIX] Arahkan ke rute 'penilaian/get-mapel'
                url: '<?= site_url('admin/penilaian/get-mapel') ?>',
                type: 'POST',
                data: {
                    id_kelas: id_kelas,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'JSON',
                success: function(response) {
                    selectMapel.html('<option value="">-- Pilih Mata Pelajaran --</option>');
                    if (response.status === 'success' && response.mapel.length > 0) {
                        $.each(response.mapel, function(key, value) {
                            selectMapel.append('<option value="' + value.id_mapel + '">' + value.nama_mapel + '</option>');
                        });
                    } else {
                        selectMapel.html('<option value="">-- Tidak ada mapel terhubung --</option>');
                    }
                },
                error: function() {
                    selectMapel.html('<option value="">Gagal memuat mapel</option>');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>

