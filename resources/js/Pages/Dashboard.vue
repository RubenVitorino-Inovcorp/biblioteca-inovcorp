<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Book, UserPen, Building, ArrowRight } from '@lucide/vue';

const searchQuery = ref('');

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('catalog.livros.index'), { search: searchQuery.value.trim() });
    } else {
        router.get(route('catalog.livros.index'));
    }
};
</script>

<template>
    <AppLayout title="Início" :hideSidebar="true" :noPadding="true">
        <template #header>
            <div class="flex justify-center w-full">
                <nav class="homepage-nav">
                    <Link :href="route('catalog.livros.index')" class="homepage-nav-link">
                        <Book :size="16" /> Livros
                    </Link>
                    <Link :href="route('catalog.autores.index')" class="homepage-nav-link">
                        <UserPen :size="16" /> Autores
                    </Link>
                    <Link :href="route('catalog.editoras.index')" class="homepage-nav-link">
                        <Building :size="16" /> Editoras
                    </Link>
                </nav>
            </div>
        </template>

        <!-- Hero Section -->
        <section class="hero">
            <!-- Background Video with Overlay -->
            <div class="hero-bg">
                <video
                    class="hero-video"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="auto"
                >
                    <source src="/storage/hero/hero.mp4" type="video/mp4" />
                </video>
                <div class="hero-overlay"></div>
            </div>

            <div class="hero-content">
                <h1 class="hero-title">
                    A Sua Biblioteca<br class="hero-break" /> Digital
                </h1>
                <p class="hero-subtitle">
                    Explore o nosso catálogo de livros, descubra autores e editoras.
                    Requisite livros de forma simples e rápida.
                </p>

                <!-- Search Bar -->
                <form @submit.prevent="handleSearch" class="hero-search">
                    <div class="hero-search-inner">
                        <div class="hero-search-field">
                            <svg class="hero-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <input
                                v-model="searchQuery"
                                type="text"
                                class="hero-search-input"
                                placeholder="Pesquisar por título, autor ou editora..."
                                @keyup.enter="handleSearch"
                                aria-label="Pesquisar por título, autor ou editora"
                            />
                        </div>
                        <button type="submit" class="hero-search-btn" aria-label="Pesquisar">
                            Pesquisar
                        </button>
                    </div>
                </form>

                <!-- Stats -->
                <div class="hero-stats">
                    <span class="hero-stat">
                        <Book :size="16" />
                        Livros
                    </span>
                    <span class="hero-stat-dot"></span>
                    <span class="hero-stat">
                        <UserPen :size="16" />
                        Autores
                    </span>
                    <span class="hero-stat-dot"></span>
                    <span class="hero-stat">
                        <Building :size="16" />
                        Editoras
                    </span>
                </div>
            </div>
        </section>

        <!-- Featured Collections -->
        <section class="collections">
            <div class="collections-header">
                <div>
                    <h2 class="collections-title">Coleções em Destaque</h2>
                    <p class="collections-subtitle">Explore o catálogo organizado por categorias.</p>
                </div>
                <Link :href="route('catalog.livros.index')" class="collections-view-all">
                    Ver Catálogo Completo
                    <ArrowRight :size="18" />
                </Link>
            </div>

            <div class="collections-grid">
                <!-- Livros (Large Card) -->
                <Link :href="route('catalog.livros.index')" class="collection-card collection-card--large">
                    <div class="collection-card-bg"></div>
                    <div class="collection-card-top">
                        <span class="collection-icon collection-icon--primary">
                            <Book :size="24" />
                        </span>
                        <span class="collection-badge">Catálogo</span>
                    </div>
                    <div class="collection-card-body">
                        <h3 class="collection-card-title">Livros</h3>
                        <p class="collection-card-desc">
                            Explore toda a coleção de livros disponíveis na biblioteca. Pesquise por título, autor ou editora e requisite os livros que pretender.
                        </p>
                    </div>
                </Link>

                <div class="collections-side">
                    <!-- Autores (Small Card) -->
                    <Link :href="route('catalog.autores.index')" class="collection-card collection-card--small">
                        <span class="collection-icon collection-icon--secondary">
                            <UserPen :size="20" />
                        </span>
                        <h3 class="collection-card-title collection-card-title--sm">Autores</h3>
                        <p class="collection-card-desc">Descubra os autores presentes no nosso catálogo e as suas obras.</p>
                    </Link>

                    <!-- Editoras (Small Card) -->
                    <Link :href="route('catalog.editoras.index')" class="collection-card collection-card--small">
                        <span class="collection-icon collection-icon--tertiary">
                            <Building :size="20" />
                        </span>
                        <h3 class="collection-card-title collection-card-title--sm">Editoras</h3>
                        <p class="collection-card-desc">Consulte as editoras e os títulos publicados por cada uma.</p>
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
/* ───────────────────────────────────────────
   Homepage Nav Links (header slot)
   ─────────────────────────────────────────── */
