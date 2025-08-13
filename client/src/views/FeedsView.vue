<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import FeedPost from '@/components/FeedPost.vue'
import axios from '@/plugins/axios'

const authStore = useAuthStore()

// Reactive data
const searchQuery = ref('')
const sortBy = ref('latest')
const loading = ref(true)
const posts = ref([])
const error = ref(null)

// Methods
const handleSearch = () => {
  console.log('Searching for:', searchQuery.value)
}

const handleSortChange = () => {
  console.log('Sorting by:', sortBy.value)
}

const handleFilter = () => {
  console.log('Opening filter options')
}

const fetchFeed = async () => {
  try {
    loading.value = true
    const params = { public: true, search: searchQuery.value }
    const { data } = await axios.get('/posts', { params })
    posts.value = data.data || data
  } catch (e) {
    error.value = 'Failed to load feed'
    console.error(e)
  } finally {
    loading.value = false
  }
}

const onLike = async (postSlug) => {
  try {
    const post = posts.value.find((p) => p.slug === postSlug)
    if (!post) return

    const endpoint = post.is_liked ? 'unlike' : 'like'
    const response = await axios.post(`/posts/${postSlug}/${endpoint}`)

    // Update the post in the local state
    posts.value = posts.value.map((p) =>
      p.slug === postSlug
        ? {
            ...p,
            is_liked: response.data.is_liked,
            likes_count: response.data.likes,
          }
        : p,
    )
  } catch (err) {
    console.error('Error handling like:', err)

    // If we get a 400 error, refresh the feed to get correct states
    if (err.response && err.response.status === 400) {
      console.log('Like states out of sync, refreshing feed...')
      await fetchFeed()
    }
  }
}

const onComment = (postId) => {
  console.log('Open comments for post', postId)
}

const onShare = (postSlug) => {
  console.log('Share post', postSlug)
  navigator?.clipboard?.writeText?.(window.location.origin + '/blog/' + postSlug).catch(() => {})
}

// Lifecycle
onMounted(() => {
  fetchFeed()
})
</script>

<template>
  <div class="feeds-view">
    <div class="container py-4">
      <!-- Welcome Section -->
      <div class="welcome-card mb-4">
        <div class="welcome-content">
          <div class="welcome-text">
            <h2 class="welcome-title">
              Welcome back, {{ authStore.user?.username || authStore.user?.name || 'User' }}!
            </h2>
            <p class="welcome-subtitle">Discover amazing stories from our community</p>
          </div>
          <RouterLink to="/posts/create" class="new-post-btn">
            <svg
              class="plus-icon"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M12 5V19"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <path
                d="M5 12H19"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            New Post
          </RouterLink>
        </div>
      </div>

      <!-- Search and Filter Section -->
      <div class="search-filter-card mb-4">
        <div class="search-filter-content">
          <div class="search-section">
            <div class="search-input-wrapper">
              <svg
                class="search-icon"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M21 21L16.65 16.65"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M11 6C13.7614 6 16 8.23858 16 11C16 13.7614 13.7614 16 11 16C8.23858 16 6 13.7614 6 11C6 8.23858 8.23858 6 11 6Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              <input
                type="text"
                class="search-input"
                placeholder="Search posts, authors, or topics..."
                v-model="searchQuery"
                @keyup.enter="handleSearch"
              />
            </div>
          </div>

          <div class="filter-section">
            <button class="filter-btn" @click="handleFilter">
              <svg
                class="filter-icon"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </button>

            <div class="sort-dropdown">
              <select v-model="sortBy" @change="handleSortChange" class="sort-btn">
                <option value="latest">Latest</option>
                <option value="popular">Popular</option>
                <option value="trending">Trending</option>
                <option value="oldest">Oldest</option>
              </select>
              <svg
                class="chevron-icon"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M6 9L12 15L18 9"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="content-area">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          {{ error }}
        </div>

        <!-- Feed Content -->
        <div v-else class="feed-content">
          <div v-if="posts.length === 0" class="placeholder-content">
            <h3>No posts yet</h3>
            <p>Follow authors and topics to fill your feed.</p>
          </div>
          <div v-else class="feed-list">
            <FeedPost
              v-for="post in posts"
              :key="post.id"
              :post="post"
              @like="onLike"
              @comment="onComment"
              @share="onShare"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.feeds-view {
  min-height: calc(100vh - 80px);
  background-color: #f8fafc;
  padding-top: 1rem;
}

/* Welcome Card */
.welcome-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.welcome-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.welcome-text {
  flex: 1;
}

.welcome-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #334155;
  margin: 0 0 0.25rem 0;
}

.welcome-subtitle {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
}

.new-post-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
}

.new-post-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
  color: white;
  text-decoration: none;
}

.plus-icon {
  flex-shrink: 0;
}

/* Search and Filter Card */
.search-filter-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.search-filter-content {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-section {
  flex: 1;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 1rem;
  color: #94a3b8;
  z-index: 1;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  background-color: #f8fafc;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  background-color: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-input::placeholder {
  color: #94a3b8;
}

.filter-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.filter-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  color: #64748b;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  background-color: #f0f9ff;
}

.sort-dropdown {
  position: relative;
}

.sort-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  color: #334155;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 120px;
}

.sort-btn:hover {
  border-color: #3b82f6;
  background-color: #f0f9ff;
}

.sort-btn:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.chevron-icon {
  flex-shrink: 0;
  transition: transform 0.3s ease;
  pointer-events: none;
}

.sort-btn:hover .chevron-icon {
  transform: rotate(180deg);
}

/* Content Area */
.content-area {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  border: 1px solid rgba(0, 0, 0, 0.05);
  min-height: 400px;
}

.placeholder-content {
  text-align: center;
  color: #64748b;
}

.placeholder-content h3 {
  color: #334155;
  margin-bottom: 1rem;
}

/* Loading and Error States */
.loading-state {
  text-align: center;
  padding: 3rem 0;
}

.error-state {
  text-align: center;
  color: #dc2626;
  padding: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .welcome-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .new-post-btn {
    width: 100%;
    justify-content: center;
  }

  .search-filter-content {
    flex-direction: column;
    gap: 1rem;
  }

  .filter-section {
    width: 100%;
    justify-content: space-between;
  }

  .sort-btn {
    flex: 1;
    justify-content: space-between;
  }
}
</style>
