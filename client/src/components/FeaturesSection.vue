<template>
  <!-- Features Section -->
  <section class="features-section">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center mb-5">
          <h2 class="features-title">Why Choose BlogSpace?</h2>
          <p class="features-subtitle">Everything you need to start sharing your stories</p>
        </div>
      </div>

      <div class="row justify-content-center">
        <!-- Feature 1: Easy Writing -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="feature-card text-center">
            <div class="feature-icon blue-icon mb-4">
              <i class="bi bi-journal-text"></i>
            </div>
            <h3 class="feature-title">Easy Writing</h3>
            <p class="feature-description">
              Create beautiful posts with our intuitive editor. No technical skills required - just
              focus on your content.
            </p>
          </div>
        </div>

        <!-- Feature 2: Find Your Audience -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="feature-card text-center">
            <div class="feature-icon purple-icon mb-4">
              <i class="bi bi-people"></i>
            </div>
            <h3 class="feature-title">Find Your Audience</h3>
            <p class="feature-description">
              Connect with readers who appreciate your content. Build meaningful relationships
              through your writing.
            </p>
          </div>
        </div>

        <!-- Feature 3: Engage & Interact -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="feature-card text-center">
            <div class="feature-icon orange-icon mb-4">
              <i class="bi bi-heart"></i>
            </div>
            <h3 class="feature-title">Engage & Interact</h3>
            <p class="feature-description">
              Like posts you love and join conversations through comments. Be part of a vibrant
              community.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'FeaturesSection',
}
</script>

<style scoped>
/* Features Section */
.features-section {
  background-color: #ffffff;
  padding: 100px 0;
}

.features-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 1rem;
}

.features-subtitle {
  font-size: 1.125rem;
  color: #6c757d;
  font-weight: 400;
  margin-bottom: 4rem;
}

.feature-card {
  padding: 2rem 1.5rem;
  border-radius: 12px;
  transition: all 0.3s ease;
  height: 100%;
  border: 1px solid #f1f3f4;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  color: white;
  margin: 0 auto;
}

.blue-icon {
  background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
}

.purple-icon {
  background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
}

.orange-icon {
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
}

.feature-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 1rem;
}

.feature-description {
  font-size: 1rem;
  color: #6c757d;
  line-height: 1.6;
  margin: 0;
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

/* Features Animation */
.feature-card {
  animation: fadeInUp 0.8s ease-out;
  animation-fill-mode: both;
}

.feature-card:nth-child(1) {
  animation-delay: 0.1s;
}

.feature-card:nth-child(2) {
  animation-delay: 0.2s;
}

.feature-card:nth-child(3) {
  animation-delay: 0.3s;
}

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

@media (prefers-reduced-motion: reduce) {
  .feature-card {
    animation: none;
    transition: none;
  }

  .feature-card:hover {
    transform: none;
  }
}
</style>
