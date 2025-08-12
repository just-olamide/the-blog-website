<script>
import { useAuthStore } from '@/stores/auth'
import { mapState } from 'pinia'

export default {
  name: 'Navbar',
  data() {
    return {
      isMenuOpen: false,
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user', 'isAuthenticated']),
  },
  methods: {
    async logout() {
      const authStore = useAuthStore()
      try {
        await authStore.logout()
        this.$router.push('/login')
      } catch (error) {
        console.error('Logout error:', error)
      }
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
          <svg
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"
              fill="white"
            />
            <path
              d="M19 15L19.74 12.26L22.5 11.5L19.74 10.74L19 8L18.26 10.74L15.5 11.5L18.26 12.26L19 15Z"
              fill="white"
            />
            <path
              d="M5 15L5.74 12.26L8.5 11.5L5.74 10.74L5 8L4.26 10.74L1.5 11.5L4.26 12.26L5 15Z"
              fill="white"
            />
          </svg>
        </div>
        <span class="brand-text">
          <span class="blog-text">Blog</span><span class="space-text">Space</span>
        </span>
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
          <!-- Unauthenticated state -->
          <template v-if="!isAuthenticated">
            <RouterLink to="/login" class="btn btn-link nav-signin-btn me-lg-3 mb-2 mb-lg-0">
              Sign In
            </RouterLink>
            <RouterLink to="/register" class="btn btn-primary nav-getstarted-btn">
              Get Started
            </RouterLink>
          </template>

          <!-- Authenticated state -->
          <template v-else>
            <!-- Navigation Links -->
            <div class="nav-links me-lg-4 mb-2 mb-lg-0">
              <RouterLink to="/" class="nav-link">
                <svg
                  class="nav-icon"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M9 22V12H15V22"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                Feed
              </RouterLink>
              <RouterLink to="/create-post" class="nav-link">
                <svg
                  class="nav-icon"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                Write
              </RouterLink>
              <RouterLink to="/profile" class="nav-link">
                <svg
                  class="nav-icon"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <circle
                    cx="12"
                    cy="7"
                    r="4"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                Profile
              </RouterLink>
            </div>

            <!-- User Profile Section -->
            <div class="user-profile">
              <div class="user-avatar">
                <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name || 'User'" />
                <div v-else class="avatar-placeholder">
                  {{ (user?.name || 'U').charAt(0).toUpperCase() }}
                </div>
              </div>
              <span class="username">{{ user?.username || user?.name || 'User' }}</span>
              <button @click="logout" class="logout-btn">
                <svg
                  class="logout-icon"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M16 17L21 12L16 7"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M21 12H9"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                Logout
              </button>
            </div>
          </template>
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
  font-weight: 700;
  font-size: 1.5rem;
}

.blog-text {
  color: #334155;
}

.space-text {
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
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

/* Navigation Links for Authenticated Users */
.nav-links {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b !important;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 0;
  transition: all 0.3s ease;
  border-radius: 8px;
}

.nav-link:hover {
  color: #334155 !important;
  background-color: rgba(0, 0, 0, 0.05);
  padding: 0.5rem 0.75rem;
}

.nav-link.router-link-active {
  color: #667eea !important;
  background-color: rgba(102, 126, 234, 0.1);
  padding: 0.5rem 0.75rem;
}

.nav-icon {
  flex-shrink: 0;
}

/* User Profile Section */
.user-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 12px;
  background-color: rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(0, 0, 0, 0.08);
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
}

.username {
  font-weight: 500;
  color: #334155;
  font-size: 0.9rem;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  background: none;
  border: none;
  color: #ef4444;
  font-size: 0.8rem;
  font-weight: 500;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
}

.logout-btn:hover {
  background-color: rgba(239, 68, 68, 0.1);
  color: #dc2626;
}

.logout-icon {
  flex-shrink: 0;
}

/* Mobile Responsive for Authenticated State */
@media (max-width: 991.98px) {
  .nav-links {
    flex-direction: column;
    gap: 1rem;
    width: 100%;
    margin-bottom: 1rem;
  }

  .nav-link {
    width: 100%;
    justify-content: flex-start;
    padding: 0.75rem 1rem;
  }

  .user-profile {
    width: 100%;
    justify-content: center;
    padding: 1rem;
  }
}
</style>
