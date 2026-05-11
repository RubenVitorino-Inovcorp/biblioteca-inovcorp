<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Toaster, toast } from 'vue-sonner'
import 'vue-sonner/style.css'
import { UsersIcon, Book, Building, LayoutDashboard } from "@lucide/vue";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

// Persist sidebar state in localStorage
const savedSidebarState = typeof window !== 'undefined'
    ? localStorage.getItem('sidebar-open')
    : null;
const sidebarOpen = ref(savedSidebarState !== null ? savedSidebarState === 'true' : true);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
    localStorage.setItem('sidebar-open', sidebarOpen.value);
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />
        <Toaster richColors position="bottom-right" theme="light" :closeButton="true" closeButtonPosition="top-right" />

        <div class="app-shell">
            <!-- Sidebar -->
            <aside class="sidebar" :class="{ 'sidebar--collapsed': !sidebarOpen }">
                <div class="sidebar-inner">
                    <div class="sidebar-header">
                        <Link :href="route('dashboard')" class="sidebar-brand" v-if="sidebarOpen">
                            <img class="sidebar-logo" src="/logo.webp" alt="logo">
                            <span class="sidebar-brand-text">Biblioteca</span>
                        </Link>
                        <button @click="toggleSidebar" class="sidebar-toggle" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="sidebar-toggle-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="sidebar-nav">
                        <div class="sidebar-nav-section">
                            <span class="sidebar-nav-label" v-if="sidebarOpen">CATÁLOGO</span>
                            <ul class="sidebar-nav-list">
                                <li>
                                    <NavLink :href="route('livros.index')" :active="route().current('livros.*')">
                                        <Book :size="20"/>
                                        <span v-if="sidebarOpen">Livros</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('autores.index')" :active="route().current('autores.*')">
                                        <UsersIcon :size="20" />
                                        <span v-if="sidebarOpen">Autores</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('editoras.index')" :active="route().current('editoras.*')">
                                        <Building :size="20"/>
                                        <span v-if="sidebarOpen">Editoras</span>
                                    </NavLink>
                                </li>
                            </ul>
                        </div>

<!--                        <div class="sidebar-nav-section" v-if="sidebarOpen">-->
<!--                            <span class="sidebar-nav-label">SISTEMA</span>-->
<!--                            <ul class="sidebar-nav-list">-->
<!--                                <li>-->
<!--                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">-->
<!--                                        <LayoutDashboard :size="20"/>-->
<!--                                        <span>Dashboard</span>-->
<!--                                    </NavLink>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </div>-->
                    </nav>

                    <!-- User Section (Jetstream) -->
                    <div class="sidebar-footer" v-if="$page.props.auth?.user">
                        <div class="sidebar-user" v-if="sidebarOpen">
                            <Dropdown align="left" width="48" direction="up">
                                <template #trigger>
                                    <button class="sidebar-user-btn" type="button">
                                        <div class="sidebar-user-avatar">
                                            <img
                                                v-if="$page.props.jetstream?.managesProfilePhotos"
                                                class="sidebar-user-photo"
                                                :src="$page.props.auth.user.profile_photo_url"
                                                :alt="$page.props.auth.user.name"
                                            >
                                            <span v-else class="sidebar-user-initials">
                                                {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="sidebar-user-info">
                                            <span class="sidebar-user-name">{{ $page.props.auth.user.name }}</span>
                                            <span class="sidebar-user-email">{{ $page.props.auth.user.email }}</span>
                                        </div>
                                        <svg class="sidebar-user-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div class="sidebar-dropdown-header">
                                        Gerir Conta
                                    </div>

                                    <DropdownLink :href="route('profile.show')">
                                        Perfil
                                    </DropdownLink>

                                    <DropdownLink v-if="$page.props.jetstream?.hasApiFeatures" :href="route('api-tokens.index')">
                                        API Tokens
                                    </DropdownLink>

                                    <div class="sidebar-dropdown-divider" />

                                    <!-- Authentication -->
                                    <form @submit.prevent="logout">
                                        <DropdownLink as="button">
                                            Terminar Sessão
                                        </DropdownLink>
                                    </form>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Collapsed state: just show avatar -->
                        <div v-else class="sidebar-user-collapsed">
                            <Dropdown align="left" width="48" direction="up">
                                <template #trigger>
                                    <button class="sidebar-avatar-btn" type="button">
                                        <div class="sidebar-user-avatar">
                                            <img
                                                v-if="$page.props.jetstream?.managesProfilePhotos"
                                                class="sidebar-user-photo"
                                                :src="$page.props.auth.user.profile_photo_url"
                                                :alt="$page.props.auth.user.name"
                                            >
                                            <span v-else class="sidebar-user-initials">
                                                {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                                            </span>
                                        </div>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="sidebar-dropdown-header">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <DropdownLink :href="route('profile.show')">
                                        Perfil
                                    </DropdownLink>
                                    <div class="sidebar-dropdown-divider" />
                                    <form @submit.prevent="logout">
                                        <DropdownLink as="button">
                                            Terminar Sessão
                                        </DropdownLink>
                                    </form>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Mobile sidebar overlay -->
            <div class="sidebar-overlay" :class="{ 'sidebar-overlay--visible': showingNavigationDropdown }" @click="showingNavigationDropdown = false" />

            <!-- Main Content -->
            <div class="main-content" :class="{ 'main-content--expanded': !sidebarOpen }">
                <!-- Page Header -->
                <header v-if="$slots.header" class="content-header">
                    <div class="content-header-inner">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="content-body">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ───────────────────────────────────────────
   App Shell
   ─────────────────────────────────────────── */
.app-shell {
    display: flex;
    min-height: 100vh;
    background: #f7f9fb;
}

/* ───────────────────────────────────────────
   Sidebar
   ─────────────────────────────────────────── */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 260px;
    z-index: 40;
    transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar--collapsed {
    width: 68px;
}

.sidebar-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #ffffff;
    border-right: 1px solid #E2E8F0;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar--collapsed .sidebar-inner {
    overflow: visible;
}

/* Sidebar Header */
.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 16px;
    border-bottom: 1px solid #E2E8F0;
    min-height: 68px;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #191c1e;
    transition: opacity 0.15s ease;
}

