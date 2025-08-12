<template>
  <div class="container mt-5">
    <div class="jumbotron text-center">
      <h1 class="display-4">Welcome to Our Blog Platform</h1>
      <p class="lead">Share your thoughts, ideas, and stories with the world.</p>
      <hr class="my-4">
      <p>Join our community of writers and readers to explore diverse perspectives and engaging content.</p>
      <RouterLink to="/blog" class="btn btn-primary btn-lg">Read Blog Posts</RouterLink>
      <RouterLink v-if="!isAuthenticated" to="/register" class="btn btn-outline-primary btn-lg ms-2">Get Started</RouterLink>
    </div>

    <div class="row mt-5">
      <div class="col-md-4">
        <h3>Share Your Story</h3>
        <p>Create an account and start sharing your unique perspective with our growing community.</p>
      </div>
      <div class="col-md-4">
        <h3>Connect with Others</h3>
        <p>Engage with other writers and readers through comments and discussions.</p>
      </div>
      <div class="col-md-4">
        <h3>Discover Content</h3>
        <p>Explore a wide range of topics and find content that interests you.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

export default {
  setup() {
    const authStore = useAuthStore()
    const { isAuthenticated } = storeToRefs(authStore)

    return {
      isAuthenticated
    }
  }
}
</script>

<style scoped>
.jumbotron {
  background-color: #f8f9fa;
  padding: 4rem 2rem;
  border-radius: 0.3rem;
}
</style>
