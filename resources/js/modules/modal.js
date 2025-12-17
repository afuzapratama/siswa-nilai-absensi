/**
 * Modal Module - Vanilla JS Modal Handler
 */

class Modal {
  constructor() {
    this.activeModal = null;
    this.init();
  }
  
  init() {
    // Listen for modal triggers
    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('[data-modal-target]');
      if (trigger) {
        e.preventDefault();
        const modalId = trigger.dataset.modalTarget;
        this.open(modalId);
      }
      
      // Close button
      const closeBtn = e.target.closest('[data-modal-close]');
      if (closeBtn) {
        e.preventDefault();
        this.close();
      }
      
      // Click on modal backdrop
      if (e.target.classList.contains('modal-backdrop')) {
        this.close();
      }
    });
    
    // Close on escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.activeModal) {
        this.close();
      }
    });
  }
  
  open(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    this.activeModal = modalId;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Focus first input if exists
    setTimeout(() => {
      const firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
      if (firstInput) firstInput.focus();
    }, 100);
    
    // Dispatch event
    modal.dispatchEvent(new CustomEvent('modal:opened'));
  }
  
  close() {
    if (!this.activeModal) return;
    
    const modal = document.getElementById(this.activeModal);
    if (modal) {
      modal.classList.remove('active');
      modal.dispatchEvent(new CustomEvent('modal:closed'));
    }
    
    document.body.style.overflow = '';
    this.activeModal = null;
  }
}

// Initialize and expose globally
document.addEventListener('DOMContentLoaded', () => {
  window.modal = new Modal();
});

// Helper functions for programmatic control
window.openModal = (modalId) => window.modal?.open(modalId);
window.closeModal = () => window.modal?.close();

export default Modal;
