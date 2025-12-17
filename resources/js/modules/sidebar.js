/**
 * Sidebar Module - Handles sidebar toggle for mobile and desktop
 */

class Sidebar {
  constructor() {
    this.sidebar = document.getElementById('sidebar');
    this.sidebarBackdrop = document.getElementById('sidebar-backdrop');
    this.sidebarToggle = document.getElementById('sidebar-toggle');
    this.sidebarClose = document.getElementById('sidebar-close');
    
    if (this.sidebar) {
      this.init();
    }
  }
  
  init() {
    // Mobile toggle button
    if (this.sidebarToggle) {
      this.sidebarToggle.addEventListener('click', () => this.open());
    }
    
    // Close button (mobile)
    if (this.sidebarClose) {
      this.sidebarClose.addEventListener('click', () => this.close());
    }
    
    // Backdrop click to close
    if (this.sidebarBackdrop) {
      this.sidebarBackdrop.addEventListener('click', () => this.close());
    }
    
    // Close on escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen()) {
        this.close();
      }
    });
    
    // Handle resize
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1024) {
        this.close();
      }
    });
  }
  
  open() {
    this.sidebar.classList.remove('-translate-x-full');
    this.sidebar.classList.add('translate-x-0');
    if (this.sidebarBackdrop) {
      this.sidebarBackdrop.classList.remove('hidden');
    }
    document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
  }
  
  close() {
    this.sidebar.classList.add('-translate-x-full');
    this.sidebar.classList.remove('translate-x-0');
    if (this.sidebarBackdrop) {
      this.sidebarBackdrop.classList.add('hidden');
    }
    document.body.classList.remove('overflow-hidden');
  }
  
  isOpen() {
    return this.sidebar.classList.contains('translate-x-0');
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  window.sidebar = new Sidebar();
});

export default Sidebar;