.homepage-nav {
    display: flex;
    align-items: center;
    gap: 8px;
}

.homepage-nav-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #404944;
    text-decoration: none;
    padding: 6px 14px;
    border-radius: 6px;
    transition: color 0.15s ease, background 0.15s ease;
}

.homepage-nav-link:hover {
    color: #003527;
    background: rgba(0, 53, 39, 0.06);
}

/* ───────────────────────────────────────────
   Hero Section — Full Bleed
   ─────────────────────────────────────────── */
.hero {
    position: relative;
    min-height: 100vh;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 72px 20px 64px;
    overflow: hidden;
    /* Pull hero up behind the floating header */
    margin-top: -120px;
}

@media (min-width: 768px) {
    .hero {
        padding: 72px 64px 80px;
    }
}

/* Background Video + Overlay */
.hero-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
}

.hero-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.3;
    mix-blend-mode: multiply;
    filter: grayscale(30%);
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(228, 226, 222, 0.92) 0%,
        rgba(228, 226, 222, 0.85) 40%,
        rgba(244, 241, 234, 1) 100%
    );
    z-index: 1;
}

/* Hero Content */
.hero-content {
    position: relative;
    z-index: 2;
    max-width: 900px;
    width: 100%;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    /* Extra top padding to clear the floating header */
    padding-top: 80px;
}

@media (min-width: 768px) {
    .hero-content {
        padding-top: 40px;
    }
}

.hero-title {
    font-family: 'EB Garamond', serif;
    font-size: 42px;
    font-weight: 500;
    line-height: 1.15;
    letter-spacing: -0.01em;
    color: #003527;
    margin-bottom: 20px;
}

@media (min-width: 768px) {
    .hero-title {
        font-size: 64px;
        letter-spacing: -0.02em;
        line-height: 1.12;
        margin-bottom: 24px;
    }
}

.hero-break {
    display: none;
}

@media (min-width: 768px) {
    .hero-break {
        display: block;
    }
}

.hero-subtitle {
    font-family: 'Manrope', sans-serif;
    font-size: 16px;
    font-weight: 400;
    line-height: 1.65;
    color: #404944;
    max-width: 560px;
    margin-bottom: 40px;
}

@media (min-width: 768px) {
    .hero-subtitle {
        font-size: 18px;
        margin-bottom: 48px;
    }
}

/* Hero Search Bar */
.hero-search {
    width: 100%;
    max-width: 720px;
}

.hero-search-inner {
    display: flex;
    flex-direction: column;
    gap: 8px;
    background: #ffffff;
    padding: 8px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(6, 78, 59, 0.08);
    border: 1px solid rgba(191, 201, 195, 0.2);
}

@media (min-width: 640px) {
    .hero-search-inner {
        flex-direction: row;
        align-items: stretch;
    }
}

.hero-search-field {
    flex: 1;
    display: flex;
    align-items: center;
    padding: 0 16px;
    background: #f5f3f6;
    border-radius: 6px;
    transition: border-color 0.2s ease;
    border-bottom: 2px solid transparent;
}

.hero-search-field:focus-within {
    border-bottom-color: #e3c280;
}

.hero-search-icon {
    width: 20px;
    height: 20px;
    color: #5e5e5c;
    margin-right: 12px;
    flex-shrink: 0;
}

.hero-search-input {
    width: 100%;
    background: transparent;
    border: none;
    outline: none;
    font-family: 'Manrope', sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: #1b1b1e;
    padding: 14px 0;
}

.hero-search-input::placeholder {
    color: rgba(64, 73, 68, 0.5);
}

.hero-search-input:focus {
    box-shadow: none;
}

.hero-search-btn {
    background: #003527;
    color: #ffffff;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 14px 32px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s ease;
    white-space: nowrap;
}

.hero-search-btn:hover {
    background: rgba(0, 53, 39, 0.9);
}

