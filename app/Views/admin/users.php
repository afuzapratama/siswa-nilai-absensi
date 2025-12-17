<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Manajemen User</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola akun pengguna untuk akses sistem</p>
        </div>
        <button class="btn btn-primary" id="btnTambahUser">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Tambah User
        </button>
    </div>

    <!-- Card Tabel -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar User</h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Lengkap</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-64">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                                    <p>Belum ada data user</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; ?>
                            <?php foreach ($users as $user) : ?>
                                <tr class="hover:bg-gray-50" data-id="<?= $user['id'] ?>">
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= $no++ ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900"><?= esc($user['username']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= esc($user['nama_lengkap']) ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="btn btn-warning btn-sm btn-edit" data-id="<?= $user['id'] ?>" title="Edit">
                                                <i data-lucide="pencil" class="w-3 h-3"></i>
                                            </button>
                                            <button class="btn btn-sm bg-cyan-500 hover:bg-cyan-600 text-white btn-ganti-pass" 
                                                    data-id="<?= $user['id'] ?>" data-username="<?= esc($user['username']) ?>" title="Ganti Password">
                                                <i data-lucide="key" class="w-3 h-3"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $user['id'] ?>" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal CRUD User (Tambah/Edit) -->
<div class="modal" id="modalUser">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-md">
            <div class="modal-header">
                <h3 class="modal-title" id="modalLabel">Form User</h3>
                <button type="button" class="btn-close" data-dismiss="modal">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="formUser">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_field">
                <input type="hidden" name="id_user" id="id_user">
                <div class="modal-body space-y-4">
                    <!-- Error Alert -->
                    <div class="alert alert-danger hidden" id="error-alert"></div>

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-input" id="username" name="username" required 
                               placeholder="Masukkan username">
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-input" id="nama_lengkap" name="nama_lengkap" required
                               placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <!-- Grup Password (hanya tampil saat Tambah Baru) -->
                    <div id="grup-password" class="space-y-4">
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-input" id="password" name="password" 
                                   placeholder="Min. 6 karakter">
                        </div>
                        <div class="form-group">
                            <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-input" id="password_confirm" name="password_confirm"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ganti Password -->
