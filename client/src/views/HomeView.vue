<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <!-- Brand Logo -->
        <div class="hero-brand">
          <div class="brand-icon-large">
            <svg
              width="40"
              height="40"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"
                fill="white"
              />
              <path
                d="M19 15L19.74 12.26L22.5 11.5L19.74 10.74L19 8L18.26 10.74L15.5 11.5L18.26 12.26L19 15Z"
                fill="white"
              />
              <path
                d="M5 15L5.74 12.26L8.5 11.5L5.74 10.74L5 8L4.26 10.74L1.5 11.5L4.26 12.26L5 15Z"
                fill="white"
              />
            </svg>
          </div>
          <h1 class="brand-title">
            <span class="blog-text">Blog</span><span class="space-text">Space</span>
          </h1>
        </div>

        <!-- Main Heading -->
        <h2 class="hero-title">Share your thoughts. Connect with people.</h2>

        <!-- Subtitle -->
        <p class="hero-subtitle">
          A simple, beautiful platform for writers and readers to create, discover, and engage with
          meaningful content.
        </p>

        <!-- CTA Buttons -->
        <div class="hero-buttons">
          <RouterLink to="/register" class="btn btn-primary btn-hero">
            Get Started
            <svg
              class="arrow-icon"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M5 12H19M19 12L12 5M19 12L12 19"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </RouterLink>
          <RouterLink to="/blog" class="btn btn-outline-secondary btn-hero-outline">
            Explore Posts
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Features Section Component -->
    <FeaturesSection />

    <!-- Blog Posts Section Component -->
    <BlogPostsSection />

    <!-- CTA Section Component -->
    <CtaSection />
  </div>
</template>

<script>
import FeaturesSection from '@/components/FeaturesSection.vue'
import BlogPostsSection from '@/components/BlogPostsSection.vue'
import CtaSection from '@/components/CtaSection.vue'

export default {
  name: 'HomeView',
  components: {
    FeaturesSection,
    BlogPostsSection,
    CtaSection,
  },
}
</script>

<style scoped>
/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 120px 0 80px;
  text-align: center;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Brand Logo */
.hero-brand {
  margin-bottom: 3rem;
}

.brand-icon-large {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  border-radius: 20px;
  margin: 0 auto 1.5rem;
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.brand-title {
  font-size: 3.5rem;
  font-weight: 700;
  margin: 0;
  line-height: 1.2;
}

.blog-text {
  color: #334155;
}

.space-text {
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Typography */
.hero-title {
  font-size: 2.5rem;
  font-weight: 600;
  color: #334155;
  line-height: 1.3;
  margin-bottom: 1.5rem;
}

.hero-subtitle {
  font-size: 1.125rem;
  color: #64748b;
  line-height: 1.6;
  font-weight: 400;
  margin-bottom: 3rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

/* Button Styles */
.hero-buttons {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn-hero {
  padding: 14px 28px;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 8px;
  border: none;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  min-width: 160px;
  justify-content: center;
  box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
}

.btn-hero:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
  color: white;
  text-decoration: none;
}

.btn-hero-outline {
  padding: 14px 28px;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 8px;
  border: 2px solid #e2e8f0;
  background-color: white;
  color: #64748b;
  text-decoration: none;
  transition: all 0.3s ease;
  min-width: 160px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-hero-outline:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
  color: #475569;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  text-decoration: none;
}

.arrow-icon {
  transition: transform 0.3s ease;
}

.btn-hero:hover .arrow-icon {
  transform: translateX(4px);
}

/* Responsive Design */
@media (max-width: 1200px) {
  .brand-title {
    font-size: 3rem;
  }

  .hero-title {
    font-size: 2.25rem;
  }
}

@media (max-width: 992px) {
  .hero-section {
    padding: 100px 0 60px;
  }

  .brand-icon-large {
    width: 70px;
    height: 70px;
  }

  .brand-title {
    font-size: 2.75rem;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-subtitle {
    font-size: 1.1rem;
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 80px 0 40px;
  }

  .brand-icon-large {
    width: 60px;
    height: 60px;
  }

  .brand-title {
    font-size: 2.25rem;
  }

  .hero-title {
    font-size: 1.75rem;
    line-height: 1.3;
  }

  .hero-subtitle {
    font-size: 1rem;
    margin-bottom: 2rem;
  }

  .hero-brand {
    margin-bottom: 2rem;
  }

  .hero-buttons {
    flex-direction: column;
    gap: 0.75rem;
  }

  .btn-hero,
  .btn-hero-outline {
    width: 100%;
    max-width: 280px;
    padding: 16px 20px;
  }
}

@media (max-width: 576px) {
  .hero-section {
    padding: 60px 0 30px;
  }

  .brand-icon-large {
    width: 50px;
    height: 50px;
  }

  .brand-title {
    font-size: 2rem;
  }

  .hero-title {
    font-size: 1.5rem;
  }

  .hero-subtitle {
    font-size: 0.95rem;
  }

  .hero-content {
    padding: 0 1rem;
  }
}

/* Animation for initial load */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-brand {
  animation: fadeInUp 0.8s ease-out;
}

.hero-title {
  animation: fadeInUp 0.8s ease-out 0.2s;
  animation-fill-mode: both;
}

.hero-subtitle {
  animation: fadeInUp 0.8s ease-out 0.4s;
  animation-fill-mode: both;
}

.hero-buttons {
  animation: fadeInUp 0.8s ease-out 0.6s;
  animation-fill-mode: both;
}

/* Focus states for accessibility */
.btn-hero:focus,
.btn-hero-outline:focus {
  outline: 3px solid rgba(59, 130, 246, 0.5);
  outline-offset: 2px;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .btn-hero,
  .btn-hero-outline {
    transition: none;
  }

  .hero-brand,
  .hero-title,
  .hero-subtitle,
  .hero-buttons {
    animation: none;
  }
}

/* Features Responsive */
@media (max-width: 992px) {
  .features-section {
    padding: 80px 0;
  }

  .features-title {
    font-size: 2.25rem;
  }

  .features-subtitle {
    font-size: 1.1rem;
    margin-bottom: 3rem;
  }
}

@media (max-width: 768px) {
  .features-section {
    padding: 60px 0;
  }

  .features-title {
    font-size: 2rem;
  }

  .features-subtitle {
    font-size: 1rem;
    margin-bottom: 2.5rem;
  }

  .feature-card {
    padding: 1.5rem 1rem;
    margin-bottom: 2rem;
  }

  .feature-icon {
    width: 56px;
    height: 56px;
    font-size: 1.6rem;
  }

  .feature-title {
    font-size: 1.375rem;
  }
}

@media (max-width: 576px) {
  .features-section {
    padding: 50px 0;
  }

  .features-title {
    font-size: 1.75rem;
  }

  .features-subtitle {
    font-size: 0.95rem;
  }

  .feature-card {
    padding: 1.25rem 0.75rem;
  }
}
</style>
