<template>
  <section class="cta-section">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
          <div class="cta-content">
            <!-- Star Icon -->
            <div class="cta-icon mb-4">
              <svg
                width="48"
                height="48"
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

            <!-- Title -->
            <h2 class="cta-title mb-4">Ready to Share Your Story?</h2>

            <!-- Subtitle -->
            <p class="cta-subtitle mb-5">
              Join thousands of writers who are already sharing their thoughts on BlogSpace
            </p>

            <!-- CTA Button -->
            <RouterLink to="/register" class="btn btn-cta">
              Start Writing Today
              <svg
                class="arrow-icon ms-2"
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
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'CtaSection',
}
</script>

<style scoped>
.cta-section {
  background: linear-gradient(135deg, #4f7df8 0%, #7b68ee 100%);
  padding: 100px 0;
  position: relative;
  overflow: hidden;
}

.cta-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(79, 125, 248, 0.9) 0%, rgba(123, 104, 238, 0.9) 100%);
  z-index: 1;
}

.container {
  position: relative;
  z-index: 2;
}

.cta-content {
  max-width: 600px;
  margin: 0 auto;
}

.cta-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  animation: sparkle 2s ease-in-out infinite;
}

.cta-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: white;
  margin-bottom: 1.5rem;
}

.cta-subtitle {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.9);
  line-height: 1.6;
  margin-bottom: 0;
}

.btn-cta {
  background: white;
  color: #4f7df8;
  border: none;
  padding: 16px 32px;
  font-size: 1.125rem;
  font-weight: 600;
  border-radius: 12px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-cta:hover {
  background: #f8f9fa;
  color: #4f7df8;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  text-decoration: none;
}

.arrow-icon {
  transition: transform 0.3s ease;
}

.btn-cta:hover .arrow-icon {
  transform: translateX(4px);
}

/* Animations */
@keyframes sparkle {
  0%,
  100% {
    transform: scale(1) rotate(0deg);
    opacity: 1;
  }
  50% {
    transform: scale(1.1) rotate(180deg);
    opacity: 0.8;
  }
}

/* Responsive Design */
@media (max-width: 992px) {
  .cta-section {
    padding: 80px 0;
  }

  .cta-title {
    font-size: 2.25rem;
  }

  .cta-subtitle {
    font-size: 1.125rem;
  }
}

@media (max-width: 768px) {
  .cta-section {
    padding: 60px 0;
  }

  .cta-title {
    font-size: 2rem;
  }

  .cta-subtitle {
    font-size: 1rem;
  }

  .btn-cta {
    padding: 14px 28px;
    font-size: 1rem;
  }
}

@media (max-width: 576px) {
  .cta-section {
    padding: 50px 0;
  }

  .cta-title {
    font-size: 1.75rem;
  }

  .cta-subtitle {
    font-size: 0.95rem;
  }

  .btn-cta {
    padding: 12px 24px;
    font-size: 0.95rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  .cta-icon {
    animation: none;
  }

  .btn-cta:hover {
    transform: none;
  }

  .btn-cta:hover .arrow-icon {
    transform: none;
  }
}
</style>