/* Hero Stats */
.hero-stats {
    margin-top: 32px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    justify-content: center;
}

.hero-stat {
    display: flex;
    align-items: center;
    gap: 6px;
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #404944;
    opacity: 0.75;
}

.hero-stat-dot {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #bfc9c3;
}

/* ───────────────────────────────────────────
   Collections Section
   ─────────────────────────────────────────── */
.collections {
    max-width: 1280px;
    margin: 0 auto;
    padding: 80px 20px;
}

@media (min-width: 768px) {
    .collections {
        padding: 120px 64px;
    }
}

.collections-header {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 48px;
    padding-bottom: 24px;
    border-bottom: 1px solid rgba(191, 201, 195, 0.2);
}

@media (min-width: 768px) {
    .collections-header {
        flex-direction: row;
        align-items: flex-end;
        justify-content: space-between;
        gap: 32px;
        margin-bottom: 56px;
    }
}

.collections-title {
    font-family: 'EB Garamond', serif;
    font-size: 28px;
    font-weight: 500;
    color: #003527;
    margin-bottom: 4px;
}

@media (min-width: 768px) {
    .collections-title {
        font-size: 32px;
    }
}

.collections-subtitle {
    font-family: 'Manrope', sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: #404944;
}

.collections-view-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #003527;
    text-decoration: none;
    padding-bottom: 4px;
    border-bottom: 1px solid #e3c280;
    transition: color 0.2s ease, opacity 0.2s ease;
    white-space: nowrap;
    flex-shrink: 0;
}

.collections-view-all:hover {
    opacity: 0.75;
}

/* Collections Grid */
.collections-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 768px) {
    .collections-grid {
        grid-template-columns: 2fr 1fr;
        gap: 32px;
    }
}

.collections-side {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

@media (min-width: 768px) {
    .collections-side {
        gap: 32px;
    }
}

/* Collection Card */
.collection-card {
    background: #ffffff;
    border: 1px solid rgba(191, 201, 195, 0.2);
    border-radius: 10px;
    padding: 32px;
    text-decoration: none;
    color: inherit;
    transition: box-shadow 0.3s ease, transform 0.2s ease;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.collection-card:hover {
    box-shadow: 0 4px 20px rgba(6, 78, 59, 0.08);
    transform: translateY(-2px);
}

.collection-card--large {
    min-height: 340px;
    justify-content: space-between;
}

@media (min-width: 768px) {
    .collection-card--large {
        min-height: 400px;
        padding: 40px;
    }
}

.collection-card--small {
    flex: 1;
    padding: 24px;
}

@media (min-width: 768px) {
    .collection-card--small {
        padding: 28px;
    }
}

.collection-card-bg {
    position: absolute;
    inset: 0;
    background: rgba(245, 243, 246, 0.3);
    transition: background 0.3s ease;
    z-index: 0;
}

.collection-card:hover .collection-card-bg {
    background: transparent;
}

.collection-card-top {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 40px;
}

@media (min-width: 768px) {
    .collection-card-top {
        margin-bottom: 60px;
    }
}

.collection-card-body {
    position: relative;
    z-index: 1;
}

.collection-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin-bottom: 16px;
    flex-shrink: 0;
}

.collection-icon--primary {
    background: rgba(0, 108, 73, 0.08);
    color: #003527;
}

.collection-icon--secondary {
    background: rgba(94, 94, 92, 0.08);
    color: #5e5e5c;
    width: 40px;
    height: 40px;
}

.collection-icon--tertiary {
    background: rgba(61, 43, 0, 0.06);
    color: #3d2b00;
    width: 40px;
    height: 40px;
}

.collection-badge {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #404944;
    background: #f5f3f6;
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid rgba(191, 201, 195, 0.3);
}

.collection-card-title {
    font-family: 'EB Garamond', serif;
    font-size: 28px;
    font-weight: 500;
    color: #003527;
    margin-bottom: 12px;
    transition: opacity 0.2s ease;
}

.collection-card:hover .collection-card-title {
    opacity: 0.8;
}

.collection-card-title--sm {
    font-size: 22px;
    margin-bottom: 8px;
}

.collection-card-desc {
    font-family: 'Manrope', sans-serif;
    font-size: 15px;
    font-weight: 400;
    line-height: 1.65;
    color: #404944;
    max-width: 480px;
}
</style>
