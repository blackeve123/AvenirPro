<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getUserResults, resetTest } from '../services/test'
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
import JobResultCard from '../components/JobResultCard.vue'

const router = useRouter()
const results  = ref(null)
const loading  = ref(true)
const error    = ref('')

// ── Data helpers ────────────────────────────────────────────────────────────

const jobs = computed(() => results.value?.recommended_jobs ?? [])
const profile = computed(() => results.value?.profile ?? '')
const topProfiles = computed(() => results.value?.top_profiles ?? [])
const scores = computed(() => {
  const raw = results.value?.scores ?? {}
  return Object.entries(raw).map(([code, data]) => ({
    code,
    name:  typeof data === 'object' ? data.name  : code,
    score: typeof data === 'object' ? data.score : data,
  }))
})

const maxScore = computed(() => {
  return Math.max(...scores.value.map(s => s.score), 1)
})

const profileColors = {
  R: { bg: 'from-amber-500 to-orange-400',  border: '#f97316', text: '#9a3412' },
  I: { bg: 'from-blue-600 to-indigo-500',   border: '#4f46e5', text: '#1e1b4b' },
  A: { bg: 'from-fuchsia-500 to-pink-500',  border: '#ec4899', text: '#831843' },
  S: { bg: 'from-emerald-500 to-teal-400',  border: '#10b981', text: '#065f46' },
  E: { bg: 'from-orange-500 to-red-400',    border: '#ef4444', text: '#7f1d1d' },
  C: { bg: 'from-sky-500 to-cyan-400',      border: '#0ea5e9', text: '#0c4a6e' },
}

const barColor = (code) => {
  const map = { R:'#f97316', I:'#4f46e5', A:'#ec4899', S:'#10b981', E:'#ef4444', C:'#0ea5e9' }
  return map[code] ?? '#64748b'
}

const getProfileBg = (code) => profileColors[code]?.bg ?? 'from-slate-500 to-slate-400'

// ── Load ─────────────────────────────────────────────────────────────────────

onMounted(async () => {
  try {
    const response = await getUserResults()

    const data = response.data?.data ?? response.data

    if (!data || !data.profile) {
      router.push('/test')
      return
    }
    results.value = data
  } catch (err) {
    error.value = "Impossible d'analyser vos résultats. Veuillez réessayer."
  } finally {
    loading.value = false
  }
})


const isResetting = ref(false)

const retakeTest = async () => {
  if (isResetting.value) return
  isResetting.value = true
  try {
    await resetTest()
    router.push('/test')
  } catch (err) {
    error.value = "Impossible de recommencer le test."
  } finally {
    isResetting.value = false
  }
}
</script>

