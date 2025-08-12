<script>
export default {
  name: 'Navbar',
  data() {
    return {
      isAuthenticated: false,
      user: null,
      isMenuOpen: false,
    }
  },
  methods: {
    async logout() {
      // Implement logout logic
      this.isAuthenticated = false
      this.user = null
      this.$router.push('/login')
    },
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen
    },
  },
}
</script>

<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
      <!-- Brand/Logo -->
      <RouterLink class="navbar-brand d-flex align-items-center text-decoration-none" to="/">
        <div class="brand-icon me-2">
          <i class="bi bi-journal-text"></i>
        </div>
        <span class="brand-text">BlogSpace</span>
      </RouterLink>

      <!-- Mobile toggle button -->
      <button
        class="navbar-toggler border-0 p-0"
        type="button"
        @click="toggleMenu"
        :aria-expanded="isMenuOpen"
        aria-controls="navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation items -->
      <div class="collapse navbar-collapse" id="navbarNav" :class="{ show: isMenuOpen }">
        <div class="ms-auto d-flex align-items-center flex-column flex-lg-row">
          <!-- Sign In button -->
          <RouterLink to="/login" class="btn btn-link nav-signin-btn me-lg-3 mb-2 mb-lg-0">
            Sign In
          </RouterLink>

          <!-- Get Started button -->
          <RouterLink to="/register" class="btn btn-primary nav-getstarted-btn">
            Get Started
          </RouterLink>
        </div>
      </div>
    </div>
  </nav>

  <!-- Spacer for fixed navbar -->
  <div class="navbar-spacer"></div>
</template>

<style scoped>
/* Navbar Styles */
.navbar {
  padding: 1rem 0;
  backdrop-filter: blur(20px);
  background-color: rgba(255, 255, 255, 0.95) !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  z-index: 1030;
}

.navbar-spacer {
  height: 80px;
}

/* Brand Styles */
.navbar-brand {
  font-weight: 700;
  font-size: 1.5rem;
  color: #667eea !important;
  transition: all 0.3s ease;
  text-decoration: none !important;
}

.navbar-brand:hover {
  color: #5a67d8 !important;
  transform: translateY(-1px);
}

.brand-icon {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
}

.brand-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 700;
}

/* Navigation Button Styles */
.nav-signin-btn {
  color: #6c757d !important;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border: none;
  background: none;
  text-decoration: none !important;
  transition: all 0.3s ease;
  border-radius: 8px;
}

.nav-signin-btn:hover {
  color: #495057 !important;
  background-color: rgba(0, 0, 0, 0.05);
}

.nav-getstarted-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  color: white !important;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none !important;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
  position: relative;
  overflow: hidden;
}

.nav-getstarted-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: -1;
}

.nav-getstarted-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
  color: white !important;
}

.nav-getstarted-btn:hover::before {
  opacity: 1;
}

/* Mobile Responsive */
@media (max-width: 991.98px) {
  .navbar-collapse {
    margin-top: 1rem;
    padding: 1rem 0;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
  }

  .nav-signin-btn,
  .nav-getstarted-btn {
    width: 100%;
    text-align: center;
    justify-content: center;
  }

  .nav-signin-btn {
    margin-bottom: 0.5rem;
  }
}

/* Mobile toggle button */
.navbar-toggler {
  padding: 0.25rem 0.5rem;
  border: none;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.navbar-toggler:focus {
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
  outline: none;
}

.navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Accessibility */
.nav-signin-btn:focus,
.nav-getstarted-btn:focus {
  outline: 3px solid rgba(102, 126, 234, 0.5);
  outline-offset: 2px;
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .navbar {
    background-color: white !important;
    border-bottom: 2px solid #000;
  }

  .brand-text {
    -webkit-text-fill-color: #000;
    color: #000;
  }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .navbar-brand,
  .nav-signin-btn,
  .nav-getstarted-btn {
    transition: none;
  }
}
</style>
