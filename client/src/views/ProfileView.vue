<template>
  <div class="profile-page">
    <div class="container py-5">
      <div class="profile-card">
        <div class="profile-header">
          <div class="avatar">
            <img v-if="user?.avatar" :src="avatarUrl" :alt="user?.name || 'User'" />
            <div v-else class="avatar-placeholder">
              {{ (user?.name || 'U').charAt(0).toUpperCase() }}
            </div>
          </div>
          <div class="identity">
            <h1 class="name">{{ user?.name || '—' }}</h1>
            <p class="username">{{ user?.username || '' }}</p>
            <div class="meta">
              <span class="joined">
                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path d="M8 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                  <path d="M16 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                  <rect
                    x="3"
                    y="5"
                    width="18"
                    height="16"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="2"
                  />
                  <path d="M3 10H21" stroke="currentColor" stroke-width="2" />
                </svg>
                Joined {{ joinedAt }}
              </span>
              <span class="dot">•</span>
              <span class="email">{{ user?.email }}</span>
            </div>
          </div>
          <RouterLink to="/profile/edit" class="edit-btn">
            <svg
              class="edit-icon"
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
            Edit Profile
          </RouterLink>
        </div>

        <hr class="divider" />

        <div class="stats">
          <div class="stat">
            <div class="value">{{ stats.posts }}</div>
            <div class="label">Posts</div>
          </div>
          <div class="stat">
            <div class="value">{{ stats.likes }}</div>
            <div class="label">Total Likes</div>
          </div>
          <div class="stat">
            <div class="value">{{ stats.comments }}</div>
            <div class="label">Total Comments</div>
          </div>
        </div>
      </div>

      <!-- User Posts Section -->
      <div class="posts-section">
        <h2 class="section-title">My Posts</h2>
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading your posts...</p>
        </div>
        <div v-else-if="posts.length === 0" class="empty-state">
          <svg
            width="64"
            height="64"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M14 2V8H20"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M16 13H8"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M16 17H8"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M10 9H8"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
          <h3>No posts yet</h3>
          <p>You haven't written any posts yet. Start sharing your thoughts!</p>
          <RouterLink to="/posts/create" class="create-post-btn">
            Create Your First Post
          </RouterLink>
        </div>
        <div v-else class="posts-grid">
          <article v-for="post in posts" :key="post.id" class="post-card">
            <!-- Post Header -->
            <div class="post-header">
              <div class="author-info">
                <div class="author-avatar">
                  <img v-if="user?.avatar" :src="user.avatar" :alt="user.name" />
                  <div v-else class="avatar-placeholder">
                    {{ (user?.name || 'U').charAt(0).toUpperCase() }}
                  </div>
                </div>
                <div class="author-details">
                  <h4 class="author-name">{{ user?.name || 'Unknown' }}</h4>
                  <span class="post-date">{{ timeAgo(post.created_at) }}</span>
                </div>
              </div>
              <div class="post-actions">
                <button @click="editPost(post)" class="action-btn edit-btn" title="Edit post">
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M18.5 2.50001C18.8978 2.10219 19.4374 1.87869 20 1.87869C20.5626 1.87869 21.1022 2.10219 21.5 2.50001C21.8978 2.89784 22.1213 3.4374 22.1213 4.00001C22.1213 4.56262 21.8978 5.10219 21.5 5.50001L12 15L8 16L9 12L18.5 2.50001Z"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Post Content -->
            <div class="post-content">
              <h3 class="post-title" @click="viewPost(post)">{{ post.title }}</h3>
              <div class="post-excerpt">
                {{
                  post.excerpt ||
                  post.content.substring(0, 150) + (post.content.length > 150 ? '...' : '')
                }}
              </div>
              <div v-if="post.featured_image" class="post-image">
                <img
                  :src="`http://localhost:8000/storage/${post.featured_image}`"
                  :alt="post.title"
                  @click="viewPost(post)"
                />
              </div>
            </div>

            <!-- Post Stats -->
            <div class="post-stats">
              <button
                class="stat-item like-btn"
                @click="handleLike(post.id)"
                :class="{ active: post.is_liked }"
              >
                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M20.84 4.61C20.3292 4.099 19.7228 3.69364 19.0554 3.41708C18.3879 3.14052 17.6725 2.99817 16.95 2.99817C16.2275 2.99817 15.5121 3.14052 14.8446 3.41708C14.1772 3.69364 13.5708 4.099 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.5783 8.50903 2.9987 7.05 2.9987C5.59096 2.9987 4.19169 3.5783 3.16 4.61C2.1283 5.6417 1.5487 7.04097 1.5487 8.5C1.5487 9.95903 2.1283 11.3583 3.16 12.39L12 21.23L20.84 12.39C21.351 11.8792 21.7563 11.2728 22.0329 10.6053C22.3095 9.93789 22.4518 9.22248 22.4518 8.5C22.4518 7.77752 22.3095 7.06211 22.0329 6.39467C21.7563 5.72723 21.351 5.1208 20.84 4.61V4.61Z"
                    :fill="post.is_liked ? '#ef4444' : 'none'"
                    :stroke="post.is_liked ? '#ef4444' : '#6b7280'"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                <span>{{ post.likes_count || 0 }} likes</span>
              </button>
              <button class="stat-item comment-btn" @click="toggleComments(post)">
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
                <span>{{ post.comments_count || 0 }} comments</span>
              </button>
            </div>

            <!-- Comments Section -->
            <div v-if="post.showComments" class="comments-section">
              <h4 class="comments-title">Comments ({{ post.comments?.length || 0 }})</h4>

              <!-- Add Comment Form -->
              <div class="add-comment">
                <div class="comment-avatar">
                  <img v-if="user?.avatar" :src="user.avatar" :alt="user.name" />
                  <div v-else class="avatar-placeholder">
                    {{ (user?.name || 'U').charAt(0).toUpperCase() }}
                  </div>
                </div>
                <div class="comment-form">
                  <textarea
                    v-model="post.newComment"
                    class="comment-input"
                    placeholder="Add a thoughtful comment..."
                    rows="2"
                  ></textarea>
                  <div class="comment-actions">
                    <button
                      class="comment-submit"
                      @click="submitComment(post)"
                      :disabled="post.submitting || !post.newComment?.trim()"
                    >
                      {{ post.submitting ? 'Posting...' : 'Comment' }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Comments List -->
              <div v-if="!post.comments || post.comments.length === 0" class="no-comments">
                <p>No comments yet. Be the first to comment!</p>
              </div>

              <div v-else class="comments-list">
                <CommentComponent
                  v-for="comment in post.comments"
                  :key="comment.id"
                  :comment="comment"
                  :current-user-id="user?.id"
                  @reply="(data) => handleCommentReply(post, data)"
                  @update="(data) => handleCommentUpdate(post, data)"
                  @delete="(commentId) => handleCommentDelete(post, commentId)"
                />
              </div>
            </div>
          </article>
        </div>

        <!-- Load More Button -->
        <div v-if="hasMorePosts" class="load-more-section">
          <button @click="loadMorePosts" :disabled="loadingMore" class="load-more-btn">
            <span v-if="loadingMore">Loading...</span>
            <span v-else>Load More Posts</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import CommentComponent from '@/components/CommentComponent.vue'
import axios from '@/plugins/axios'
import ProfilePost from '@/components/ProfilePost.vue'

export default {
  name: 'ProfileView',
  components: {
    ProfilePost,
    CommentComponent,
  },
  setup() {
    const user = ref(null)
    const stats = ref({ posts: 0, likes: 0, comments: 0 })
    const posts = ref([])
    const currentPage = ref(1)
    const hasMorePosts = ref(false)
    const newPassword = ref('')
    const confirmPassword = ref('')
    const error = ref('')
    const success = ref('')
    const loading = ref(false)
    const loadingMore = ref(false)

    onMounted(async () => {
      try {
        loading.value = true
        const [userRes, statsRes, postsRes] = await Promise.all([
          axios.get('/user'),
          axios.get('/profile/stats'),
          axios.get('/profile/posts?page=1'),
        ])
        user.value = userRes.data
        stats.value = statsRes.data
        posts.value = postsRes.data.data
        hasMorePosts.value = postsRes.data.current_page < postsRes.data.last_page
        currentPage.value = postsRes.data.current_page
      } catch (err) {
        error.value = 'Failed to load profile'
      } finally {
        loading.value = false
      }
    })

    const loadMorePosts = async () => {
      if (loadingMore.value || !hasMorePosts.value) return

      try {
        loadingMore.value = true
        const nextPage = currentPage.value + 1
        const response = await axios.get(`/profile/posts?page=${nextPage}`)

        posts.value = [...posts.value, ...response.data.data]
        hasMorePosts.value = response.data.current_page < response.data.last_page
        currentPage.value = response.data.current_page
      } catch (err) {
        console.error('Failed to load more posts:', err)
      } finally {
        loadingMore.value = false
      }
    }

    const handleLike = async (postId) => {
      try {
        const post = posts.value.find((p) => p.id === postId)
        if (!post) return

        const postSlug = post.slug || post.id

        if (post.is_liked) {
          await axios.post(`/posts/${postSlug}/unlike`)
          post.is_liked = false
          post.likes_count = Math.max(0, (post.likes_count || 0) - 1)
        } else {
          await axios.post(`/posts/${postSlug}/like`)
          post.is_liked = true
          post.likes_count = (post.likes_count || 0) + 1
        }
      } catch (err) {
        console.error('Failed to toggle like:', err)
      }
    }

    const handleComment = (postId) => {
      // Navigate to post detail page where comments can be viewed/added
      const post = posts.value.find((p) => p.id === postId)
      if (post) {
        // Navigate to the post page
        window.location.href = `/blog/${post.slug || post.id}`
      }
    }

    const handleShare = async (postId) => {
      try {
        const post = posts.value.find((p) => p.id === postId)
        if (!post) return

        const shareUrl = `${window.location.origin}/blog/${post.slug || post.id}`

        if (navigator.share) {
          await navigator.share({
            title: post.title,
            text: post.excerpt || post.content?.substring(0, 150),
            url: shareUrl,
          })
        } else {
          await navigator.clipboard.writeText(shareUrl)
          // You could show a toast notification here
          alert('Post link copied to clipboard!')
        }
      } catch (err) {
        console.error('Failed to share post:', err)
      }
    }

    const updateProfile = async () => {
      error.value = ''
      success.value = ''

      if (newPassword.value && newPassword.value !== confirmPassword.value) {
        error.value = 'Passwords do not match'
        return
      }

      loading.value = true
      try {
        const data = {
          name: user.value.name,
          email: user.value.email,
        }

        if (newPassword.value) {
          data.password = newPassword.value
          data.password_confirmation = confirmPassword.value
        }

        await axios.put('/profile', data)
        success.value = 'Profile updated successfully'
        newPassword.value = ''
        confirmPassword.value = ''
      } catch (err) {
        error.value = err.response?.data?.message || 'Failed to update profile'
      } finally {
        loading.value = false
      }
    }

    const avatarUrl = computed(() =>
      user.value?.avatar ? `http://localhost:8000/storage/${user.value.avatar}` : '',
    )
    const joinedAt = computed(() => {
      if (!user.value?.created_at) return ''
      const d = new Date(user.value.created_at)
      return d.toLocaleString('en-US', { month: 'long', year: 'numeric' })
    })

    // New comment-related methods
    const timeAgo = (dateString) => {
      const now = new Date()
      const date = new Date(dateString)
      const diffInSeconds = Math.floor((now - date) / 1000)

      if (diffInSeconds < 60) return 'Just now'
      if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} min ago`
      if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
      return `${Math.floor(diffInSeconds / 86400)} days ago`
    }

    const toggleComments = async (post) => {
      if (!post.showComments) {
        // Load comments when showing for the first time
        try {
          const response = await axios.get(`/posts/${post.slug}/comments`)
          post.comments = response.data
        } catch (error) {
          console.error('Error loading comments:', error)
          post.comments = []
        }
      }
      post.showComments = !post.showComments
    }

    const submitComment = async (post) => {
      if (!post.newComment?.trim()) return

      post.submitting = true
      try {
        const response = await axios.post(`/posts/${post.slug}/comments`, {
          content: post.newComment,
        })

        // Add the new comment to the list
        if (!post.comments) post.comments = []
        post.comments.unshift(response.data)
        post.newComment = ''
        post.comments_count = (post.comments_count || 0) + 1
      } catch (error) {
        console.error('Error submitting comment:', error)
        alert('Error posting comment. Please try again.')
      } finally {
        post.submitting = false
      }
    }

    const handleCommentReply = async (post, data) => {
      try {
        const response = await axios.post(`/posts/${post.slug}/comments`, {
          content: data.content,
          parent_id: data.parentId,
        })

        // Refresh comments to show the new reply
        const commentsResponse = await axios.get(`/posts/${post.slug}/comments`)
        post.comments = commentsResponse.data
        post.comments_count = (post.comments_count || 0) + 1
      } catch (error) {
        console.error('Error submitting reply:', error)
        alert('Error posting reply. Please try again.')
      }
    }

    const handleCommentUpdate = async (post, data) => {
      try {
        await axios.put(`/comments/${data.commentId}`, {
          content: data.content,
        })

        // Refresh comments to show the updated comment
        const response = await axios.get(`/posts/${post.slug}/comments`)
        post.comments = response.data
      } catch (error) {
        console.error('Error updating comment:', error)
        alert('Error updating comment. Please try again.')
      }
    }

    const handleCommentDelete = async (post, commentId) => {
      if (!confirm('Are you sure you want to delete this comment?')) return

      try {
        await axios.delete(`/comments/${commentId}`)

        // Refresh comments to remove the deleted comment
        const response = await axios.get(`/posts/${post.slug}/comments`)
        post.comments = response.data
        post.comments_count = Math.max(0, (post.comments_count || 0) - 1)
      } catch (error) {
        console.error('Error deleting comment:', error)
        alert('Error deleting comment. Please try again.')
      }
    }

    const viewPost = (post) => {
      window.location.href = `/blog/${post.slug || post.id}`
    }

    const editPost = (post) => {
      window.location.href = `/posts/${post.slug}/edit`
    }

    return {
      user,
      stats,
      posts,
      hasMorePosts,
      newPassword,
      confirmPassword,
      error,
      success,
      loading,
      loadingMore,
      updateProfile,
      loadMorePosts,
      handleLike,
      handleComment,
      handleShare,
      avatarUrl,
      joinedAt,
      timeAgo,
      toggleComments,
      submitComment,
      handleCommentReply,
      handleCommentUpdate,
      handleCommentDelete,
      viewPost,
      editPost,
    }
  },
}
</script>

<style scoped>
.profile-page {
  background-color: #f8fafc;
  min-height: calc(100vh - 80px);
}

.profile-card {
  background: white;
  border-radius: 16px;
  border: 1px solid rgba(0, 0, 0, 0.06);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
  padding: 24px;
  max-width: 900px;
  margin: 0 auto;
}

.profile-header {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 20px;
  align-items: center;
}

.avatar {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  overflow: hidden;
}

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  color: white;
  font-weight: 700;
  font-size: 36px;
}

.identity .name {
  margin: 0;
  font-weight: 700;
  color: #0f172a;
  font-size: 34px;
}
.identity .username {
  margin: 4px 0 0;
  color: #64748b;
}
.meta {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 10px;
  color: #64748b;
}
.dot {
  color: #cbd5e1;
}

.edit-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #0f172a;
  padding: 10px 14px;
  border-radius: 10px;
  text-decoration: none;
  transition: all 0.2s ease;
}
.edit-btn:hover {
  background: #eef2ff;
  border-color: #c7d2fe;
}

.divider {
  border: none;
  border-top: 1px solid #e2e8f0;
  margin: 18px 0;
}

.stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  text-align: center;
}
.stat .value {
  font-size: 28px;
  font-weight: 700;
  color: #0f172a;
}
.stat .label {
  color: #64748b;
}

@media (max-width: 640px) {
  .profile-header {
    grid-template-columns: 1fr;
    text-align: center;
  }
  .edit-btn {
    justify-self: center;
  }
}

/* Posts Section Styles */
.posts-section {
  max-width: 900px;
  margin: 2rem auto 0;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 1.5rem;
  text-align: center;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top: 3px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  text-align: center;
  color: #64748b;
}

.empty-state svg {
  color: #cbd5e1;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #334155;
  margin: 0.5rem 0;
}

.empty-state p {
  margin: 0.5rem 0 1.5rem;
  max-width: 400px;
}

.create-post-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  color: white;
  padding: 12px 24px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.create-post-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  color: white;
}

.posts-grid {
  display: grid;
  gap: 1.5rem;
}

.post-card {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  transition: all 0.3s ease;
}

.post-card:hover {
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 20px 0 20px;
}

.author-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.author-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  background: #667eea;
  display: flex;
  align-items: center;
  justify-content: center;
}

.author-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  color: white;
  font-weight: 600;
  font-size: 16px;
}

.author-details h4 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #1a202c;
}

.post-date {
  font-size: 12px;
  color: #64748b;
}

.post-actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 8px;
  cursor: pointer;
  color: #64748b;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background: #f1f5f9;
  color: #3b82f6;
}

.post-content {
  padding: 16px 20px;
}

.post-title {
  margin: 0 0 12px 0;
  font-size: 18px;
  font-weight: 700;
  color: #1a202c;
  cursor: pointer;
  transition: color 0.2s ease;
}

.post-title:hover {
  color: #3b82f6;
}

.post-excerpt {
  color: #4a5568;
  line-height: 1.6;
  margin-bottom: 12px;
}

.post-image {
  margin-top: 12px;
  border-radius: 8px;
  overflow: hidden;
}

.post-image img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.post-image img:hover {
  transform: scale(1.02);
}

.post-stats {
  display: flex;
  gap: 24px;
  padding: 16px 20px;
  border-top: 1px solid #f1f5f9;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 400;
  transition: all 0.2s ease;
}

.stat-item:hover {
  background: transparent;
  color: #374151;
}

.stat-item.active {
  color: #ef4444;
}

.stat-item.active svg path {
  stroke: #ef4444;
}

.comments-section {
  border-top: 1px solid #f1f5f9;
  padding: 20px;
  background: #fafbfc;
}

.comments-title {
  margin: 0 0 16px 0;
  font-size: 16px;
  font-weight: 600;
  color: #1a202c;
}

.add-comment {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.comment-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  overflow: hidden;
  background: #667eea;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.comment-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.comment-avatar .avatar-placeholder {
  color: white;
  font-weight: 600;
  font-size: 12px;
}

.comment-form {
  flex: 1;
}

.comment-input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px;
  font-size: 14px;
  resize: none;
  font-family: inherit;
  transition: border-color 0.2s ease;
}

.comment-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.comment-actions {
  margin-top: 8px;
  display: flex;
  justify-content: flex-end;
}

.comment-submit {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.comment-submit:hover:not(:disabled) {
  background: #2563eb;
}

.comment-submit:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}

.no-comments {
  text-align: center;
  color: #64748b;
  font-style: italic;
  padding: 20px;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.load-more-section {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
}

.load-more-btn {
  background: white;
  border: 2px solid #e2e8f0;
  color: #64748b;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.load-more-btn:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
  transform: translateY(-1px);
}

.load-more-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .posts-section {
    margin-top: 1.5rem;
    padding: 0 1rem;
  }

  .section-title {
    font-size: 1.25rem;
  }

  .empty-state {
    padding: 2rem 1rem;
  }
}
</style>
