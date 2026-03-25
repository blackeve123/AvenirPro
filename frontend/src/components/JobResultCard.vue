<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  id:          { type: Number, required: true },
  title:       { type: String, required: true },
  image_url:   { type: String, default: null },
  match_pct:   { type: Number, default: 0 },
  reason:      { type: String, default: '' },
  category:    { type: String, default: '' },
  description: { type: String, default: '' },
  delay:       { type: Number, default: 0 }, // animation stagger delay in ms
})

const goToJob = () => {
  router.push(`/metiers/${props.id}`)
}

const matchColor = (pct) => {
  if (pct >= 75) return '#22c55e'
  if (pct >= 50) return '#f97316'
  return '#64748b'
}

const matchLabel = (pct) => {
  if (pct >= 75) return 'Excellent'
  if (pct >= 50) return 'Bon match'
  return 'Compatible'
}
</script>

<template>
  <article
    class="job-result-card"
    :style="`--delay: ${delay}ms`"
    @click="goToJob"
  >
    <!-- Image Area -->
    <div class="card-image">
      <img
        v-if="image_url"
        :src="image_url"
        :alt="title"
        class="card-img"
      />
      <div v-else class="card-img-placeholder">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="placeholder-icon">
          <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Zm-9 6H5V9h6v4Zm8 0h-6V9h6v4Z"/>
        </svg>
      </div>

      <!-- Match badge -->
      <div class="match-badge" :style="`background: ${matchColor(match_pct)}`">
        <span class="match-pct">{{ match_pct }}%</span>
        <span class="match-label">{{ matchLabel(match_pct) }}</span>
      </div>
    </div>

    <!-- Content -->
    <div class="card-body">
      <span v-if="category" class="card-category">{{ category }}</span>
      <h3 class="card-title">{{ title }}</h3>

      <!-- AI Reason -->
      <div v-if="reason" class="card-reason">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="reason-icon">
          <path d="M9.664 1.319a.75.75 0 0 1 .672 0 41.059 41.059 0 0 1 8.198 5.424.75.75 0 0 1-.254 1.285 31.372 31.372 0 0 0-7.86 3.83.75.75 0 0 1-.84 0 31.508 31.508 0 0 0-2.08-1.287V9.394c0-.244.116-.463.29-.592a32.912 32.912 0 0 0 .598-.45 41.15 41.15 0 0 1 .82-.553 19.623 19.623 0 0 0-3.595 1.422.75.75 0 0 1-.254-1.285 41.059 41.059 0 0 1 8.198-5.424Z"/>
          <path d="M3.842 11.323A32.955 32.955 0 0 1 10 8.755a32.916 32.916 0 0 1 6.158 2.568c.09.548.139 1.11.139 1.682a19.683 19.683 0 0 1-.214 2.926.75.75 0 0 1-.526.619l-.995.243a12.008 12.008 0 0 0-1.012-.473.75.75 0 0 0-.494 0 12.01 12.01 0 0 0-4.112 0 .75.75 0 0 0-.494 0 12.008 12.008 0 0 0-1.012.473l-.995-.243a.75.75 0 0 1-.526-.619 19.683 19.683 0 0 1-.214-2.926c0-.572.048-1.134.139-1.682Z"/>
        </svg>
        <p class="reason-text">"{{ reason }}"</p>
      </div>
      <p v-else-if="description" class="card-desc">{{ description.substring(0, 100) }}{{ description.length > 100 ? '…' : '' }}</p>

      <!-- CTA -->
      <button class="card-cta" @click.stop="goToJob">
        <span>Voir le parcours</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="cta-arrow">
          <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
        </svg>
      </button>
    </div>
  </article>
</template>

<style scoped>
.job-result-card {
  background: #fff;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  display: flex;
  flex-direction: column;
  animation: cardFadeUp 0.55s ease both;
  animation-delay: var(--delay, 0ms);
}

.job-result-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.14);
}

@keyframes cardFadeUp {
  from { opacity: 0; transform: translateY(32px) scale(0.98); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}


/* Image */
.card-image {
  position: relative;
  height: 180px;
  overflow: hidden;
  background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
  flex-shrink: 0;
}

.card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.job-result-card:hover .card-img {
  transform: scale(1.07);
}

.card-img-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
}
.placeholder-icon {
  width: 3rem;
  height: 3rem;
  color: rgba(255, 255, 255, 0.35);
}

/* Match badge with glassmorphism */
.match-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  border-radius: 1rem;
  padding: 0.4rem 0.85rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  line-height: 1;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  z-index: 10;
}
.match-pct {
  font-size: 1.1rem;
  font-weight: 900;
  color: #1e3a5f;
}
.match-label {
  font-size: 0.55rem;
  font-weight: 800;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-top: 0.1rem;
}


/* Body */
.card-body {
  padding: 1.25rem 1.5rem 1.5rem;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.card-category {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #f97316;
  margin-bottom: 0.35rem;
}

.card-title {
  font-size: 1.15rem;
  font-weight: 800;
  color: #1e3a5f;
  margin: 0 0 0.75rem;
  line-height: 1.3;
}

/* AI reason - Enhanced for 'Flow' */
.card-reason {
  display: flex;
  gap: 0.75rem;
  align-items: flex-start;
  background: #f8fafc;
  border-radius: 0.85rem;
  padding: 0.85rem 1rem;
  margin-bottom: 1.25rem;
  position: relative;
  border: 1px solid #f1f5f9;
}
.card-reason::before {
  content: '“';
  position: absolute;
  top: -0.2rem;
  left: 0.5rem;
  font-size: 2rem;
  font-family: serif;
  color: #cbd5e1;
  opacity: 0.5;
}
.reason-icon {
  width: 1.25rem;
  height: 1.25rem;
  color: #6366f1;
  flex-shrink: 0;
  margin-top: 0.15rem;
}
.reason-text {
  font-size: 0.88rem;
  color: #334155;
  font-weight: 500;
  line-height: 1.5;
  margin: 0;
}


.card-desc {
  font-size: 0.83rem;
  color: #64748b;
  line-height: 1.5;
  margin: 0 0 1rem;
  flex: 1;
}

/* CTA */
.card-cta {
  margin-top: auto;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.7rem 1rem;
  background: transparent;
  border: 2px solid #1e3a5f;
  border-radius: 0.75rem;
  color: #1e3a5f;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
}
.card-cta:hover {
  background: #1e3a5f;
  color: #fff;
}
.cta-arrow {
  width: 1rem;
  height: 1rem;
  transition: transform 0.2s ease;
}
.card-cta:hover .cta-arrow {
  transform: translateX(4px);
}
</style>
