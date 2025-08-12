<script>
import PostForm from '@/components/PostForm.vue'
import axios from '@/plugins/axios'

export default {
  name: 'EditPostView',
  components: {
    PostForm
  },
  data() {
    return {
      post: null,
      loading: true,
      error: null
    }
  },
  methods: {
    async fetchPost() {
      try {
        const response = await axios.get(`/posts/${this.$route.params.id}`)
        this.post = response.data
      } catch (error) {
        this.error = 'Failed to load post'
        console.error('Error fetching post:', error)
      } finally {
        this.loading = false
      }
    }
  },
  created() {
    this.fetchPost()
  }
}
</script>

<template>
  <div class="container py-5">
    <!-- Loading State -->
    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <!-- Post Form -->
    <PostForm v-else-if="post" :post="post" />
  </div>
</template>
