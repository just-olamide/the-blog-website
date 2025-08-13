<script>
export default {
  name: 'CommentComponent',
  props: {
    comment: {
      type: Object,
      required: true,
    },
    currentUserId: {
      type: Number,
      default: null,
    },
    depth: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      showReplyForm: false,
      replyContent: '',
      editing: false,
      editContent: '',
      loading: false,
      showReplies: true,
    }
  },
  computed: {
    isAuthor() {
      return this.currentUserId === this.comment.user_id
    },
    timeAgo() {
      if (!this.comment.created_at) return ''
      const now = new Date()
      const commentDate = new Date(this.comment.created_at)
      const diffInSeconds = Math.floor((now - commentDate) / 1000)

      if (diffInSeconds < 60) return 'Just now'
      if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`
      if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`
      if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`
      return commentDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    },
    hasReplies() {
      return this.comment.replies && this.comment.replies.length > 0
    },
    avatarUrl() {
      return this.comment.user?.avatar || null
    },
    authorInitial() {
      return (this.comment.user?.name || 'U').charAt(0).toUpperCase()
    },
  },
  methods: {
    async submitReply() {
      if (!this.replyContent.trim()) return

      this.loading = true
      try {
        this.$emit('reply', {
          content: this.replyContent,
          parentId: this.comment.id,
        })
        this.replyContent = ''
        this.showReplyForm = false
      } catch (error) {
        console.error('Error submitting reply:', error)
      } finally {
        this.loading = false
      }
    },
    startEditing() {
      this.editContent = this.comment.content
      this.editing = true
    },
    async saveEdit() {
      if (!this.editContent.trim()) return

      this.loading = true
      try {
        this.$emit('update', {
          id: this.comment.id,
          content: this.editContent,
        })
        this.editing = false
      } catch (error) {
        console.error('Error updating comment:', error)
      } finally {
        this.loading = false
      }
    },
    async deleteComment() {
      if (!confirm('Are you sure you want to delete this comment?')) return

      this.loading = true
      try {
        this.$emit('delete', this.comment.id)
      } catch (error) {
        console.error('Error deleting comment:', error)
      } finally {
        this.loading = false
      }
    },
    toggleReplies() {
      this.showReplies = !this.showReplies
    },
    cancelEdit() {
      this.editing = false
      this.editContent = ''
    },
    cancelReply() {
      this.showReplyForm = false
      this.replyContent = ''
    },
  },
}
</script>

<template>
  <div class="comment-wrapper">
    <div class="comment">
      <!-- Comment Header -->
      <div class="comment-header">
        <div class="comment-avatar">
          <img v-if="avatarUrl" :src="avatarUrl" :alt="comment.user?.name || 'User'" />
          <div v-else class="avatar-placeholder">{{ authorInitial }}</div>
        </div>

        <div class="comment-info">
          <div class="comment-author-line">
            <span class="comment-author">{{ comment.user?.name || 'Anonymous' }}</span>
            <span class="comment-time">{{ timeAgo }}</span>
          </div>

          <!-- Comment Content -->
          <div v-if="!editing" class="comment-content">
            {{ comment.content }}
          </div>

          <!-- Edit Form -->
          <div v-else class="comment-edit-form">
            <textarea
              v-model="editContent"
              class="edit-textarea"
              rows="3"
              @keydown.escape="cancelEdit"
            ></textarea>
            <div class="edit-actions">
              <button class="save-btn" @click="saveEdit" :disabled="loading || !editContent.trim()">
                {{ loading ? 'Saving...' : 'Save' }}
              </button>
              <button class="cancel-btn" @click="cancelEdit" :disabled="loading">Cancel</button>
            </div>
          </div>

          <!-- Comment Actions -->
          <div v-if="!editing" class="comment-actions">
            <button
              v-if="currentUserId"
              class="action-btn reply-btn"
              @click="showReplyForm = !showReplyForm"
            >
              Reply
            </button>

            <button v-if="isAuthor" class="action-btn edit-btn" @click="startEditing">Edit</button>

            <button v-if="isAuthor" class="action-btn delete-btn" @click="deleteComment">
              Delete
            </button>

            <button v-if="hasReplies" class="action-btn toggle-replies-btn" @click="toggleReplies">
              {{ showReplies ? 'Hide' : 'Show' }} {{ comment.replies.length }}
              {{ comment.replies.length === 1 ? 'reply' : 'replies' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Reply Form -->
      <div v-if="showReplyForm" class="reply-form">
        <div class="reply-form-header">
          <div class="reply-avatar">
            <img v-if="currentUserId" :src="avatarUrl" alt="Your avatar" />
            <div v-else class="avatar-placeholder">Y</div>
          </div>
          <div class="reply-form-content">
            <textarea
              v-model="replyContent"
              class="reply-textarea"
              rows="2"
              placeholder="Write a reply..."
              @keydown.escape="cancelReply"
            ></textarea>
            <div class="reply-actions">
              <button
                class="reply-submit-btn"
                @click="submitReply"
                :disabled="loading || !replyContent.trim()"
              >
                {{ loading ? 'Replying...' : 'Reply' }}
              </button>
              <button class="reply-cancel-btn" @click="cancelReply" :disabled="loading">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Nested Replies -->
    <div v-if="hasReplies && showReplies" class="replies-container">
      <CommentComponent
        v-for="reply in comment.replies"
        :key="reply.id"
        :comment="reply"
        :current-user-id="currentUserId"
        :depth="depth + 1"
        @reply="$emit('reply', $event)"
        @update="$emit('update', $event)"
        @delete="$emit('delete', $event)"
      />
    </div>
  </div>
</template>

<style scoped>
.comment-wrapper {
  margin-bottom: 16px;
}

.comment {
  background: white;
}

.comment-header {
  display: flex;
  gap: 12px;
  align-items: flex-start;
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

.avatar-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
}

.comment-info {
  flex: 1;
  min-width: 0;
}

.comment-author-line {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.comment-author {
  font-weight: 600;
  color: #1f2937;
  font-size: 14px;
}

.comment-time {
  font-size: 12px;
  color: #9ca3af;
}

.comment-content {
  color: #374151;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 8px;
  word-wrap: break-word;
}

.comment-actions {
  display: flex;
  gap: 16px;
  margin-top: 8px;
}

.action-btn {
  background: none;
  border: none;
  color: #6b7280;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  padding: 4px 0;
  transition: color 0.2s ease;
}

.action-btn:hover {
  color: #374151;
}

.reply-btn:hover {
  color: #3b82f6;
}

.edit-btn:hover {
  color: #f59e0b;
}

.delete-btn:hover {
  color: #ef4444;
}

.toggle-replies-btn:hover {
  color: #8b5cf6;
}

/* Edit Form Styles */
.comment-edit-form {
  margin-top: 8px;
}

.edit-textarea {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 14px;
  resize: vertical;
  background: #f9fafb;
  transition: all 0.2s ease;
  margin-bottom: 8px;
}

.edit-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.edit-actions {
  display: flex;
  gap: 8px;
}

.save-btn,
.cancel-btn {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid;
}

.save-btn {
  background: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.save-btn:hover:not(:disabled) {
  background: #2563eb;
  border-color: #2563eb;
}

.save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.cancel-btn {
  background: white;
  border-color: #d1d5db;
  color: #6b7280;
}

.cancel-btn:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

/* Reply Form Styles */
.reply-form {
  margin-top: 12px;
  padding-top: 12px;
}

.reply-form-header {
  display: flex;
  gap: 12px;
  align-items: flex-start;
}

.reply-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.reply-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.reply-avatar .avatar-placeholder {
  font-size: 0.875rem;
}

.reply-form-content {
  flex: 1;
}

.reply-textarea {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 14px;
  resize: vertical;
  background: #f9fafb;
  transition: all 0.2s ease;
  margin-bottom: 8px;
}

.reply-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.reply-textarea::placeholder {
  color: #9ca3af;
}

.reply-actions {
  display: flex;
  gap: 8px;
}

.reply-submit-btn,
.reply-cancel-btn {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid;
}

.reply-submit-btn {
  background: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.reply-submit-btn:hover:not(:disabled) {
  background: #2563eb;
  border-color: #2563eb;
}

.reply-submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.reply-cancel-btn {
  background: white;
  border-color: #d1d5db;
  color: #6b7280;
}

.reply-cancel-btn:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

/* Replies Container */
.replies-container {
  margin-left: 52px;
  margin-top: 16px;
  position: relative;
}

.replies-container::before {
  content: '';
  position: absolute;
  left: -26px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e5e7eb;
}

/* Responsive Design */
@media (max-width: 768px) {
  .comment-header {
    gap: 8px;
  }

  .comment-avatar {
    width: 36px;
    height: 36px;
  }

  .reply-avatar {
    width: 28px;
    height: 28px;
  }

  .replies-container {
    margin-left: 44px;
  }

  .replies-container::before {
    left: -22px;
  }

  .comment-actions {
    gap: 12px;
  }

  .action-btn {
    font-size: 11px;
  }
}

@media (max-width: 480px) {
  .replies-container {
    margin-left: 32px;
  }

  .replies-container::before {
    left: -16px;
  }

  .comment-author-line {
    flex-wrap: wrap;
  }
}
</style>
