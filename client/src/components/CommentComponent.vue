<script>
export default {
  name: 'CommentComponent',
  props: {
    comment: {
      type: Object,
      required: true
    },
    currentUserId: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      showReplyForm: false,
      replyContent: '',
      editing: false,
      editContent: '',
      loading: false
    }
  },
  computed: {
    isAuthor() {
      return this.currentUserId === this.comment.user_id
    },
    formattedDate() {
      return new Date(this.comment.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
  },
  methods: {
    async submitReply() {
      if (!this.replyContent.trim()) return

      this.loading = true
      try {
        await this.$emit('reply', {
          content: this.replyContent,
          parentId: this.comment.id
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
        await this.$emit('update', {
          id: this.comment.id,
          content: this.editContent
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
        await this.$emit('delete', this.comment.id)
      } catch (error) {
        console.error('Error deleting comment:', error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<template>
  <div class="comment mb-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div class="user-info">
            <h6 class="mb-1">{{ comment.user.name }}</h6>
            <small class="text-muted">{{ formattedDate }}</small>
          </div>
          <div v-if="isAuthor" class="dropdown">
            <button class="btn btn-link dropdown-toggle" 
                    type="button" 
                    data-bs-toggle="dropdown">
              <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu">
              <li>
                <button class="dropdown-item" 
                        @click="startEditing">
                  <i class="bi bi-pencil"></i> Edit
                </button>
              </li>
              <li>
                <button class="dropdown-item text-danger" 
                        @click="deleteComment">
                  <i class="bi bi-trash"></i> Delete
                </button>
              </li>
            </ul>
          </div>
        </div>

        <!-- Comment Content -->
        <div v-if="!editing" class="mt-2">
          {{ comment.content }}
        </div>

        <!-- Edit Form -->
        <div v-else class="mt-2">
          <textarea 
            v-model="editContent"
            class="form-control mb-2"
            rows="3"
          ></textarea>
          <div class="d-flex gap-2">
            <button 
              class="btn btn-primary btn-sm"
              @click="saveEdit"
              :disabled="loading"
            >
              {{ loading ? 'Saving...' : 'Save' }}
            </button>
            <button 
              class="btn btn-outline-secondary btn-sm"
              @click="editing = false"
              :disabled="loading"
            >
              Cancel
            </button>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-3 d-flex gap-2">
          <button 
            v-if="currentUserId"
            class="btn btn-link btn-sm p-0"
            @click="showReplyForm = !showReplyForm"
          >
            Reply
          </button>
        </div>

        <!-- Reply Form -->
        <div v-if="showReplyForm" class="mt-3">
          <textarea 
            v-model="replyContent"
            class="form-control mb-2"
            rows="2"
            placeholder="Write your reply..."
          ></textarea>
          <div class="d-flex gap-2">
            <button 
              class="btn btn-primary btn-sm"
              @click="submitReply"
              :disabled="loading"
            >
              {{ loading ? 'Submitting...' : 'Submit Reply' }}
            </button>
            <button 
              class="btn btn-outline-secondary btn-sm"
              @click="showReplyForm = false"
              :disabled="loading"
            >
              Cancel
            </button>
          </div>
        </div>

        <!-- Nested Comments -->
        <div v-if="comment.replies && comment.replies.length > 0" 
             class="nested-comments mt-3">
          <CommentComponent
            v-for="reply in comment.replies"
            :key="reply.id"
            :comment="reply"
            :current-user-id="currentUserId"
            @reply="$emit('reply', $event)"
            @update="$emit('update', $event)"
            @delete="$emit('delete', $event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.nested-comments {
  margin-left: 2rem;
  border-left: 2px solid #dee2e6;
  padding-left: 1rem;
}

.btn-link {
  text-decoration: none;
}

.dropdown-toggle::after {
  display: none;
}
</style>
