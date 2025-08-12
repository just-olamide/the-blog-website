<template>
  <section class="blog-posts-section">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center mb-5">
          <h2 class="section-title">See What Others Are Sharing</h2>
          <p class="section-subtitle">Real stories from our community</p>
        </div>
      </div>

      <div class="row justify-content-center">
        <!-- Blog Post 1 -->
        <div class="col-lg-8 col-md-10 mb-5">
          <div class="blog-post-card">
            <div class="post-header">
              <div class="author-avatar purple-avatar">S</div>
              <div class="author-info">
                <h4 class="author-name">Sarah Chen</h4>
                <p class="post-date">2 days ago</p>
              </div>
            </div>

            <div class="post-content">
              <h3 class="post-title">5 Morning Habits That Changed My Life</h3>
              <p class="post-excerpt">
                Discovering the power of morning routines transformed not just my days, but my
                entire perspective on productivity and well-being...
              </p>
            </div>

            <div class="post-stats">
              <div class="stat-item">
                <i class="bi bi-heart"></i>
                <span>24</span>
              </div>
              <div class="stat-item">
                <i class="bi bi-chat"></i>
                <span>8 comments</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Blog Post 2 -->
        <div class="col-lg-8 col-md-10 mb-5">
          <div class="blog-post-card">
            <div class="post-header">
              <div class="author-avatar blue-avatar">M</div>
              <div class="author-info">
                <h4 class="author-name">Marcus Johnson</h4>
                <p class="post-date">4 days ago</p>
              </div>
            </div>

            <div class="post-content">
              <h3 class="post-title">Building My First App: Lessons Learned</h3>
              <p class="post-excerpt">
                Six months ago, I was a complete beginner in programming. Today, I'm launching my
                first mobile application...
              </p>
            </div>

            <div class="post-stats">
              <div class="stat-item">
                <i class="bi bi-heart"></i>
                <span>42</span>
              </div>
              <div class="stat-item">
                <i class="bi bi-chat"></i>
                <span>15 comments</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'BlogPostsSection',
}
</script>

<style scoped>
/* Blog Posts Section */
.blog-posts-section {
  background-color: #f8f9fa;
  padding: 100px 0;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.125rem;
  color: #718096;
  margin-bottom: 0;
}

.blog-post-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
}

.blog-post-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.post-header {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
}

.author-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1.25rem;
  color: white;
  margin-right: 1rem;
  flex-shrink: 0;
}

.purple-avatar {
  background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
}

.blue-avatar {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.author-info {
  flex: 1;
}

.author-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 0.25rem 0;
}

.post-date {
  font-size: 0.875rem;
  color: #718096;
  margin: 0;
}

.post-content {
  margin-bottom: 1.5rem;
}

.post-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 0.75rem;
  line-height: 1.3;
}

.post-excerpt {
  font-size: 1rem;
  color: #4a5568;
  line-height: 1.6;
  margin: 0;
}

.post-stats {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #718096;
  font-size: 0.875rem;
}

.stat-item i {
  font-size: 1rem;
}

/* Blog Posts Responsive */
@media (max-width: 992px) {
  .blog-posts-section {
    padding: 80px 0;
  }

  .section-title {
    font-size: 2.25rem;
  }
}

@media (max-width: 768px) {
  .blog-posts-section {
    padding: 60px 0;
  }

  .section-title {
    font-size: 2rem;
  }

  .section-subtitle {
    font-size: 1rem;
  }

  .blog-post-card {
    padding: 1.5rem;
  }

  .post-title {
    font-size: 1.375rem;
  }
}

@media (max-width: 576px) {
  .blog-posts-section {
    padding: 50px 0;
  }

  .section-title {
    font-size: 1.75rem;
  }

  .blog-post-card {
    padding: 1.25rem;
  }

  .post-header {
    margin-bottom: 1.25rem;
  }

  .author-avatar {
    width: 40px;
    height: 40px;
    font-size: 1.125rem;
  }

  .post-title {
    font-size: 1.25rem;
  }

  .post-stats {
    gap: 1rem;
  }
}
</style>
