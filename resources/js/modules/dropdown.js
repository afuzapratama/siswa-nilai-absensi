/**
 * Dropdown Module - Click-based dropdown menus
 */

class Dropdown {
  constructor() {
    this.activeDropdown = null;
    this.init();
  }
  
  init() {
    // Toggle dropdown
    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('[data-dropdown-toggle]');
      
      if (trigger) {
        e.preventDefault();
        e.stopPropagation();
        const dropdownId = trigger.dataset.dropdownToggle;
        this.toggle(dropdownId);
        return;
      }
      
      // Click outside - close all
      if (this.activeDropdown) {
        const dropdown = document.getElementById(this.activeDropdown);
        if (dropdown && !dropdown.contains(e.target)) {
          this.closeAll();
        }
      }
    });
    
    // Close on escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeAll();
      }
    });
  }
  
  toggle(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (!dropdown) return;
    
    const isHidden = dropdown.classList.contains('hidden');
    
    // Close any open dropdown first
    this.closeAll();
    
    if (isHidden) {
      dropdown.classList.remove('hidden');
      dropdown.classList.add('animate-fade-in');
      this.activeDropdown = dropdownId;
    }
  }
  
  closeAll() {
    document.querySelectorAll('[data-dropdown-menu]').forEach(menu => {
      menu.classList.add('hidden');
      menu.classList.remove('animate-fade-in');
    });
    this.activeDropdown = null;
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  window.dropdown = new Dropdown();
});

export default Dropdown;
