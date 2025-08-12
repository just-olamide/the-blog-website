<script>
import axios from '@/plugins/axios'
import { useAuthStore } from '@/stores/auth'
import { marked } from 'marked'

export default {
  name: 'PostForm',
  props: {
    post: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: {
        title: '',
        content: '',
        category_id: '',
        tags: [],
        featured_image: null,
        status: 'draft'
      },
      categories: [],
      availableTags: [],
      preview: false,
      loading: false,
      error: null,
      imagePreview: null
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    },
    isEdit() {
      return !!this.post
    },
    markdownPreview() {
      return marked(this.form.content)
    }
  },
  methods: {
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
        this.availableTags = response.data
      } catch (error) {
        console.error('Error fetching tags:', error)
      }
    },
    handleImageChange(event) {
      const file = event.target.files[0]
      if (!file) return

      if (file.size > 2 * 1024 * 1024) {
        this.error = 'Image size should not exceed 2MB'
        event.target.value = ''
        return
      }

      this.form.featured_image = file
      this.imagePreview = URL.createObjectURL(file)
    },
    async handleSubmit() {
      this.loading = true
      this.error = null

      try {
        const formData = new FormData()
        Object.keys(this.form).forEach(key => {
          if (key === 'tags') {
            formData.append(key, JSON.stringify(this.form[key]))
          } else if (key === 'featured_image' && this.form[key]) {
            formData.append(key, this.form[key])
          } else {
            formData.append(key, this.form[key])
          }
        })

        const endpoint = this.isEdit ? `/posts/${this.post.id}` : '/posts'
        const method = this.isEdit ? 'put' : 'post'
        
        await axios({
          method,
          url: endpoint,
          data: formData,
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        this.$router.push('/dashboard')
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while saving the post'
        console.error('Error submitting post:', error)
      } finally {
        this.loading = false
      }
    }
  },
  created() {
    if (this.post) {
      this.form = {
        title: this.post.title,
        content: this.post.content,
        category_id: this.post.category_id,
        tags: this.post.tags.map(tag => tag.id),
        status: this.post.status,
        featured_image: null
      }
      if (this.post.featured_image) {
        this.imagePreview = `http://localhost:8000/storage/${this.post.featured_image}`
      }
    }
    this.fetchCategories()
    this.fetchTags()
  }
}
</script>

<template>
  <div class="post-form">
    <div class="card shadow-sm">
      <div class="card-body">
        <h2 class="card-title mb-4">
          {{ isEdit ? 'Edit Post' : 'Create New Post' }}
        </h2>

        <!-- Error Alert -->
        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <form @submit.prevent="handleSubmit">
          <!-- Title -->
          <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input
              type="text"
              class="form-control"
              id="title"
              v-model="form.title"
              required
            >
          </div>

          <!-- Content -->
          <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <div class="d-flex gap-2 mb-2">
              <button
                type="button"
                class="btn btn-outline-secondary btn-sm"
                @click="preview = !preview"
              >
                {{ preview ? 'Edit' : 'Preview' }}
              </button>
            </div>
            <template v-if="!preview">
              <textarea
                class="form-control"
                id="content"
                v-model="form.content"
                rows="10"
                required
              ></textarea>
              <small class="text-muted">
                Supports Markdown formatting
              </small>
            </template>
            <div v-else class="border rounded p-3 bg-light">
              <div v-html="markdownPreview"></div>
            </div>
          </div>

          <!-- Category -->
          <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select
              class="form-select"
              id="category"
              v-model="form.category_id"
              required
            >
              <option value="">Select a category</option>
              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>

          <!-- Tags -->
          <div class="mb-3">
            <label class="form-label">Tags</label>
            <div class="tags-container">
              <div class="form-check form-check-inline" 
                   v-for="tag in availableTags" 
                   :key="tag.id">
                <input
                  class="form-check-input"
                  type="checkbox"
                  :id="'tag-' + tag.id"
                  :value="tag.id"
                  v-model="form.tags"
                >
                <label class="form-check-label" :for="'tag-' + tag.id">
                  {{ tag.name }}
                </label>
              </div>
            </div>
          </div>

          <!-- Featured Image -->
          <div class="mb-3">
            <label for="featured_image" class="form-label">Featured Image</label>
            <input
              type="file"
              class="form-control"
              id="featured_image"
              accept="image/*"
              @change="handleImageChange"
            >
            <small class="text-muted d-block">
              Maximum file size: 2MB
            </small>
            <div v-if="imagePreview" class="mt-2">
              <img
                :src="imagePreview"
                alt="Preview"
                class="img-thumbnail"
                style="max-height: 200px"
              >
            </div>
          </div>

          <!-- Status -->
          <div class="mb-4">
            <label class="form-label">Status</label>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                id="draft"
                value="draft"
                v-model="form.status"
              >
              <label class="form-check-label" for="draft">
                Save as Draft
              </label>
            </div>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                id="published"
                value="published"
                v-model="form.status"
              >
              <label class="form-check-label" for="published">
                Publish Now
              </label>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="d-flex gap-2">
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="loading"
            >
              {{ loading ? 'Saving...' : (isEdit ? 'Update Post' : 'Create Post') }}
            </button>
            <RouterLink
              to="/dashboard"
              class="btn btn-outline-secondary"
            >
              Cancel
            </RouterLink>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.post-form {
  max-width: 800px;
  margin: 0 auto;
}

.tags-container {
  max-height: 150px;
  overflow-y: auto;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  padding: 0.5rem;
}
</style>
