<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from '@/plugins/axios'
import CommentComponent from '@/components/CommentComponent.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// Reactive data
const post = ref(null)
const comments = ref([])
const loading = ref(true)
const error = ref(null)
const newComment = ref('')
const submitting = ref(false)
const isMounted = ref(false)

// Computed properties
const currentUserId = computed(() => authStore.user?.id)

const formattedDate = computed(() => {
  if (!post.value) return ''
  return new Date(post.value.created_at).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
})

const timeAgo = computed(() => {
  if (!post.value) return ''
  const now = new Date()
  const postDate = new Date(post.value.created_at)
  const diffInSeconds = Math.floor((now - postDate) / 1000)

  if (diffInSeconds < 60) return 'Just now'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} min ago`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
  return `${Math.floor(diffInSeconds / 86400)} days ago`
})

const isOwner = computed(() => post.value?.user_id === currentUserId.value)

// Methods
const fetchPost = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/posts/${route.params.slug}`)

    if (!isMounted.value) return

    post.value = response.data
    await fetchComments()
  } catch (err) {
    if (!isMounted.value) return

    error.value = 'Failed to load post'
    console.error('Error fetching post:', err)
  } finally {
    if (isMounted.value) {
      loading.value = false
    }
  }
}

const fetchComments = async () => {
  try {
    const response = await axios.get(`/posts/${route.params.slug}/comments`)

    if (!isMounted.value) return

    comments.value = response.data
  } catch (err) {
    console.error('Error fetching comments:', err)
  }
}

const submitComment = async () => {
  if (!newComment.value.trim()) return

  submitting.value = true
  try {
    await axios.post(`/posts/${route.params.slug}/comments`, {
      content: newComment.value,
    })

    newComment.value = ''
    await fetchComments()
  } catch (err) {
    console.error('Error submitting comment:', err)
    if (err.response?.status === 401) {
      alert('Please log in to post a comment')
    } else if (err.response?.status === 422) {
      alert('Please check your comment content')
    } else {
      alert('Error posting comment. Please try again.')
    }
  } finally {
    submitting.value = false
  }
}

const handleCommentReply = async (data) => {
  try {
    await axios.post(`/posts/${route.params.slug}/comments`, {
      content: data.content,
      parent_id: data.parentId,
    })
    await fetchComments()
  } catch (err) {
    console.error('Error submitting reply:', err)
  }
}

const handleCommentUpdate = async (data) => {
  try {
    await axios.put(`/comments/${data.id}`, {
      content: data.content,
    })
    await fetchComments()
  } catch (err) {
    console.error('Error updating comment:', err)
  }
}

const handleCommentDelete = async (commentId) => {
  try {
    await axios.delete(`/comments/${commentId}`)
    await fetchComments()
  } catch (err) {
    console.error('Error deleting comment:', err)
  }
}

const handleLike = async () => {
  try {
    if (!authStore.isLoggedIn) {
      router.push('/login')
      return
    }

    const endpoint = post.value.is_liked ? 'unlike' : 'like'
    const response = await axios.post(`/posts/${route.params.slug}/${endpoint}`)

    // Only update frontend state if API call succeeds
    post.value.likes_count = response.data.likes
    post.value.is_liked = response.data.is_liked
  } catch (err) {
    console.error('Error handling like:', err)

    // If we get a 400 error, it means the states are out of sync
    // Refresh the post data to get the correct state
    if (err.response && err.response.status === 400) {
      console.log('Like states out of sync, refreshing post data...')
      if (isMounted.value) {
        await fetchPost()
      }
    }
  }
}

const goBackToFeed = () => {
  router.push('/feeds')
}

const editPost = () => {
  router.push(`/posts/${post.value.slug}/edit`)
}

const scrollToComments = () => {
  const commentsSection = document.querySelector('.comments-section')
  if (commentsSection) {
    commentsSection.scrollIntoView({ behavior: 'smooth' })
  }
}

// Lifecycle hooks
onMounted(() => {
  isMounted.value = true
  fetchPost()
})

