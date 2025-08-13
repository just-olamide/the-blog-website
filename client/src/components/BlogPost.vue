<script>
export default {
  name: 'BlogPost',
  props: {
    post: {
      type: Object,
      required: true,
    },
  },
  computed: {
    formattedDate() {
      return new Date(this.post.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    },
  },
  methods: {
    truncateContent(content, length = 200) {
      if (content.length <= length) return content
      return content.substring(0, length) + '...'
    },
  },
}
</script>

<template>
  <div class="blog-post card mb-4 shadow-sm">
    <div v-if="post.featured_image" class="post-image">
      <img
        :src="`http://localhost:8000/storage/${post.featured_image}`"
        :alt="post.title"
        class="card-img-top"
      />
    </div>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="badge bg-primary">
          {{ post.category.name }}
        </span>
        <small class="text-muted">{{ formattedDate }}</small>
      </div>

      <h2 class="card-title h4">
        <RouterLink
          :to="{ name: 'post', params: { slug: post.slug } }"
          class="text-decoration-none text-dark"
        >
          {{ post.title }}
        </RouterLink>
      </h2>

      <p class="card-text">{{ truncateContent(post.content) }}</p>

      <div class="d-flex justify-content-between align-items-center">
        <div class="btn-group">
          <RouterLink
            :to="{ name: 'post', params: { slug: post.slug } }"
            class="btn btn-sm btn-outline-primary"
          >
            Read More
          </RouterLink>
        </div>
        <div class="post-meta">
          <span class="me-3"> <i class="bi bi-eye"></i> {{ post.view_count }} </span>
          <span> <i class="bi bi-heart"></i> {{ post.like_count }} </span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.blog-post {
  transition: transform 0.2s;
}

.blog-post:hover {
  transform: translateY(-5px);
}

.post-image {
  height: 200px;
  overflow: hidden;
}

.post-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>