.sidebar-brand:hover {
    opacity: 0.8;
}

.sidebar-logo {
    width: 32px;
    height: 32px;
}

.sidebar-brand-text {
    font-family: 'Manrope', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: #191c1e;
    letter-spacing: -0.01em;
}

.sidebar-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    background: none;
    border: 1px solid transparent;
    border-radius: 6px;
    cursor: pointer;
    color: #6c7a71;
    transition: all 0.15s ease;
    flex-shrink: 0;
}

.sidebar-toggle:hover {
    background: #f2f4f6;
    color: #191c1e;
}

.sidebar-toggle-icon {
    width: 20px;
    height: 20px;
}

/* Sidebar Navigation */
.sidebar-nav {
    flex: 1;
    padding: 16px 12px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.sidebar-nav-section {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sidebar-nav-label {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #6c7a71;
    padding: 0 14px 6px;
    text-transform: uppercase;
}

.sidebar-nav-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.sidebar-icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

/* ───────────────────────────────────────────
   Sidebar Footer / User
   ─────────────────────────────────────────── */
.sidebar-footer {
    padding: 12px;
    border-top: 1px solid #E2E8F0;
}

.sidebar-user-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 8px 10px;
    background: none;
    border: 1px solid transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
    text-align: left;
}

.sidebar-user-btn:hover {
    background: #f2f4f6;
    border-color: #E2E8F0;
}

.sidebar-user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #006c49, #10b981);
    color: #fff;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 700;
}

.sidebar-user-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sidebar-user-initials {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 700;
}

.sidebar-user-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.sidebar-user-name {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #191c1e;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sidebar-user-email {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    font-weight: 400;
    color: #6c7a71;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sidebar-user-chevron {
    width: 16px;
    height: 16px;
    color: #6c7a71;
    flex-shrink: 0;
}

.sidebar-user-collapsed {
    display: flex;
    justify-content: center;
}

.sidebar-avatar-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    background: none;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: opacity 0.15s ease;
}

.sidebar-avatar-btn:hover {
    opacity: 0.8;
}

.sidebar-dropdown-header {
    display: block;
    padding: 8px 16px 4px;
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #6c7a71;
}

.sidebar-dropdown-divider {
    height: 1px;
    background: #E2E8F0;
    margin: 4px 0;
}

/* ───────────────────────────────────────────
   Mobile overlay
   ─────────────────────────────────────────── */
.sidebar-overlay {
    display: none;
}

/* ───────────────────────────────────────────
   Main Content
   ─────────────────────────────────────────── */
.main-content {
    flex: 1;
    margin-left: 260px;
    transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.main-content--expanded {
    margin-left: 68px;
}

.content-header {
    position: sticky;
    top: 16px;
    z-index: 30;
    margin: 16px 24px 0;
    background: rgba(255, 255, 255, 0.80);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.05);
}

.content-header-inner {
    padding: 16px 24px;
}

.content-body {
    flex: 1;
    padding: 24px;
}

/* ───────────────────────────────────────────
   Responsive
   ─────────────────────────────────────────── */
@media (max-width: 1023px) {
    .sidebar {
        transform: translateX(-100%);
        width: 260px !important;
    }

    .sidebar:not(.sidebar--collapsed) {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0 !important;
    }

    .sidebar-overlay--visible {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 35;
    }
}
</style>
