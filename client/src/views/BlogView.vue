<script>
import { useAuthStore } from '@/stores/auth'
import BlogPost from '@/components/BlogPost.vue'
import axios from '@/plugins/axios'

export default {
  name: 'BlogView',
  components: {
    BlogPost
  },
  data() {
    return {
      posts: [],
      categories: [],
      tags: [],
      loading: true,
      error: null,
      filters: {
        category: '',
        tag: '',
        search: ''
      },
      pagination: {
        currentPage: 1,
        lastPage: 1,
        total: 0
      }
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    }
  },
  methods: {
    async fetchPosts(page = 1) {
      try {
        this.loading = true
        const params = {
          page,
          category_id: this.filters.category,
          tag_id: this.filters.tag,
          search: this.filters.search,
          public: true
        }

        const response = await axios.get('/posts', { params })
        this.posts = response.data.data
        this.pagination = {
          currentPage: response.data.current_page,
          lastPage: response.data.last_page,
          total: response.data.total
        }
      } catch (error) {
        this.error = 'Failed to load posts'
        console.error('Error fetching posts:', error)
      } finally {
        this.loading = false
      }
    },
    async fetchCategories() {
      try {
        const response = await axios.get('/categories')
        this.categories = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    },
    async fetchTags() {
      try {
        const response = await axios.get('/tags')
        this.tags = response.data
      } catch (error) {
        console.error('Error fetching tags:', error)
      }
    },
    applyFilters() {
      this.fetchPosts(1)
    },
    clearFilters() {
      this.filters = {
        category: '',
        tag: '',
        search: ''
      }
      this.fetchPosts(1)
    },
    handlePageChange(page) {
      this.fetchPosts(page)
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  },
  created() {
    this.fetchPosts()
    this.fetchCategories()
    this.fetchTags()
  }
}
</script>

<template>
  <div class="blog-view">
    <div class="container py-5">
      <!-- Header -->
      <header class="text-center mb-5">
        <h1 class="display-4 mb-3">Blog Posts</h1>
        <p class="lead text-muted">Discover the latest articles and insights</p>
      </header>

      <!-- Filters -->
      <div class="row mb-4">
        <div class="col-md-12">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-4">
                  <select 
                    v-model="filters.category"
                    class="form-select"
                    @change="applyFilters"
                  >
                    <option value="">All Categories</option>
                    <option 
                      v-for="category in categories"
                      :key="category.id"
                      :value="category.id"
                    >
                      {{ category.name }}
                    </option>
                  </select>
                </div>
                <div class="col-md-4">
                  <select 
                    v-model="filters.tag"
                    class="form-select"
                    @change="applyFilters"
                  >
                    <option value="">All Tags</option>
                    <option 
                      v-for="tag in tags"
                      :key="tag.id"
                      :value="tag.id"
                    >
                      {{ tag.name }}
                    </option>
                  </select>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <input 
                      type="text"
                      class="form-control"
                      placeholder="Search posts..."
                      v-model="filters.search"
                      @keyup.enter="applyFilters"
                    >
                    <button 
                      class="btn btn-primary"
                      @click="applyFilters"
                    >
                      Search
                    </button>
                  </div>
                </div>
              </div>
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

      <!-- Posts Grid -->
      <div v-else class="row">
        <div v-if="posts.length === 0" class="col-12 text-center py-5">
          <h3>No posts found</h3>
          <button class="btn btn-outline-primary mt-3" @click="clearFilters">
            Clear Filters
          </button>
        </div>
        <div v-else v-for="post in posts" :key="post.id" class="col-md-6 col-lg-4">
          <BlogPost :post="post" />
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.lastPage > 1" class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
          <ul class="pagination">
            <li 
              class="page-item"
              :class="{ disabled: pagination.currentPage === 1 }"
            >
              <a 
                class="page-link" 
                href="#"
                @click.prevent="handlePageChange(pagination.currentPage - 1)"
              >
                Previous
              </a>
            </li>
            <li 
              v-for="page in pagination.lastPage"
              :key="page"
              class="page-item"
              :class="{ active: page === pagination.currentPage }"
            >
              <a 
                class="page-link" 
                href="#"
                @click.prevent="handlePageChange(page)"
              >
                {{ page }}
              </a>
            </li>
            <li 
              class="page-item"
              :class="{ disabled: pagination.currentPage === pagination.lastPage }"
            >
              <a 
                class="page-link" 
                href="#"
                @click.prevent="handlePageChange(pagination.currentPage + 1)"
              >
                Next
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<style scoped>
.blog-view {
  min-height: calc(100vh - 56px - 72px); /* Subtracting navbar and footer height */
}

.pagination {
  margin-bottom: 0;
}
</style>