<div class="modal" id="modalPassword">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-md">
            <div class="modal-header">
                <h3 class="modal-title">Ganti Password</h3>
                <button type="button" class="btn-close" data-dismiss="modal">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="formPassword">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_field_pass">
                <input type="hidden" name="id_user_pass" id="id_user_pass">
                <div class="modal-body space-y-4">
                    <!-- User info -->
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ganti password untuk</p>
                            <p class="font-semibold text-gray-900" id="username-pass"></p>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <div class="alert alert-danger hidden" id="error-alert-pass"></div>
                    
                    <div class="form-group">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-input" id="new_password" name="new_password" 
                               placeholder="Min. 6 karakter" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirm" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-input" id="new_password_confirm" name="new_password_confirm" 
                               placeholder="Ulangi password baru" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanPassword">
                        <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal" id="modalDelete">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-sm">
            <div class="modal-body text-center py-8">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus User?</h3>
                <p class="text-gray-500 text-sm mb-6">User yang dihapus tidak dapat dikembalikan!</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnKonfirmasiHapus">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="module">
    // CSRF & Base URL
    const csrfName = document.querySelector('meta[name="csrf-name"]').content;
    let csrfHash = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = document.querySelector('meta[name="base-url"]').content;

    // Update all CSRF fields
    function updateCsrfFields(newHash) {
        csrfHash = newHash;
        document.querySelectorAll('input[name="' + csrfName + '"]').forEach(el => {
            el.value = newHash;
        });
    }

    // Modal helpers
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on backdrop click or close button
    document.querySelectorAll('.modal').forEach(modal => {
        modal.querySelector('.modal-backdrop')?.addEventListener('click', () => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        });
        modal.querySelectorAll('[data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    });

    // Toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        toast.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // 1. Tombol Tambah User
    document.getElementById('btnTambahUser').addEventListener('click', () => {
        document.getElementById('modalLabel').textContent = 'Tambah User Baru';
        document.getElementById('formUser').reset();
        document.getElementById('id_user').value = '';
        document.getElementById('error-alert').classList.add('hidden');
        document.getElementById('error-alert').innerHTML = '';
        document.getElementById('grup-password').classList.remove('hidden');
        openModal('modalUser');
    });

    // 2. Tombol Edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;
            document.getElementById('modalLabel').textContent = 'Edit User';
            document.getElementById('error-alert').classList.add('hidden');
            document.getElementById('error-alert').innerHTML = '';
            document.getElementById('grup-password').classList.add('hidden');

            try {
                const formData = new FormData();
                formData.append('id', id);
                formData.append(csrfName, csrfHash);

                const response = await fetch(`${baseUrl}admin/users/fetch`, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.csrf_hash) updateCsrfFields(data.csrf_hash);
                
                if (data.status === 'success') {
                    document.getElementById('id_user').value = data.data.id;
                    document.getElementById('username').value = data.data.username;
                    document.getElementById('nama_lengkap').value = data.data.nama_lengkap;
                    openModal('modalUser');
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan server', 'error');
            }
        });
    });

    // 3. Submit Form User (Tambah/Edit)
    document.getElementById('formUser').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set(csrfName, csrfHash);
        
        const btnSimpan = document.getElementById('btnSimpan');
        const originalText = btnSimpan.innerHTML;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Menyimpan...';

        try {
            const response = await fetch(`${baseUrl}admin/users/save`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.csrf_hash) updateCsrfFields(data.csrf_hash);
            
            if (data.status === 'success') {
                closeModal('modalUser');
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                let errors = '<ul class="list-disc list-inside">';
                if (data.errors) {
                    Object.values(data.errors).forEach(err => {
                        errors += `<li>${err}</li>`;
                    });
                }
                errors += '</ul>';
                document.getElementById('error-alert').innerHTML = errors;
                document.getElementById('error-alert').classList.remove('hidden');
            }
        } catch (err) {
            showToast('Terjadi kesalahan server', 'error');
        } finally {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = originalText;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    });

    // 4. Tombol Delete
    let deleteUserId = null;
    
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteUserId = this.dataset.id;
            openModal('modalDelete');
        });
    });

    document.getElementById('btnKonfirmasiHapus').addEventListener('click', async function() {
        if (!deleteUserId) return;
        
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Menghapus...';

        try {
            const formData = new FormData();
            formData.append('id', deleteUserId);
            formData.append(csrfName, csrfHash);

            const response = await fetch(`${baseUrl}admin/users/delete`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.csrf_hash) updateCsrfFields(data.csrf_hash);
            
            closeModal('modalDelete');
            showToast(data.message, data.status);
            
            if (data.status === 'success') {
                setTimeout(() => location.reload(), 1000);
            }
        } catch (err) {
            showToast('Terjadi kesalahan server', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if (typeof lucide !== 'undefined') lucide.createIcons();
            deleteUserId = null;
        }
    });

    // 5. Tombol Ganti Password
    document.querySelectorAll('.btn-ganti-pass').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const username = this.dataset.username;
            
            document.getElementById('formPassword').reset();
            document.getElementById('id_user_pass').value = id;
            document.getElementById('username-pass').textContent = username;
            document.getElementById('error-alert-pass').classList.add('hidden');
            document.getElementById('error-alert-pass').innerHTML = '';
            openModal('modalPassword');
        });
    });

    // 6. Submit Form Ganti Password
    document.getElementById('formPassword').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set(csrfName, csrfHash);
        
        const btnSimpan = document.getElementById('btnSimpanPassword');
        const originalText = btnSimpan.innerHTML;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Menyimpan...';

        try {
            const response = await fetch(`${baseUrl}admin/users/change-password`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.csrf_hash) updateCsrfFields(data.csrf_hash);
            
            if (data.status === 'success') {
                closeModal('modalPassword');
                showToast(data.message, 'success');
            } else {
                let errors = '<ul class="list-disc list-inside">';
                if (data.errors) {
                    Object.values(data.errors).forEach(err => {
                        errors += `<li>${err}</li>`;
                    });
                }
                errors += '</ul>';
                document.getElementById('error-alert-pass').innerHTML = errors;
                document.getElementById('error-alert-pass').classList.remove('hidden');
            }
        } catch (err) {
            showToast('Terjadi kesalahan server', 'error');
        } finally {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = originalText;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    });
</script>
<?= $this->endSection() ?>
