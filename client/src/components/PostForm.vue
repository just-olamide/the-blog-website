<script>
import axios from '@/plugins/axios'
import { useAuthStore } from '@/stores/auth'
import { marked } from 'marked'
import Modal from '@/components/Modal.vue'
import Toast from '@/components/Toast.vue'

export default {
  name: 'PostForm',
  components: {
    Modal,
    Toast,
  },
  props: {
    post: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      form: {
        title: '',
        content: '',
        category_id: '',
        featured_image: null,
        status: 'draft',
      },
      categories: [],
      preview: false,
      loading: false,
      error: null,
      imagePreview: null,
      showConfirmModal: false,
      showSuccessToast: false,
      toastMessage: '',
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
    },
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
    handleFormSubmit() {
      // Show confirmation modal before actual submission
      this.showConfirmModal = true
    },
    cancelSubmission() {
      this.showConfirmModal = false
    },
    async confirmSubmission() {
      this.showConfirmModal = false
      await this.handleSubmit()
    },
    async handleSubmit() {
      this.loading = true
      this.error = null

      try {
        const formData = new FormData()
        Object.keys(this.form).forEach((key) => {
          if (key === 'featured_image' && this.form[key]) {
            formData.append(key, this.form[key])
          } else {
            formData.append(key, this.form[key])
          }
        })

        const endpoint = this.isEdit ? `/posts/${this.post.id}` : '/posts'
        const method = this.isEdit ? 'put' : 'post'

        const response = await axios({
          method,
          url: endpoint,
          data: formData,
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })

        // Show success toast
        this.toastMessage = this.isEdit
          ? 'Post updated successfully!'
          : `Post ${this.form.status === 'published' ? 'published' : 'saved as draft'} successfully!`
        this.showSuccessToast = true

        // Wait a moment for the toast to show, then redirect
        setTimeout(() => {
          if (this.isEdit) {
            this.$router.push('/dashboard')
          } else {
            // For new posts, redirect to feeds to show the new post
            this.$router.push('/feeds')
          }
        }, 1500)
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while saving the post'
        console.error('Error submitting post:', error)
      } finally {
        this.loading = false
      }
    },
    closeToast() {
      this.showSuccessToast = false
    },
  },
  created() {
    if (this.post) {
      this.form = {
        title: this.post.title,
        content: this.post.content,
        category_id: this.post.category_id,
        status: this.post.status,
        featured_image: null,
      }
      if (this.post.featured_image) {
        this.imagePreview = `http://localhost:8000/storage/${this.post.featured_image}`
      }
    }
    this.fetchCategories()
  },
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

        <form @submit.prevent="handleFormSubmit">
          <!-- Title -->
          <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" v-model="form.title" required />
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
              <small class="text-muted"> Supports Markdown formatting </small>
            </template>
            <div v-else class="border rounded p-3 bg-light">
              <div v-html="markdownPreview"></div>
            </div>
          </div>

          <!-- Category -->
          <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" v-model="form.category_id" required>
              <option value="">Select a category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
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
            />
            <small class="text-muted d-block"> Maximum file size: 2MB </small>
            <div v-if="imagePreview" class="mt-2">
              <img
                :src="imagePreview"
                alt="Preview"
                class="img-thumbnail"
                style="max-height: 200px"
              />
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
              />
              <label class="form-check-label" for="draft"> Save as Draft </label>
            </div>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                id="published"
                value="published"
                v-model="form.status"
              />
              <label class="form-check-label" for="published"> Publish Now </label>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Saving...' : isEdit ? 'Update Post' : 'Create Post' }}
            </button>
            <RouterLink to="/dashboard" class="btn btn-outline-secondary"> Cancel </RouterLink>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <Modal
      :show="showConfirmModal"
      title="Confirm Post Submission"
      size="md"
      @close="cancelSubmission"
    >
      <div class="confirmation-content">
        <div class="confirmation-icon">
          <svg
            width="48"
            height="48"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M9 12L11 14L15 10"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
              stroke="currentColor"
              stroke-width="2"
            />
          </svg>
        </div>
        <h4 class="confirmation-title">
          {{
            isEdit ? 'Update Post?' : form.status === 'published' ? 'Publish Post?' : 'Save Draft?'
          }}
        </h4>
        <p class="confirmation-message">
          <template v-if="isEdit">
            Are you sure you want to update this post with your changes?
          </template>
          <template v-else-if="form.status === 'published'">
            Your post "{{ form.title }}" will be published and visible to all users. Are you sure
            you want to continue?
          </template>
          <template v-else>
            Your post "{{ form.title }}" will be saved as a draft. You can publish it later from
            your dashboard.
          </template>
        </p>
      </div>

      <template #footer>
        <button
          type="button"
          class="btn btn-secondary"
          @click="cancelSubmission"
          :disabled="loading"
        >
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary"
          @click="confirmSubmission"
          :disabled="loading"
        >
          <span v-if="loading">Processing...</span>
          <span v-else-if="isEdit">Update Post</span>
          <span v-else-if="form.status === 'published'">Yes, Publish</span>
          <span v-else>Save Draft</span>
        </button>
      </template>
    </Modal>

    <!-- Success Toast -->
    <Toast
      :show="showSuccessToast"
      type="success"
      title="Success!"
      :message="toastMessage"
      @close="closeToast"
    />
  </div>
</template>

<style scoped>
.post-form {
  max-width: 800px;
  margin: 0 auto;
}

.confirmation-content {
  text-align: center;
  padding: 20px 0;
}

.confirmation-icon {
  display: flex;
  justify-content: center;
  margin-bottom: 16px;
  color: #10b981;
}

.confirmation-title {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 12px 0;
}

.confirmation-message {
  color: #6b7280;
  line-height: 1.5;
  margin: 0;
}

.btn {
  padding: 10px 20px;
  border-radius: 8px;
  border: 1px solid;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 100px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background-color: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #2563eb;
  border-color: #2563eb;
}

.btn-secondary {
  background-color: white;
  border-color: #d1d5db;
  color: #374151;
}

.btn-secondary:hover:not(:disabled) {
  background-color: #f9fafb;
  border-color: #9ca3af;
}

/* tags UI removed */
</style>
