/**
 * AJAX Module - Fetch wrapper with CSRF support
 */

class Ajax {
  constructor() {
    this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    this.csrfName = document.querySelector('meta[name="csrf-name"]')?.content || 'csrf_test_name';
    this.baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
  }
  
  /**
   * Update CSRF token (call after each request if needed)
   */
  updateCsrf(newToken) {
    this.csrfToken = newToken;
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) metaTag.content = newToken;
    
    // Update all hidden CSRF inputs
    document.querySelectorAll(`input[name="${this.csrfName}"]`).forEach(input => {
      input.value = newToken;
    });
  }
  
  /**
   * Make a fetch request with CSRF token
   */
  async request(url, options = {}) {
    const defaultOptions = {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
    };
    
    const mergedOptions = { ...defaultOptions, ...options };
    
    // Add CSRF token for non-GET requests
    if (mergedOptions.method !== 'GET') {
      if (mergedOptions.body instanceof FormData) {
        mergedOptions.body.append(this.csrfName, this.csrfToken);
      } else if (typeof mergedOptions.body === 'object') {
        mergedOptions.body[this.csrfName] = this.csrfToken;
        mergedOptions.body = JSON.stringify(mergedOptions.body);
        mergedOptions.headers['Content-Type'] = 'application/json';
      } else if (typeof mergedOptions.body === 'string') {
        // URL encoded string
        mergedOptions.body += `&${this.csrfName}=${encodeURIComponent(this.csrfToken)}`;
        mergedOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
      } else {
        // No body, create one with CSRF
        mergedOptions.body = `${this.csrfName}=${encodeURIComponent(this.csrfToken)}`;
        mergedOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
      }
    }
    
    try {
      const response = await fetch(url, mergedOptions);
      
      // Update CSRF token from response header if present
      const newCsrf = response.headers.get('X-CSRF-TOKEN-Response');
      if (newCsrf) {
        this.updateCsrf(newCsrf);
      }
      
      // Parse response
      const contentType = response.headers.get('content-type');
      let data;
      
      if (contentType?.includes('application/json')) {
        data = await response.json();
      } else {
        data = await response.text();
      }
      
      if (!response.ok) {
        throw { status: response.status, data };
      }
      
      return data;
    } catch (error) {
      console.error('AJAX Error:', error);
      throw error;
    }
  }
  
  /**
   * GET request
   */
  get(url, params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const fullUrl = queryString ? `${url}?${queryString}` : url;
    return this.request(fullUrl);
  }
  
  /**
   * POST request with FormData or Object
   */
  post(url, data = {}) {
    return this.request(url, {
      method: 'POST',
      body: data instanceof FormData ? data : data,
    });
  }
  
  /**
   * POST with form element
   */
  postForm(url, formElement) {
    const formData = new FormData(formElement);
    return this.post(url, formData);
  }
}

// Initialize and expose globally
document.addEventListener('DOMContentLoaded', () => {
  window.ajax = new Ajax();
});

export default Ajax;
