<script>
import { useAuthStore } from '@/stores/auth'
import axios from '@/plugins/axios'
import CommentComponent from '@/components/CommentComponent.vue'

export default {
  name: 'PostView',
  components: {
    CommentComponent
  },
  data() {
    return {
      post: null,
      comments: [],
      loading: true,
      error: null,
      newComment: '',
      submitting: false
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    },
    currentUserId() {
      return this.authStore.user?.id
    },
    formattedDate() {
      if (!this.post) return ''
      return new Date(this.post.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
  },
  methods: {
    async fetchPost() {
      try {
        this.loading = true
        const response = await axios.get(`/posts/${this.$route.params.slug}`)
        this.post = response.data
        await this.fetchComments()
      } catch (error) {
        this.error = 'Failed to load post'
        console.error('Error fetching post:', error)
      } finally {
        this.loading = false
      }
    },
    async fetchComments() {
      try {
        const response = await axios.get(`/posts/${this.post.id}/comments`)
        this.comments = response.data
      } catch (error) {
        console.error('Error fetching comments:', error)
      }
    },
    async submitComment() {
      if (!this.newComment.trim()) return

      this.submitting = true
      try {
        await axios.post(`/posts/${this.post.id}/comments`, {
          content: this.newComment
        })
        this.newComment = ''
        await this.fetchComments()
      } catch (error) {
        console.error('Error submitting comment:', error)
      } finally {
        this.submitting = false
      }
    },
    async handleCommentReply(data) {
      try {
        await axios.post(`/posts/${this.post.id}/comments`, {
          content: data.content,
          parent_id: data.parentId
        })
        await this.fetchComments()
      } catch (error) {
        console.error('Error submitting reply:', error)
      }
    },
    async handleCommentUpdate(data) {
      try {
        await axios.put(`/comments/${data.id}`, {
          content: data.content
        })
        await this.fetchComments()
      } catch (error) {
        console.error('Error updating comment:', error)
      }
    },
    async handleCommentDelete(commentId) {
      try {
        await axios.delete(`/comments/${commentId}`)
        await this.fetchComments()
      } catch (error) {
        console.error('Error deleting comment:', error)
      }
    },
    async handleLike() {
      try {
        if (!this.authStore.isLoggedIn) {
          this.$router.push('/login')
          return
        }

        const endpoint = this.post.liked_by_user ? 'unlike' : 'like'
        const response = await axios.post(`/posts/${this.post.id}/${endpoint}`)
        this.post.like_count = response.data.likes
        this.post.liked_by_user = !this.post.liked_by_user
      } catch (error) {
        console.error('Error handling like:', error)
      }
    }
  },
  created() {
    this.fetchPost()
  }
}
</script>

<template>
  <div class="post-view">
    <div class="container py-5">
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

      <!-- Post Content -->
      <template v-else>
        <article class="blog-post">
          <!-- Post Header -->
          <header class="mb-4">
            <div class="post-meta mb-3">
              <span class="badge bg-primary me-2">{{ post.category.name }}</span>
              <span class="text-muted">{{ formattedDate }}</span>
            </div>
            <h1 class="display-4 mb-3">{{ post.title }}</h1>
            <div class="d-flex align-items-center">
              <img 
                :src="post.user.avatar || 'https://via.placeholder.com/40'"
                :alt="post.user.name"
                class="rounded-circle me-2"
                width="40"
                height="40"
              >
              <div>
                <div class="fw-bold">{{ post.user.name }}</div>
                <small class="text-muted">{{ post.user.bio || 'Author' }}</small>
              </div>
            </div>
          </header>

          <!-- Featured Image -->
          <div v-if="post.featured_image" class="featured-image mb-4">
            <img 
              :src="`http://localhost:8000/storage/${post.featured_image}`"
              :alt="post.title"
              class="img-fluid rounded"
            >
          </div>

          <!-- Post Content -->
          <div class="post-content mb-4">
            {{ post.content }}
          </div>

          <!-- Tags -->
          <div v-if="post.tags.length" class="post-tags mb-4">
            <span 
              v-for="tag in post.tags" 
              :key="tag.id"
              class="badge bg-secondary me-2"
            >
              #{{ tag.name }}
            </span>
          </div>

          <!-- Post Actions -->
          <div class="post-actions d-flex align-items-center mb-5">
            <button 
              class="btn btn-outline-primary me-3"
              @click="handleLike"
            >
              <i :class="['bi', post.liked_by_user ? 'bi-heart-fill' : 'bi-heart']"></i>
              {{ post.like_count }} likes
            </button>
            <button 
              class="btn btn-outline-secondary"
              @click="$refs.commentInput?.focus()"
            >
              <i class="bi bi-chat"></i>
              {{ comments.length }} comments
            </button>
          </div>

          <!-- Comments Section -->
          <section class="comments-section">
            <h3 class="mb-4">Comments</h3>

            <!-- New Comment Form -->
            <div v-if="authStore.isLoggedIn" class="mb-4">
              <textarea
                v-model="newComment"
                class="form-control mb-2"
                rows="3"
                placeholder="Write your comment..."
                ref="commentInput"
              ></textarea>
              <button
                class="btn btn-primary"
                @click="submitComment"
                :disabled="submitting"
              >
                {{ submitting ? 'Submitting...' : 'Submit Comment' }}
              </button>
            </div>
            <div v-else class="alert alert-info">
              Please <RouterLink to="/login">login</RouterLink> to leave a comment.
            </div>

            <!-- Comments List -->
            <div class="comments-list">
              <CommentComponent
                v-for="comment in comments"
                :key="comment.id"
                :comment="comment"
                :current-user-id="currentUserId"
                @reply="handleCommentReply"
                @update="handleCommentUpdate"
                @delete="handleCommentDelete"
              />
            </div>
          </section>
        </article>
      </template>
    </div>
  </div>
</template>

<style scoped>
.post-view {
  min-height: calc(100vh - 56px - 72px);
}

.featured-image {
  max-height: 500px;
  overflow: hidden;
}

.featured-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.post-content {
  font-size: 1.1rem;
  line-height: 1.8;
}

.comments-section {
  border-top: 1px solid #dee2e6;
  padding-top: 2rem;
}
</style>