<template>
  <div class="page-wrapper">
    <Navbar />

    <main class="main-content">

      <!-- ── Loader ────────────────────────────────────────────────────────── -->
      <div v-if="loading" class="loader-wrap">
        <div class="loader-ring"></div>
        <div class="loader-text">
          <p class="loader-primary">
            Chargement de vos résultats...
          </p>
        </div>
      </div>

      <!-- ── Error ─────────────────────────────────────────────────────────── -->
      <div v-else-if="error" class="error-box">
        <div class="error-icon">😕</div>
        <h3>Oups...</h3>
        <p>{{ error }}</p>
        <button class="btn-retake" @click="retakeTest">Refaire le test</button>
      </div>

      <!-- ── Results ───────────────────────────────────────────────────────── -->
      <div v-else-if="results" class="results-wrap">

        <!-- Hero header -->
        <div class="results-hero">
          <div class="profile-badge">{{ profile }}</div>
          <h1 class="results-title">Ton Profil d&apos;Orientation</h1>
          <p class="results-sub">
            Voici les métiers qui correspondent le mieux à ta personnalité, enrichis par l'IA.
          </p>
        </div>

        <!-- ── Top RIASEC profile cards ──────────────────────────────────── -->
        <section v-if="topProfiles.length" class="section">
          <h2 class="section-title">Tes types dominants</h2>
          <div class="profiles-grid">
            <div
              v-for="(p, i) in topProfiles"
              :key="p.code"
              class="profile-card"
            >
              <div class="profile-bar bg-gradient-to-r" :class="getProfileBg(p.code)"></div>
              <div class="profile-body">
                <div class="profile-header">
                  <span class="profile-code bg-gradient-to-r bg-clip-text" :class="getProfileBg(p.code)">
                    {{ p.code }}
                  </span>
                  <span class="profile-rank">#{{ i + 1 }}</span>
                </div>
                <h3 class="profile-name">{{ p.name }}</h3>
                <p class="profile-desc">{{ p.description }}</p>
                <div class="profile-score">{{ p.score }} pts</div>
              </div>
            </div>
          </div>
        </section>

        <!-- ── RIASEC Score Chart ─────────────────────────────────────────── -->
        <section class="section">
          <h2 class="section-title">Détail des scores RIASEC</h2>
          <div class="score-chart">
            <div v-for="s in scores" :key="s.code" class="score-row">
              <div class="score-meta">
                <span class="score-code">{{ s.code }}</span>
                <span class="score-name">{{ s.name }}</span>
              </div>
              <div class="score-bar-wrap">
                <div
                  class="score-bar"
                  :style="`width: ${Math.max(4, Math.round((s.score / maxScore) * 100))}%; background: ${barColor(s.code)}`"
                ></div>
              </div>
              <span class="score-val">{{ s.score }}</span>
            </div>
          </div>
        </section>

        <!-- ── Job Cards ──────────────────────────────────────────────────── -->
        <section class="section">
          <div class="jobs-header">
            <h2 class="section-title mb-0">🎯 Métiers recommandés pour toi</h2>
            <router-link to="/metiers" class="see-all-link">Voir tous les métiers →</router-link>
          </div>

          <div class="jobs-grid">
            <JobResultCard
              v-for="(job, i) in jobs"
              :key="job.id"
              :id="job.id"
              :title="job.title"
              :image_url="job.image_url"
              :match_pct="job.match_pct"
              :reason="job.reason"
              :category="job.category"
              :description="job.description"
              :delay="i * 120"
            />
          </div>

          <div v-if="!jobs.length" class="no-jobs">
            <p>Aucun métier trouvé pour ce profil.</p>
            <button class="btn-retake" @click="retakeTest">Refaire le test</button>
          </div>
        </section>

        <!-- ── Footer CTA ─────────────────────────────────────────────────── -->
        <div class="footer-cta">
          <button class="btn-retake" @click="retakeTest">
            🔄 Refaire le test
          </button>
          <router-link to="/metiers" class="btn-explore">
            Explorer tous les métiers →
          </router-link>
        </div>

      </div>
    </main>

    <Footer />
  </div>
</template>

<style scoped>
/* ── Global layout ─────────────────────────────────────────────────────────── */
.page-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f8fafc;
  font-family: 'Inter', system-ui, sans-serif;
}
.main-content {
  flex: 1;
  padding: 2.5rem 1rem 4rem;
  max-width: 1100px;
  margin: 0 auto;
  width: 100%;
}

