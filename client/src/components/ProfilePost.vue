<script>
export default {
  name: 'ProfilePost',
  props: {
    post: {
      type: Object,
      required: true,
    },
  },
  computed: {
    author() {
      return this.post.user || this.post.author || {}
    },
    formattedDate() {
      if (!this.post.created_at) return ''
      return new Date(this.post.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      })
    },
    readTime() {
      if (!this.post.content) return '2 min read'
      const wordsPerMinute = 200
      const wordCount = this.post.content.split(' ').length
      const minutes = Math.ceil(wordCount / wordsPerMinute)
      return `${minutes} min read`
    },
  },
  methods: {
    handleLike() {
      this.$emit('like', this.post.id)
    },
    handleComment() {
      this.$emit('comment', this.post.id)
    },
    handleShare() {
      this.$emit('share', this.post.id)
    },
    handleView() {
      // Navigate to post view page
      this.$router.push(`/posts/${this.post.slug || this.post.id}`)
    },
    handleEdit() {
      // Navigate to post edit page
      this.$router.push(`/edit-post/${this.post.id}`)
    },
  },
}
</script>

<template>
  <div class="profile-post">
    <!-- Post Header -->
    <div class="post-header">
      <div class="author-info">
        <div class="author-avatar">
          <img v-if="author?.avatar" :src="author.avatar" :alt="author?.name || 'Author'" />
          <div v-else class="avatar-placeholder">
            {{ (author?.name || 'A').charAt(0).toUpperCase() }}
          </div>
        </div>
        <div class="author-details">
          <h4 class="author-name">{{ author?.name || 'Anonymous' }}</h4>
          <div class="post-meta">
            <span class="post-date">{{ formattedDate }}</span>
            <span class="post-dot">•</span>
            <span class="read-time">{{ readTime }}</span>
          </div>
        </div>
      </div>
      <button class="more-options-btn">
        <svg
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <circle cx="12" cy="12" r="1" fill="currentColor" />
          <circle cx="19" cy="12" r="1" fill="currentColor" />
          <circle cx="5" cy="12" r="1" fill="currentColor" />
        </svg>
      </button>
    </div>

    <!-- Post Content -->
    <div class="post-content">
      <h3 class="post-title">{{ post.title }}</h3>
      <p class="post-excerpt">{{ post.excerpt || post.content?.substring(0, 150) + '...' }}</p>

      <!-- Post Image (if available) -->
      <div v-if="post.featured_image" class="post-image">
        <img :src="post.featured_image" :alt="post.title" />
      </div>
    </div>

    <!-- Post Tags -->
    <div v-if="post.tags && post.tags.length > 0" class="post-tags">
      <span v-for="tag in post.tags.slice(0, 3)" :key="tag.id" class="tag"> #{{ tag.name }} </span>
      <span v-if="post.tags.length > 3" class="more-tags"> +{{ post.tags.length - 3 }} more </span>
    </div>

    <!-- Post Actions -->
    <div class="post-actions">
      <button class="action-btn like-btn" @click="handleLike">
        <svg
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M20.84 4.61C20.3292 4.099 19.7228 3.69364 19.0554 3.41708C18.3879 3.14052 17.6725 2.99817 16.95 2.99817C16.2275 2.99817 15.5121 3.14052 14.8446 3.41708C14.1772 3.69364 13.5708 4.099 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.5783 8.50903 2.9987 7.05 2.9987C5.59096 2.9987 4.19169 3.5783 3.16 4.61C2.1283 5.6417 1.5487 7.04097 1.5487 8.5C1.5487 9.95903 2.1283 11.3583 3.16 12.39L12 21.23L20.84 12.39C21.351 11.8792 21.7564 11.2728 22.0329 10.6054C22.3095 9.93789 22.4518 9.22249 22.4518 8.5C22.4518 7.77751 22.3095 7.0621 22.0329 6.39464C21.7564 5.72718 21.351 5.12075 20.84 4.61Z"
            :fill="post.is_liked ? '#ef4444' : 'none'"
            :stroke="post.is_liked ? '#ef4444' : 'currentColor'"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
        <span class="action-text">{{ post.likes_count || 0 }}</span>
      </button>

      <button class="action-btn comment-btn" @click="handleComment">
        <svg
          width="20"
          height="20"
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
        <span class="action-text">{{ post.comments_count || 0 }}</span>
      </button>

      <button class="action-btn share-btn" @click="handleShare">
        <svg
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M4 12V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V12"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
          <path
            d="M16 6L12 2L8 6"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
          <path
            d="M12 2V15"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
        <span class="action-text">Share</span>
      </button>

      <!-- Profile-specific action buttons -->
      <div class="profile-actions">
        <button class="action-btn view-btn" @click="handleView">
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="action-text">View</span>
        </button>

        <button class="action-btn edit-btn" @click="handleEdit">
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="action-text">Edit</span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-post {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  border: 1px solid rgba(0, 0, 0, 0.05);
  margin-bottom: 1.5rem;
  transition: all 0.3s ease;
}

.profile-post:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

/* Post Header */
.post-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.author-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
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

.author-details {
  flex: 1;
}

.author-name {
  font-size: 1rem;
  font-weight: 600;
  color: #334155;
  margin: 0 0 0.25rem 0;
}

.post-meta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #64748b;
}

.post-dot {
  color: #cbd5e1;
}

.more-options-btn {
  background: none;
  border: none;
  color: #64748b;
  padding: 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.more-options-btn:hover {
  background-color: #f1f5f9;
  color: #334155;
}

/* Post Content */
.post-content {
  margin-bottom: 1rem;
}

.post-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #334155;
  margin: 0 0 0.75rem 0;
  line-height: 1.4;
}

.post-excerpt {
  font-size: 0.95rem;
  color: #64748b;
  line-height: 1.6;
  margin: 0 0 1rem 0;
}

.post-image {
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.post-image img {
  width: 100%;
  height: auto;
  object-fit: cover;
}

/* Post Tags */
.post-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.tag {
  background-color: #f0f9ff;
  color: #0369a1;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  border: 1px solid #e0f2fe;
}

.more-tags {
  color: #64748b;
  font-size: 0.8rem;
  padding: 0.25rem 0.5rem;
}

/* Post Actions */
.post-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
}

.post-actions > .action-btn {
  margin-right: 1rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: none;
  border: none;
  color: #64748b;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.action-btn:hover {
  background-color: #f8fafc;
  color: #334155;
}

.like-btn:hover {
  color: #ef4444;
}

.comment-btn:hover {
  color: #3b82f6;
}

.share-btn:hover {
  color: #10b981;
}

.profile-actions {
  display: flex;
  gap: 0.5rem;
}

.view-btn {
  background-color: #e0f2fe;
  color: #0284c7;
  border: 1px solid #7dd3fc;
}

.view-btn:hover {
  background-color: #0284c7;
  color: white;
}

.edit-btn {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.edit-btn:hover {
  background-color: #374151;
  color: white;
}

.action-text {
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
  .profile-post {
    padding: 1rem;
    margin-bottom: 1rem;
  }

  .post-header {
    margin-bottom: 0.75rem;
  }

  .author-avatar {
    width: 40px;
    height: 40px;
  }

  .post-title {
    font-size: 1.1rem;
  }

  .post-actions {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .post-actions > .action-btn {
    margin-right: 0;
  }

  .profile-actions {
    justify-content: space-between;
  }

  .action-btn {
    padding: 0.5rem;
    font-size: 0.8rem;
    justify-content: center;
  }
}
</style>
