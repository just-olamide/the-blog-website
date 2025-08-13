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
        totalLikes: 0,
      },
      filters: {
        status: 'all',
        search: '',
      },
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    },
    filteredPosts() {
      return this.posts.filter((post) => {
        const matchesStatus = this.filters.status === 'all' || post.status === this.filters.status
        const matchesSearch =
          !this.filters.search ||
          post.title.toLowerCase().includes(this.filters.search.toLowerCase())
        return matchesStatus && matchesSearch
      })
    },
  },
  methods: {
    async fetchPosts() {
      try {
        this.loading = true
        const response = await axios.get('/posts', {
          params: { user_posts: true },
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
      this.stats = this.posts.reduce(
        (acc, post) => {
          acc.total++
          acc.totalViews += post.view_count
          acc.totalLikes += post.like_count
          if (post.status === 'published') {
            acc.published++
          } else {
            acc.drafts++
          }
          return acc
        },
        {
          total: 0,
          published: 0,
          drafts: 0,
          totalViews: 0,
          totalLikes: 0,
        },
      )
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    },
    async deletePost(post) {
      if (!confirm('Are you sure you want to delete this post?')) return

      try {
        await axios.delete(`/posts/${post.id}`)
        this.posts = this.posts.filter((p) => p.id !== post.id)
        this.calculateStats()
      } catch (error) {
        console.error('Error deleting post:', error)
      }
    },
  },
  created() {
    this.fetchPosts()
  },
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
              />
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

      <!-- Posts Table or Empty State -->
      <div v-else>
        <!-- Empty state when user has no posts -->
        <div v-if="posts.length === 0" class="empty-card">
          <div class="empty-content">
            <div class="empty-icon">
              <svg
                width="36"
                height="36"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path d="M12 20H21" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" />
                <path
                  d="M16.5 3.5L20.5 7.5L8 20H4V16L16.5 3.5Z"
                  stroke="#94a3b8"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
            <h3 class="empty-title">No Posts Yet</h3>
            <p class="empty-subtitle">Share your first story with the BlogSpace community!</p>
            <RouterLink to="/posts/create" class="btn btn-primary empty-cta"
              >Create Your First Post</RouterLink
            >
          </div>
        </div>

        <!-- Posts table when there are posts (with filters applied) -->
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
                  <td colspan="6" class="text-center py-4">No posts match your filters</td>
                </tr>
                <tr v-for="post in filteredPosts" :key="post.id">
                  <td>
                    <RouterLink
                      :to="{ name: 'post', params: { slug: post.slug } }"
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
                        :to="{ name: 'edit-post', params: { id: post.id } }"
                        class="btn btn-sm btn-outline-primary"
                      >
                        <i class="bi bi-pencil"></i>
                      </RouterLink>
                      <button class="btn btn-sm btn-outline-danger" @click="deletePost(post)">
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

/* Empty state */
.empty-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 48px 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-content {
  text-align: center;
}
.empty-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}
.empty-title {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 8px 0;
}
.empty-subtitle {
  color: #64748b;
  margin-bottom: 16px;
}
.empty-cta {
  padding: 10px 16px;
}
</style>
