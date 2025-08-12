<script>
import { useAuthStore } from '@/stores/auth'
import axios from '@/plugins/axios'

export default {
  name: 'DashboardView',
  data() {
    return {
      posts: [],
      loading: true,
      error: null,
      stats: {
        total: 0,
        published: 0,
        drafts: 0,
        totalViews: 0,
        totalLikes: 0
      },
      filters: {
        status: 'all',
        search: ''
      }
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    },
    filteredPosts() {
      return this.posts.filter(post => {
        const matchesStatus = this.filters.status === 'all' || post.status === this.filters.status
        const matchesSearch = !this.filters.search || 
          post.title.toLowerCase().includes(this.filters.search.toLowerCase())
        return matchesStatus && matchesSearch
      })
    }
  },
  methods: {
    async fetchPosts() {
      try {
        this.loading = true
        const response = await axios.get('/posts', {
          params: { user_posts: true }
        })
        this.posts = response.data.data
        this.calculateStats()
      } catch (error) {
        this.error = 'Failed to load posts'
        console.error('Error fetching posts:', error)
      } finally {
        this.loading = false
      }
    },
    calculateStats() {
      this.stats = this.posts.reduce((acc, post) => {
        acc.total++
        acc.totalViews += post.view_count
        acc.totalLikes += post.like_count
        if (post.status === 'published') {
          acc.published++
        } else {
          acc.drafts++
        }
        return acc
      }, {
        total: 0,
        published: 0,
        drafts: 0,
        totalViews: 0,
        totalLikes: 0
      })
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    async deletePost(post) {
      if (!confirm('Are you sure you want to delete this post?')) return

      try {
        await axios.delete(`/posts/${post.id}`)
        this.posts = this.posts.filter(p => p.id !== post.id)
        this.calculateStats()
      } catch (error) {
        console.error('Error deleting post:', error)
      }
    }
  },
  created() {
    this.fetchPosts()
  }
}
</script>

<template>
  <div class="dashboard">
    <div class="container py-5">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dashboard</h1>
        <RouterLink to="/posts/create" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> New Post
        </RouterLink>
      </div>

      <!-- Stats Cards -->
      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5 class="card-title">Total Posts</h5>
              <p class="card-text display-6">{{ stats.total }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5 class="card-title">Published</h5>
              <p class="card-text display-6">{{ stats.published }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning">
            <div class="card-body">
              <h5 class="card-title">Drafts</h5>
              <p class="card-text display-6">{{ stats.drafts }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h5 class="card-title">Total Views</h5>
              <p class="card-text display-6">{{ stats.totalViews }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <select v-model="filters.status" class="form-select">
                <option value="all">All Posts</option>
                <option value="published">Published</option>
                <option value="draft">Drafts</option>
              </select>
            </div>
            <div class="col-md-6">
              <input
                type="text"
                class="form-control"
                v-model="filters.search"
                placeholder="Search posts..."
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="alert alert-danger" role="alert">
        {{ error }}
      </div>

      <!-- Posts Table -->
      <div v-else class="card shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Views</th>
                <th>Likes</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredPosts.length === 0">
                <td colspan="6" class="text-center py-4">
                  No posts found
                </td>
              </tr>
              <tr v-for="post in filteredPosts" :key="post.id">
                <td>
                  <RouterLink 
                    :to="{ name: 'post', params: { slug: post.slug }}"
                    class="text-decoration-none"
                  >
                    {{ post.title }}
                  </RouterLink>
                </td>
                <td>
                  <span 
                    class="badge"
                    :class="post.status === 'published' ? 'bg-success' : 'bg-warning'"
                  >
                    {{ post.status }}
                  </span>
                </td>
                <td>{{ post.view_count }}</td>
                <td>{{ post.like_count }}</td>
                <td>{{ formatDate(post.created_at) }}</td>
                <td>
                  <div class="btn-group">
                    <RouterLink
                      :to="{ name: 'edit-post', params: { id: post.id }}"
                      class="btn btn-sm btn-outline-primary"
                    >
                      <i class="bi bi-pencil"></i>
                    </RouterLink>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="deletePost(post)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  min-height: calc(100vh - 56px - 72px);
}

.card {
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-2px);
}
</style>