onBeforeUnmount(() => {
  isMounted.value = false
})
</script>

<template>
  <div class="post-view">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <p>{{ error }}</p>
    </div>

    <!-- Post Content -->
    <div v-else class="post-container">
      <!-- Header with Back and Edit buttons -->
      <div class="post-header">
        <button @click="goBackToFeed" class="back-button">
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

        <button v-if="isOwner" @click="editPost" class="edit-button">
          <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            <path
              d="M16.5 3.5L20.5 7.5L8 20H4V16L16.5 3.5Z"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
          Edit Post
        </button>
      </div>

      <!-- Post Content -->
      <div class="post-content-wrapper">
        <!-- Author Info -->
        <div class="author-section">
          <div class="author-avatar">
            <img
              v-if="post.user?.avatar"
              :src="post.user.avatar"
              :alt="post.user?.name || 'Author'"
            />
            <div v-else class="avatar-placeholder">
              {{ (post.user?.name || 'A').charAt(0).toUpperCase() }}
            </div>
          </div>
          <div class="author-info">
            <h4 class="author-name">{{ post.user?.name || 'Anonymous' }}</h4>
            <div class="post-meta">
              <span class="post-date">{{ timeAgo }}</span>
            </div>
          </div>
        </div>

        <!-- Post Title -->
        <h1 class="post-title">{{ post.title }}</h1>

        <!-- Post Content -->
        <div class="post-body">
          <p>{{ post.content }}</p>
        </div>

        <!-- Featured Image (if exists) -->
        <div v-if="post.featured_image" class="post-image">
          <img :src="post.featured_image" :alt="post.title" />
        </div>

        <!-- Post Stats -->
        <div class="post-stats">
          <button class="stat-item like-btn" @click="handleLike">
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M20.84 4.61C20.3292 4.099 19.7228 3.69364 19.0554 3.41708C18.3879 3.14052 17.6725 2.99817 16.95 2.99817C16.2275 2.99817 15.5121 3.14052 14.8446 3.41708C14.1772 3.69364 13.5708 4.099 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.5783 8.50903 2.9987 7.05 2.9987C5.59096 2.9987 4.19169 3.5783 3.16 4.61C2.1283 5.6417 1.5487 7.04097 1.5487 8.5C1.5487 9.95903 2.1283 11.3583 3.16 12.39L12 21.23L20.84 12.39C21.351 11.8792 21.7564 11.2728 22.0329 10.6054C22.3095 9.93789 22.4518 9.22249 22.4518 8.5C22.4518 7.77751 22.3095 7.0621 22.0329 6.39464C21.7564 5.72718 21.351 5.12075 20.84 4.61Z"
                :fill="post.is_liked ? '#ef4444' : 'none'"
                :stroke="post.is_liked ? '#ef4444' : '#6b7280'"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            <span>{{ post.likes_count || 0 }} likes</span>
          </button>

          <button class="stat-item comment-btn" @click="scrollToComments">
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z"
                stroke="#6b7280"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            <span>{{ comments.length || 0 }} comments</span>
          </button>
        </div>
      </div>

      <!-- Comments Section -->
      <div class="comments-section" ref="commentsSection">
        <h3 class="comments-title">Comments ({{ comments.length || 0 }})</h3>

        <!-- Add Comment Form -->
        <div v-if="authStore.isLoggedIn" class="add-comment">
          <div class="comment-avatar">
            <img
              v-if="authStore.user?.avatar"
              :src="authStore.user.avatar"
              :alt="authStore.user?.name || 'You'"
            />
            <div v-else class="avatar-placeholder">
              {{ (authStore.user?.name || 'Y').charAt(0).toUpperCase() }}
            </div>
          </div>
          <div class="comment-form">
            <textarea
              v-model="newComment"
              class="comment-input"
              placeholder="Add a thoughtful comment..."
              rows="3"
              ref="commentInput"
            ></textarea>
            <div class="comment-actions">
              <button
                class="comment-submit"
                @click="submitComment"
                :disabled="submitting || !newComment.trim()"
              >
                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M22 2L11 13"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M22 2L15 22L11 13L2 9L22 2Z"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                {{ submitting ? 'Posting...' : 'Comment' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Login prompt for non-authenticated users -->
        <div v-else class="login-prompt">
          <p>
            Please <RouterLink to="/login" class="login-link">log in</RouterLink> to leave a
            comment.
          </p>
        </div>

        <!-- Comments List -->
        <div v-if="comments.length === 0" class="no-comments">
          <svg
            width="64"
            height="64"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
          <p>No comments yet. Be the first to comment!</p>
        </div>

        <div v-else class="comments-list">
          <CommentComponent
            v-for="comment in comments"
            :key="comment.id"
            :comment="comment"
            :current-user-id="currentUserId"
            :depth="0"
            @reply="handleCommentReply"
            @update="handleCommentUpdate"
            @delete="handleCommentDelete"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.post-view {
  min-height: 100vh;
  background-color: #f8fafc;
}

.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 50vh;
}

.spinner {
  width: 40px;
  height: 40px;
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
  justify-content: center;
  align-items: center;
  min-height: 50vh;
  color: #ef4444;
}

.post-container {
  max-width: 800px;
  margin: 0 auto;
  background: white;
  min-height: 100vh;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.back-button,
.edit-button {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.back-button:hover {
  color: #374151;
  background-color: #f3f4f6;
}

.edit-button {
  color: #3b82f6;
  border: 1px solid #e5e7eb;
}

.edit-button:hover {
  background-color: #eff6ff;
  border-color: #3b82f6;
}

.post-content-wrapper {
  padding: 32px 24px;
}

.author-section {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}

.author-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.author-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1.2rem;
}

.author-info {
  flex: 1;
}

.author-name {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.post-meta {
  font-size: 14px;
  color: #6b7280;
}

.post-title {
  font-size: 32px;
  font-weight: 700;
  color: #1f2937;
  line-height: 1.2;
  margin: 0 0 24px 0;
}

.post-body {
  font-size: 16px;
  line-height: 1.6;
  color: #374151;
  margin-bottom: 32px;
}

.post-body p {
  margin: 0;
}

.post-image {
  margin-bottom: 32px;
  border-radius: 12px;
  overflow: hidden;
}

.post-image img {
  width: 100%;
  height: auto;
  display: block;
}

.post-stats {
  display: flex;
  gap: 24px;
  margin-bottom: 32px;
  padding-bottom: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #6b7280;
  font-size: 14px;
}

.comments-section {
  padding: 0 24px 32px;
}

.comments-title {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 24px 0;
}

.add-comment {
  display: flex;
  gap: 12px;
  margin-bottom: 32px;
}

.comment-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.comment-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.comment-avatar .avatar-placeholder {
  font-size: 1rem;
}

.comment-form {
  flex: 1;
}

.comment-input {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 14px;
  resize: vertical;
  background: #f9fafb;
  transition: all 0.2s ease;
}

.comment-input:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.comment-input::placeholder {
  color: #9ca3af;
}

.comment-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 12px;
}

.comment-submit {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 16px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.comment-submit:hover:not(:disabled) {
  background: #2563eb;
}

.comment-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.no-comments {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
  text-align: center;
  color: #9ca3af;
}

.no-comments svg {
  margin-bottom: 16px;
}

.no-comments p {
  margin: 0;
  font-size: 16px;
}

@media (max-width: 768px) {
  .post-container {
    margin: 0;
  }

  .post-header {
    padding: 16px 20px;
  }

  .post-content-wrapper {
    padding: 24px 20px;
  }

  .comments-section {
    padding: 0 20px 24px;
  }

  .post-title {
    font-size: 24px;
  }

  .post-stats {
    gap: 16px;
  }
}

.login-prompt {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  margin: 20px 0;
}

.login-prompt p {
  margin: 0;
  color: #6c757d;
  font-size: 14px;
}

.login-link {
  color: #007bff;
  text-decoration: none;
  font-weight: 500;
}

.login-link:hover {
  text-decoration: underline;
}
</style>
