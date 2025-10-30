<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
                </div>
                <div class="card-body">
                    <!-- Form
                    Kita gunakan 'multipart' agar siap jika nanti ingin tambah upload logo -->
                    <form id="formSettings" enctype="multipart/form-data"> 
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="nama_sekolah">Nama Sekolah</label>
                            <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" 
                                   value="<?= esc($settings['nama_sekolah'] ?? 'SMK') ?>">
                        </div>

                        <!-- Logo (Optional) - bisa dikembangkan nanti
                        <div class="form-group">
                            <label for="logo_sekolah">Logo Sekolah (Opsional)</label>
                            <input type="file" class="form-control-file" id="logo_sekolah" name="logo_sekolah">
                        </div>
                        -->

                        <div class="form-group">
                            <label for="format_tanggal">Format Tanggal</label>
                            <select class="form-control" id="format_tanggal" name="format_tanggal">
                                <option value="YYYY-MM-DD" <?= ($settings['format_tanggal'] ?? 'YYYY-MM-DD') == 'YYYY-MM-DD' ? 'selected' : '' ?>>
                                    YYYY-MM-DD (Contoh: 2025-10-31)
                                </option>
                                <option value="DD-MM-YYYY" <?= ($settings['format_tanggal'] ?? '') == 'DD-MM-YYYY' ? 'selected' : '' ?>>
                                    DD-MM-YYYY (Contoh: 31-10-2025)
                                </option>
                            </select>
                        </div>

                        <hr>
                        <h6>Bobot Kehadiran (untuk Rekap)</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="bobot_hadir">Hadir (H)</label>
                                <input type="number" step="0.1" class="form-control" id="bobot_hadir" name="bobot_hadir" 
                                       value="<?= esc($settings['bobot_hadir'] ?? '1.0') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="bobot_izin">Izin (I)</label>
                                <input type="number" step="0.1" class="form-control" id="bobot_izin" name="bobot_izin" 
                                       value="<?= esc($settings['bobot_izin'] ?? '0.7') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="bobot_sakit">Sakit (S)</label>
                                <input type="number" step="0.1" class="form-control" id="bobot_sakit" name="bobot_sakit" 
                                       value="<?= esc($settings['bobot_sakit'] ?? '0.9') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="bobot_alpa">Alpa (A)</label>
                                <input type="number" step="0.1" class="form-control" id="bobot_alpa" name="bobot_alpa" 
                                       value="<?= esc($settings['bobot_alpa'] ?? '0.0') ?>">
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan Pengaturan</button>

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
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        $('#formSettings').on('submit', function(e) {
            e.preventDefault();
            
            // Kita gunakan FormData untuk jaga-jaga jika ada upload file (logo)
            let formData = new FormData(this);

            $.ajax({
                url: '<?= site_url('admin/settings/save') ?>',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                processData: false, // Penting untuk FormData
                contentType: false, // Penting untuk FormData
                beforeSend: function() {
                    $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i> Menyimpan...');
                },
                complete: function() {
                    $('#btnSimpan').prop('disabled', false).html('Simpan Pengaturan');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Toast.fire({ icon: 'success', title: response.message });
                    } else {
                        Toast.fire({ icon: 'error', title: response.message || 'Gagal menyimpan data.' });
                    }
                },
                error: function() {
                     Toast.fire({ icon: 'error', title: 'Terjadi kesalahan server.' });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
