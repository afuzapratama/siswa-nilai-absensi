<?php helper('vite'); ?>
<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Penilaian Siswa">
    <meta name="author" content="Afuza Pratama">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="base-url" content="<?= base_url() ?>">
    
    <title><?= esc($title ?? 'Dashboard') ?> | Sistem Penilaian Siswa</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    <?= vite_assets(['css/app.css', 'js/app.js']) ?>
    
    <!-- Page-specific CSS -->
    <?= $this->renderSection('css') ?>
</head>

<body class="h-full bg-gray-50 font-sans antialiased">
    <div class="min-h-full">
        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden hidden"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-4 h-16 border-b border-white/10">
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                    </div>
                    <span class="text-white font-semibold text-lg">Nilai Siswa</span>
                    
                    <!-- Close button (mobile) -->
                    <button id="sidebar-close" class="lg:hidden ml-auto text-gray-400 hover:text-white p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-4 px-3 scrollbar-thin">
                    <!-- Dashboard -->
                    <a href="<?= site_url('admin/dashboard') ?>" 
                       class="<?= (current_url() == site_url('admin/dashboard')) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    
                    <!-- Section: Data Master -->
                    <div class="mt-6 mb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Data Master</p>
                    </div>
                    
                    <a href="<?= site_url('admin/tahun-ajaran') ?>" 
                       class="<?= (strpos(current_url(), 'admin/tahun-ajaran') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Tahun Ajaran</span>
                    </a>
                    
                    <a href="<?= site_url('admin/kelas') ?>" 
                       class="<?= (strpos(current_url(), 'admin/kelas') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Kelas</span>
                    </a>
                    
                    <a href="<?= site_url('admin/siswa') ?>" 
                       class="<?= (strpos(current_url(), 'admin/siswa') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Siswa</span>
                    </a>
                    
                    <a href="<?= site_url('admin/mata-pelajaran') ?>" 
                       class="<?= (strpos(current_url(), 'admin/mata-pelajaran') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Mata Pelajaran</span>
                    </a>
                    
                    <!-- Section: Penilaian -->
                    <div class="mt-6 mb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Penilaian</p>
                    </div>
                    
                    <a href="<?= site_url('admin/penilaian') ?>" 
                       class="<?= (strpos(current_url(), 'admin/penilaian') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Input Penilaian</span>
                    </a>
                    
                    <a href="<?= site_url('admin/report') ?>" 
                       class="<?= (strpos(current_url(), 'admin/report') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Laporan Nilai</span>
                    </a>
                    
                    <!-- Section: Absensi -->
                    <div class="mt-6 mb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Absensi</p>
                    </div>
                    
                    <a href="<?= site_url('admin/absensi') ?>" 
                       class="<?= (strpos(current_url(), 'admin/absensi') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Input Absensi</span>
                    </a>
                    
                    <a href="<?= site_url('admin/rekap-absensi') ?>" 
                       class="<?= (strpos(current_url(), 'admin/rekap-absensi') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span>Rekap Absensi</span>
                    </a>
                    
                    <!-- Section: Pengaturan -->
                    <div class="mt-6 mb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengaturan</p>
                    </div>
                    
                    <a href="<?= site_url('admin/users') ?>" 
                       class="<?= (strpos(current_url(), 'admin/users') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Manajemen User</span>
                    </a>
                    
                    <a href="<?= site_url('admin/settings') ?>" 
                       class="<?= (strpos(current_url(), 'admin/settings') !== false) ? 'nav-item-active' : 'nav-item-inactive' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Settings</span>
                    </a>
                </nav>
                
                <!-- User Info (Bottom) -->
                <div class="p-4 border-t border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-sm">
                                <?= strtoupper(substr(session()->get('username') ?? 'U', 0, 1)) ?>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium truncate">
                                <?= esc(session()->get('nama_lengkap') ?? session()->get('username') ?? 'User') ?>
                            </p>
                            <p class="text-gray-400 text-xs truncate">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <div class="lg:pl-64">
            <!-- Top Navigation -->
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <!-- Left: Mobile menu button + Page title -->
                    <div class="flex items-center gap-4">
                        <button id="sidebar-toggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-900 hidden sm:block">
                            <?= esc($title ?? 'Dashboard') ?>
                        </h1>
                    </div>
                    
                    <!-- Right: User dropdown -->
                    <div class="flex items-center gap-3">
                        <!-- User dropdown -->
                        <div class="relative">
                            <button data-dropdown-toggle="user-dropdown" class="flex items-center gap-2 p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-primary-600 font-medium text-sm">
                                        <?= strtoupper(substr(session()->get('username') ?? 'U', 0, 1)) ?>
                                    </span>
                                </div>
                                <span class="hidden sm:block text-sm font-medium text-gray-700">
                                    <?= esc(session()->get('username') ?? 'User') ?>
                                </span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div id="user-dropdown" data-dropdown-menu class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="#" class="dropdown-item flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </a>
                                <hr class="my-1 border-gray-100">
                                <a href="#" data-modal-target="modal-logout" class="dropdown-item flex items-center gap-2 text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert-success mb-4 flex items-center gap-3" role="alert">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert-error mb-4 flex items-center gap-3" role="alert">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
                <?php endif; ?>
                
                <!-- Page Title (Mobile) -->
                <h1 class="text-xl font-semibold text-gray-900 mb-4 sm:hidden">
                    <?= esc($title ?? 'Dashboard') ?>
                </h1>
                
                <!-- Content Section -->
                <?= $this->renderSection('content') ?>
            </main>
            
            <!-- Footer -->
            <footer class="border-t border-gray-200 py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    &copy; <?= date('Y') ?> Sistem Penilaian Siswa. Dibuat oleh Afuza Pratama.
                </p>
            </footer>
        </div>
    </div>
    
    <!-- Logout Modal -->
    <div id="modal-logout" class="modal">
        <div class="modal-backdrop"></div>
        <div class="modal-container">
            <div class="modal-content max-w-sm">
                <div class="modal-header">
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Logout</h3>
                    <button type="button" class="modal-close" data-modal-close>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-gray-600">Apakah Anda yakin ingin keluar dari sistem?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <a href="<?= site_url('logout') ?>" class="btn btn-danger">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Page-specific JS -->
    <?= $this->renderSection('scripts') ?>
</body>

</html>
