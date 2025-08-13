<template>
  <div class="profile-page">
    <div class="container py-5">
      <div class="profile-card">
        <div class="profile-header">
          <div class="avatar">
            <img v-if="user?.avatar" :src="avatarUrl" :alt="user?.name || 'User'" />
            <div v-else class="avatar-placeholder">{{ (user?.name || 'U').charAt(0).toUpperCase() }}</div>
          </div>
          <div class="identity">
            <h1 class="name">{{ user?.name || '—' }}</h1>
            <p class="username">{{ user?.username || '' }}</p>
            <div class="meta">
              <span class="joined">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <path d="M16 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M3 10H21" stroke="currentColor" stroke-width="2"/>
                </svg>
                Joined {{ joinedAt }}
              </span>
              <span class="dot">•</span>
              <span class="email">{{ user?.email }}</span>
            </div>
          </div>
          <RouterLink to="/profile/edit" class="edit-btn">
            <svg class="edit-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M16.5 3.5L20.5 7.5L8 20H4V16L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <h3>No posts yet</h3>
          <p>You haven't written any posts yet. Start sharing your thoughts!</p>
          <RouterLink to="/posts/create" class="create-post-btn">
            Create Your First Post
          </RouterLink>
        </div>
        <div v-else class="posts-grid">
          <ProfilePost 
            v-for="post in posts" 
            :key="post.id" 
            :post="post"
            @like="handleLike"
            @comment="handleComment"
            @share="handleShare"
          />
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
import axios from '@/plugins/axios'
import ProfilePost from '@/components/ProfilePost.vue'

export default {
  name: 'ProfileView',
  components: {
    ProfilePost
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
        const post = posts.value.find(p => p.id === postId)
        if (!post) return

        if (post.is_liked) {
          await axios.post(`/posts/${postId}/unlike`)
          post.is_liked = false
          post.likes_count = Math.max(0, (post.likes_count || 0) - 1)
        } else {
          await axios.post(`/posts/${postId}/like`)
          post.is_liked = true
          post.likes_count = (post.likes_count || 0) + 1
        }
      } catch (err) {
        console.error('Failed to toggle like:', err)
      }
    }

    const handleComment = (postId) => {
      // Navigate to post detail page where comments can be viewed/added
      const post = posts.value.find(p => p.id === postId)
      if (post) {
        // Navigate to the post page
        window.location.href = `/posts/${post.slug || post.id}`
      }
    }

    const handleShare = async (postId) => {
      try {
        const post = posts.value.find(p => p.id === postId)
        if (!post) return

        const shareUrl = `${window.location.origin}/posts/${post.slug || post.id}`
        
        if (navigator.share) {
          await navigator.share({
            title: post.title,
            text: post.excerpt || post.content?.substring(0, 150),
            url: shareUrl
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
          email: user.value.email
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

    const avatarUrl = computed(() => (user.value?.avatar ? `http://localhost:8000/storage/${user.value.avatar}` : ''))
    const joinedAt = computed(() => {
      if (!user.value?.created_at) return ''
      const d = new Date(user.value.created_at)
      return d.toLocaleString('en-US', { month: 'long', year: 'numeric' })
    })

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
      joinedAt
    }
  }
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
  border: 1px solid rgba(0,0,0,0.06);
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
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

.avatar img { width: 100%; height: 100%; object-fit: cover; }

.avatar-placeholder {
  width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  color: white; font-weight: 700; font-size: 36px;
}

.identity .name { margin: 0; font-weight: 700; color: #0f172a; font-size: 34px; }
.identity .username { margin: 4px 0 0; color: #64748b; }
.meta { display: flex; align-items: center; gap: 12px; margin-top: 10px; color: #64748b; }
.dot { color: #cbd5e1; }

.edit-btn {
  display: inline-flex; align-items: center; gap: 8px;
  border: 1px solid #e2e8f0; background: #f8fafc; color: #0f172a;
  padding: 10px 14px; border-radius: 10px; text-decoration: none;
  transition: all .2s ease;
}
.edit-btn:hover { background: #eef2ff; border-color: #c7d2fe; }

.divider { border: none; border-top: 1px solid #e2e8f0; margin: 18px 0; }

.stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; text-align: center; }
.stat .value { font-size: 28px; font-weight: 700; color: #0f172a; }
.stat .label { color: #64748b; }

@media (max-width: 640px) {
  .profile-header { grid-template-columns: 1fr; text-align: center; }
  .edit-btn { justify-self: center; }
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
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
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
