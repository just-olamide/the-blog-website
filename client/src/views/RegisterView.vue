<script>
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'RegisterView',
  data() {
    return {
      form: {
        name: '',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      error: null,
      loading: false,
    }
  },
  methods: {
    async handleSubmit() {
      if (this.form.password !== this.form.password_confirmation) {
        this.error = 'Passwords do not match'
        return
      }

      this.loading = true
      this.error = null
      const authStore = useAuthStore()

      try {
        await authStore.register(this.form)
        this.$router.push('/login')
      } catch (err) {
        this.error = err.response?.data?.message || 'An error occurred during registration'
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<template>
  <div class="register-page">
    <div class="card">
      <div class="card-body">
        <h2 class="text-center mb-4">Register</h2>

        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" v-model="form.name" required />
          </div>

          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input
              type="text"
              class="form-control"
              id="username"
              v-model="form.username"
              required
            />
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" v-model="form.email" required />
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              class="form-control"
              id="password"
              v-model="form.password"
              required
            />
          </div>

          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input
              type="password"
              class="form-control"
              id="password_confirmation"
              v-model="form.password_confirmation"
              required
            />
          </div>

          <button type="submit" class="btn btn-primary w-100" :disabled="loading">
            {{ loading ? 'Creating account...' : 'Register' }}
          </button>
        </form>

        <p class="text-center mt-3">
          Already have an account?
          <RouterLink to="/login">Login</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  max-width: 400px;
  margin: 40px auto;
  padding: 0 15px;
}

.card {
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.card-body {
  padding: 30px;
}
</style>
