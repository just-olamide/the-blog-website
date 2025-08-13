<script>
import axios from '@/plugins/axios'

export default {
  name: 'EditPostView',
  data() {
    return {
      post: null,
      editedPost: {
        title: '',
        content: '',
        category_id: '',
        status: 'draft',
        featured_image: null,
      },
      categories: [],
      loading: true,
      saving: false,
      error: null,
      hasChanges: false,
      imagePreview: null,
      newImage: null,
      isMounted: false,
    }
  },
  methods: {
    async fetchPost() {
      try {
        const response = await axios.get(`/posts/${this.$route.params.slug}`)

        if (!this.isMounted) return

        this.post = response.data

        // Initialize edited post with current data
        this.editedPost = {
          title: this.post.title,
          content: this.post.content,
          category_id: this.post.category_id,
          status: this.post.status,
          featured_image: this.post.featured_image,
        }
      } catch (error) {
        if (!this.isMounted) return

        this.error = 'Failed to load post'
        console.error('Error fetching post:', error)
      } finally {
        if (this.isMounted) {
          this.loading = false
        }
      }
    },

    async fetchCategories() {
      try {
        const response = await axios.get('/categories')

        if (!this.isMounted) return

        this.categories = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    },

    goBack() {
      if (this.hasChanges) {
        if (confirm('You have unsaved changes. Are you sure you want to leave?')) {
          this.$router.go(-1)
        }
      } else {
        this.$router.go(-1)
      }
    },

    previewPost() {
      // Navigate to post view for preview
      this.$router.push(`/blog/${this.post.slug}`)
    },

    async deletePost() {
      if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        try {
          await axios.delete(`/posts/${this.post.slug}`)
          this.$router.push('/profile')
        } catch (error) {
          alert('Failed to delete post')
          console.error('Error deleting post:', error)
        }
      }
    },

    formatText(command) {
      document.execCommand(command, false, null)
      if (this.$refs.contentEditor) {
        this.$refs.contentEditor.focus()
      }
    },

    insertLink() {
      const url = prompt('Enter URL:')
      if (url) {
        document.execCommand('createLink', false, url)
        if (this.$refs.contentEditor) {
          this.$refs.contentEditor.focus()
        }
      }
    },

    handleImageUpload(event) {
      const file = event.target.files[0]
      if (file) {
        this.newImage = file
        this.hasChanges = true

        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
          this.imagePreview = e.target.result
        }
        reader.readAsDataURL(file)
      }
    },

    removeImage() {
      this.editedPost.featured_image = null
      this.imagePreview = null
      this.newImage = null
      this.hasChanges = true
    },

    async saveDraft() {
      this.editedPost.status = 'draft'
      await this.savePost()
    },

    async updatePost() {
      this.editedPost.status = 'published'
      await this.savePost()
    },

    async savePost() {
      if (!this.editedPost.title.trim()) {
        alert('Please enter a post title')
        return
      }

      if (!this.editedPost.content.trim()) {
        alert('Please enter post content')
        return
      }

      this.saving = true
      try {
        const formData = new FormData()
        formData.append('title', this.editedPost.title)
        formData.append('content', this.editedPost.content)
        formData.append('category_id', this.editedPost.category_id)
        formData.append('status', this.editedPost.status)
        formData.append('_method', 'PUT')

        if (this.newImage) {
          formData.append('featured_image', this.newImage)
        } else if (this.editedPost.featured_image === null) {
          formData.append('remove_image', '1')
        }

        const response = await axios.put(`/posts/${this.post.slug}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })

        this.hasChanges = false
        alert(
          `Post ${this.editedPost.status === 'published' ? 'updated and published' : 'saved as draft'} successfully!`,
        )

        // Redirect to post view if published, otherwise to profile
        if (this.editedPost.status === 'published') {
          this.$router.push(`/blog/${response.data.slug}`)
        } else {
          this.$router.push('/profile')
        }
      } catch (error) {
        alert('Failed to save post')
        console.error('Error saving post:', error)
      } finally {
        this.saving = false
      }
    },
  },

  mounted() {
    this.isMounted = true
  },

  beforeUnmount() {
    this.isMounted = false
  },

  async created() {
    await Promise.all([this.fetchPost(), this.fetchCategories()])
  },

  beforeRouteLeave(to, from, next) {
    if (this.hasChanges) {
      if (confirm('You have unsaved changes. Are you sure you want to leave?')) {
        next()
      } else {
        next(false)
      }
    } else {
      next()
    }
  },
}
</script>

<template>
  <div class="edit-post-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading post...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-message">
        <svg
          width="48"
          height="48"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <circle cx="12" cy="12" r="10" stroke="#ef4444" stroke-width="2" />
          <line x1="15" y1="9" x2="9" y2="15" stroke="#ef4444" stroke-width="2" />
          <line x1="9" y1="9" x2="15" y2="15" stroke="#ef4444" stroke-width="2" />
        </svg>
        <h3>{{ error }}</h3>
        <button @click="$router.go(-1)" class="back-btn">Go Back</button>
      </div>
    </div>

    <!-- Edit Post Form -->
    <div v-else-if="post" class="edit-container">
      <!-- Header -->
      <div class="edit-header">
        <div class="header-left">
          <button @click="goBack" class="back-button">
            <svg
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M19 12H5"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <path
                d="M12 19L5 12L12 5"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            Back to Feed
          </button>
          <h1 class="page-title">Edit Post</h1>
        </div>
        <div class="header-right">
          <button @click="previewPost" class="preview-btn">
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8S1 12 1 12z"
                stroke="currentColor"
                stroke-width="2"
              />
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
            </svg>
            Preview
          </button>
          <button @click="deletePost" class="delete-btn">
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <polyline
                points="3,6 5,6 21,6"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <path
                d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            Delete
          </button>
        </div>
      </div>

      <!-- Edit Form -->
      <div class="edit-form">
        <!-- Title Editor -->
        <div class="title-section">
          <input
            v-model="editedPost.title"
            class="title-input"
            placeholder="Post title..."
            @input="hasChanges = true"
          />
        </div>

        <!-- Content Editor -->
        <div class="content-section">
          <div class="editor-toolbar">
            <button @click="formatText('bold')" class="format-btn" title="Bold">
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"
                  stroke="currentColor"
                  stroke-width="2"
                />
                <path
                  d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"
                  stroke="currentColor"
                  stroke-width="2"
                />
              </svg>
            </button>
            <button @click="formatText('italic')" class="format-btn" title="Italic">
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <line x1="19" y1="4" x2="10" y2="4" stroke="currentColor" stroke-width="2" />
                <line x1="14" y1="20" x2="5" y2="20" stroke="currentColor" stroke-width="2" />
                <line x1="15" y1="4" x2="9" y2="20" stroke="currentColor" stroke-width="2" />
              </svg>
            </button>
            <div class="toolbar-divider"></div>
            <button @click="insertLink" class="format-btn" title="Link">
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"
                  stroke="currentColor"
                  stroke-width="2"
                />
                <path
                  d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"
                  stroke="currentColor"
                  stroke-width="2"
                />
              </svg>
            </button>
          </div>
          <textarea
            ref="contentEditor"
            v-model="editedPost.content"
            class="content-input"
            placeholder="Write your content here..."
            @input="hasChanges = true"
            rows="15"
          ></textarea>
        </div>

        <!-- Category and Status -->
        <div class="meta-section">
          <div class="form-row">
            <div class="form-group">
              <label for="category">Category</label>
              <select
                v-model="editedPost.category_id"
                id="category"
                class="form-select"
                @change="hasChanges = true"
              >
                <option value="">Select a category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select
                v-model="editedPost.status"
                id="status"
                class="form-select"
                @change="hasChanges = true"
              >
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Featured Image -->
        <div class="image-section">
          <label class="section-label">Featured Image</label>
          <div class="image-upload-area">
            <div v-if="editedPost.featured_image || imagePreview" class="current-image">
              <img
                :src="imagePreview || `http://localhost:8000/storage/${editedPost.featured_image}`"
                alt="Featured image"
              />
              <button @click="removeImage" class="remove-image-btn">
                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" />
                  <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" />
                </svg>
              </button>
            </div>
            <div v-else class="upload-placeholder" @click="$refs.imageInput.click()">
              <svg
                width="48"
                height="48"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <rect
                  x="3"
                  y="3"
                  width="18"
                  height="18"
                  rx="2"
                  ry="2"
                  stroke="currentColor"
                  stroke-width="2"
                />
                <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                <polyline points="21,15 16,10 5,21" stroke="currentColor" stroke-width="2" />
              </svg>
              <p>Click to upload featured image</p>
            </div>
            <input
              ref="imageInput"
              type="file"
              accept="image/*"
              @change="handleImageUpload"
              style="display: none"
            />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <button @click="saveDraft" class="save-draft-btn" :disabled="saving">
            {{ saving ? 'Saving...' : 'Save Draft' }}
          </button>
          <button @click="updatePost" class="update-btn" :disabled="saving">
            {{ saving ? 'Updating...' : 'Update Post' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.edit-post-page {
  min-height: 100vh;
  background-color: #f8fafc;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
  gap: 16px;
}

.loading-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e2e8f0;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.error-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
}

.error-message {
  text-align: center;
  padding: 32px;
}

.error-message h3 {
  color: #ef4444;
  margin: 16px 0;
}

.back-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 16px;
}

.edit-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
}

.edit-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 32px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 24px;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  font-size: 14px;
  padding: 8px 12px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.back-button:hover {
  background: #f1f5f9;
  color: #374151;
}

.page-title {
  font-size: 24px;
  font-weight: 700;
  color: #1a202c;
  margin: 0;
}

.header-right {
  display: flex;
  gap: 12px;
}

.preview-btn,
.delete-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  border: 1px solid #e2e8f0;
  background: white;
  color: #6b7280;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.preview-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
}

.delete-btn {
  color: #ef4444;
  border-color: #fecaca;
}

.delete-btn:hover {
  background: #fef2f2;
  border-color: #ef4444;
}

.edit-form {
  background: white;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.title-section {
  margin-bottom: 32px;
}

.title-input {
  width: 100%;
  border: none;
  font-size: 32px;
  font-weight: 700;
  color: #1a202c;
  background: transparent;
  padding: 12px 0;
  outline: none;
}

.title-input::placeholder {
  color: #9ca3af;
}

.content-section {
  margin-bottom: 32px;
}

.editor-toolbar {
  display: flex;
  gap: 8px;
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-bottom: none;
  border-radius: 8px 8px 0 0;
  background: #f8fafc;
}

.format-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  color: #6b7280;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.format-btn:hover {
  background: #e2e8f0;
  color: #374151;
}

.toolbar-divider {
  width: 1px;
  height: 20px;
  background: #e2e8f0;
  margin: 6px 4px;
}

.content-input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-top: none;
  border-radius: 0 0 8px 8px;
  padding: 16px;
  font-size: 16px;
  line-height: 1.6;
  resize: vertical;
  min-height: 300px;
  outline: none;
  font-family: inherit;
}

.content-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.meta-section {
  margin-bottom: 32px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  font-size: 14px;
}

.form-select {
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 12px;
  font-size: 14px;
  background: white;
  outline: none;
  transition: border-color 0.2s ease;
}

.form-select:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.image-section {
  margin-bottom: 32px;
}

.section-label {
  display: block;
  font-weight: 600;
  color: #374151;
  font-size: 14px;
  margin-bottom: 12px;
}

.image-upload-area {
  border: 2px dashed #e2e8f0;
  border-radius: 8px;
  padding: 24px;
  text-align: center;
  transition: border-color 0.2s ease;
}

.image-upload-area:hover {
  border-color: #3b82f6;
}

.current-image {
  position: relative;
  display: inline-block;
}

.current-image img {
  max-width: 200px;
  max-height: 200px;
  border-radius: 8px;
  object-fit: cover;
}

.remove-image-btn {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 24px;
  height: 24px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-placeholder {
  cursor: pointer;
  color: #6b7280;
  padding: 32px;
}

.upload-placeholder:hover {
  color: #3b82f6;
}

.upload-placeholder svg {
  color: #9ca3af;
  margin-bottom: 12px;
}

.action-buttons {
  display: flex;
  gap: 16px;
  justify-content: flex-end;
  padding-top: 24px;
  border-top: 1px solid #f1f5f9;
}

.save-draft-btn,
.update-btn {
  padding: 12px 24px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.save-draft-btn {
  background: #f1f5f9;
  color: #374151;
}

.save-draft-btn:hover:not(:disabled) {
  background: #e2e8f0;
}

.update-btn {
  background: #3b82f6;
  color: white;
}

.update-btn:hover:not(:disabled) {
  background: #2563eb;
}

.save-draft-btn:disabled,
.update-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .edit-container {
    padding: 0 16px;
  }

  .edit-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }

  .header-right {
    align-self: flex-end;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .title-input {
    font-size: 24px;
  }

  .action-buttons {
    flex-direction: column;
  }
}
</style>