/* ── Loader ────────────────────────────────────────────────────────────────── */
.loader-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 65vh;
  gap: 1.5rem;
}
.loader-ring {
  width: 64px;
  height: 64px;
  border: 5px solid #e2e8f0;
  border-top-color: #4f46e5;
  border-radius: 50%;
  animation: spin 0.9s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.loader-text { text-align: center; }
.loader-primary {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }
.loader-spark { margin-right: .25rem; }
.loader-sub {
  font-size: .85rem;
  color: #94a3b8;
  margin: .35rem 0 0;
}

/* ── Error ─────────────────────────────────────────────────────────────────── */
.error-box {
  max-width: 480px;
  margin: 5rem auto;
  text-align: center;
  background: #fff;
  border-radius: 1.25rem;
  padding: 3rem 2rem;
  box-shadow: 0 4px 24px rgba(0,0,0,.08);
}
.error-icon { font-size: 3rem; margin-bottom: .75rem; }
.error-box h3 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 .5rem; }
.error-box p  { color: #64748b; margin: 0 0 1.5rem; }

/* ── Results ────────────────────────────────────────────────────────────────── */
.results-wrap {
  animation: fadeUp .5s ease both;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Hero */
.results-hero {
  text-align: center;
  margin-bottom: 3rem;
}
.profile-badge {
  display: inline-block;
  font-size: 2.5rem;
  font-weight: 900;
  letter-spacing: .15em;
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: .5rem;
}
.results-title {
  font-size: clamp(1.75rem, 4vw, 2.5rem);
  font-weight: 900;
  color: #0f172a;
  margin: 0 0 .75rem;
}
.results-sub {
  font-size: 1rem;
  color: #64748b;
  max-width: 540px;
  margin: 0 auto;
  line-height: 1.6;
}

/* Section */
.section { margin-bottom: 3rem; }
.section-title {
  font-size: 1.3rem;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 1.25rem;
}
.mb-0 { margin-bottom: 0 !important; }

/* Profiles grid */
.profiles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
}
.profile-card {
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 12px rgba(0,0,0,.06);
  transition: transform .2s ease, box-shadow .2s ease;
}
.profile-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(0,0,0,.12);
}
.profile-bar {
  height: .35rem;
  width: 100%;
}
.profile-body { padding: 1.25rem 1.5rem; }
.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: .35rem;
}
.profile-code {
  font-size: 2.5rem;
  font-weight: 900;
  /* gradient text */
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.profile-rank {
  font-size: .8rem;
  font-weight: 700;
  color: #cbd5e1;
  letter-spacing: .05em;
}
.profile-name { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0 0 .4rem; }
.profile-desc { font-size: .83rem; color: #64748b; line-height: 1.5; margin: 0 0 .75rem; }
.profile-score {
  display: inline-block;
  font-size: .8rem;
  font-weight: 700;
  color: #fff;
  background: #4f46e5;
  padding: .25rem .7rem;
  border-radius: 9999px;
}

/* Score chart */
.score-chart {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem 2rem;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 12px rgba(0,0,0,.05);
  display: flex;
  flex-direction: column;
  gap: .75rem;
}
.score-row {
  display: grid;
  grid-template-columns: 130px 1fr 40px;
  align-items: center;
  gap: .75rem;
}
.score-meta {
  display: flex;
  align-items: center;
  gap: .5rem;
}
.score-code {
  font-size: .95rem;
  font-weight: 900;
  color: #0f172a;
  min-width: 1.2rem;
}
.score-name {
  font-size: .78rem;
  font-weight: 600;
  color: #64748b;
}
.score-bar-wrap {
  background: #f1f5f9;
  border-radius: 9999px;
  height: .6rem;
  overflow: hidden;
}
.score-bar {
  height: 100%;
  border-radius: 9999px;
  transition: width .8s cubic-bezier(.4,0,.2,1);
}
.score-val {
  font-size: .85rem;
  font-weight: 700;
  color: #334155;
  text-align: right;
}

/* Jobs section */
.jobs-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: .5rem;
}
.see-all-link {
  font-size: .85rem;
  font-weight: 700;
  color: #f97316;
  text-decoration: none;
}
.see-all-link:hover { text-decoration: underline; }

.jobs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.75rem;
}

.no-jobs {
  text-align: center;
  padding: 3rem;
  color: #94a3b8;
}

/* Footer CTAs */
.footer-cta {
  display: flex;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
  padding-top: 1rem;
}
.btn-retake {
  padding: .75rem 1.75rem;
  background: #1e3a5f;
  color: #fff;
  border: none;
  border-radius: .75rem;
  font-size: .95rem;
  font-weight: 700;
  cursor: pointer;
  transition: background .2s ease, transform .15s ease;
}
.btn-retake:hover {
  background: #0f172a;
  transform: translateY(-2px);
}
.btn-explore {
  padding: .75rem 1.75rem;
  background: linear-gradient(135deg, #f97316, #ef4444);
  color: #fff;
  border-radius: .75rem;
  font-size: .95rem;
  font-weight: 700;
  text-decoration: none;
  transition: opacity .2s ease, transform .15s ease;
}
.btn-explore:hover {
  opacity: .9;
  transform: translateY(-2px);
}

/* ── Responsive ───────────────────────────────────────────────────────────── */
@media (max-width: 640px) {
  .score-row {
    grid-template-columns: 110px 1fr 36px;
    gap: .5rem;
  }
  .jobs-grid {
    grid-template-columns: 1fr;
  }
  .profiles-grid {
    grid-template-columns: 1fr;
  }
  .jobs-header { flex-direction: column; align-items: flex-start; }
}
</style>
