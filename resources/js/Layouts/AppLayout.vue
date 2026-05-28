<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import AppFooter from '@/Components/AppFooter.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Toaster, toast } from 'vue-sonner'
import 'vue-sonner/style.css'
import { Book, Building, LibraryBig, LayoutDashboard, User2Icon, UserPen } from "@lucide/vue";

const props = defineProps({
    title: String,
    hideSidebar: {
        type: Boolean,
        default: false,
    },
    noPadding: {
        type: Boolean,
        default: false,
    },
});

const showingNavigationDropdown = ref(false);

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
            <!-- Sidebar (hidden on pages with hideSidebar) -->
            <aside v-if="!hideSidebar" class="sidebar" :class="{ 'sidebar--collapsed': !sidebarOpen }">
                <div class="sidebar-inner">
                    <!-- Navigation Links -->
                    <nav class="sidebar-nav">
                        <!-- INÍCIO -->
                        <div class="sidebar-nav-section">
                            <span class="sidebar-nav-label" v-if="sidebarOpen">GERAL</span>
                            <ul class="sidebar-nav-list">
                                <li>
                                    <NavLink :href="route('home')" :active="route().current('home')">
                                        <LayoutDashboard :size="20"/>
                                        <span v-if="sidebarOpen">Início</span>
                                    </NavLink>
                                </li>
                            </ul>
                        </div>

                        <!-- CATÁLOGO -->
                        <div class="sidebar-nav-section">
                            <span class="sidebar-nav-label" v-if="sidebarOpen">CATÁLOGO</span>
                            <ul class="sidebar-nav-list">
                                <li>
                                    <NavLink :href="route('catalog.livros.index')" :active="route().current('catalog.livros.*')">
                                        <Book :size="20"/>
                                        <span v-if="sidebarOpen">Livros</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('catalog.autores.index')" :active="route().current('catalog.autores.*')">
                                        <UserPen :size="20" />
                                        <span v-if="sidebarOpen">Autores</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('catalog.editoras.index')" :active="route().current('catalog.editoras.*')">
                                        <Building :size="20"/>
                                        <span v-if="sidebarOpen">Editoras</span>
                                    </NavLink>
                                </li>
                            </ul>
                        </div>

                        <!-- PESSOAL -->
                        <div class="sidebar-nav-section">
                            <span class="sidebar-nav-label" v-if="sidebarOpen">PESSOAL</span>
                            <ul class="sidebar-nav-list">
                                <li>
                                    <NavLink :href="route('catalog.requisicoes.index')" :active="route().current('catalog.requisicoes.*')">
                                        <LibraryBig :size="20"/>
                                        <span v-if="sidebarOpen">As minhas requisições</span>
                                    </NavLink>
                                </li>
                                <li v-if="!$page.props.auth.user.is_admin">
                                    <NavLink :href="route('opinioes.index')" :active="route().current('opinioes.index')">
                                        <svg role="img" class="w-5 h-5 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span v-if="sidebarOpen">As minhas opiniões</span>
                                    </NavLink>
                                </li>
                            </ul>
                        </div>

                        <!-- GESTÃO (ADMIN) -->
                        <div class="sidebar-nav-section" v-if="$page.props.auth.user.is_admin">
                           <span class="sidebar-nav-label" v-if="sidebarOpen">GESTÃO</span>
                           <ul class="sidebar-nav-list">
                                <li>
                                    <NavLink :href="route('livros.index')" :active="route().current('livros.*') && !route().current('livros.google-index*')">
                                        <Book :size="20"/>
                                        <span v-if="sidebarOpen">Livros</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('autores.index')" :active="route().current('autores.*')">
                                        <UserPen :size="20" />
                                        <span v-if="sidebarOpen">Autores</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('editoras.index')" :active="route().current('editoras.*')">
                                        <Building :size="20"/>
                                        <span v-if="sidebarOpen">Editoras</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('requisicoes.index')" :active="route().current('requisicoes.*')">
                                        <LibraryBig :size="20"/>
                                        <span v-if="sidebarOpen">Requisições</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('admin.opinioes.index')" :active="route().current('admin.opinioes.*')">
                                        <svg role="img" class="w-5 h-5 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span v-if="sidebarOpen">Opiniões</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('utilizadores.index')" :active="route().current('utilizadores.*')">
                                        <User2Icon :size="20"/>
                                        <span v-if="sidebarOpen">Utilizadores</span>
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink :href="route('livros.google-index')" :active="route().current('livros.google-index')">
                                        <svg role="img" class="w-5 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <title>Google</title>
                                            <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                                        </svg>
                                        <span v-if="sidebarOpen">Livros (Google)</span>
                                    </NavLink>
                                </li>
                           </ul>
                        </div>
                    </nav>

                    <!-- User Section (Jetstream) -->
                    <div class="sidebar-footer" v-if="$page.props.auth?.user">
                        <div class="sidebar-user" v-if="sidebarOpen">
                            <Dropdown align="left" width="48" direction="up">
                                <template #trigger>
                                    <button class="sidebar-user-btn" type="button">
                                        <div class="avatar" :class="{ 'indicator': $page.props.auth.user.is_admin }">
                                            <span
                                                v-if="$page.props.auth.user.is_admin"
                                                class="indicator-item indicator-start badge badge-xs badge-primary py-2 px-1 text-[9px]"
                                            >
                                                Admin.
                                            </span>

                                            <div class="sidebar-user-avatar">
                                                <img
                                                    v-if="$page.props.jetstream?.managesProfilePhotos && $page.props.auth.user.profile_photo_url"
                                                    class="sidebar-user-photo"
                                                    :src="$page.props.auth.user.profile_photo_url"
                                                    :alt="$page.props.auth.user.name"
                                                >
                                                <span v-else class="sidebar-user-initials">
                                                    {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                                                </span>
                                            </div>
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
                                                v-if="$page.props.jetstream?.managesProfilePhotos && $page.props.auth.user.profile_photo_url"
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
            <div
                v-if="!hideSidebar"
                class="sidebar-overlay"
                :class="{ 'sidebar-overlay--visible': showingNavigationDropdown }"
                @click="showingNavigationDropdown = false"
            />

            <!-- Main Content -->
            <div
                class="main-content"
                :class="{
                    'main-content--expanded': !sidebarOpen && !hideSidebar,
                    'main-content--full': hideSidebar,
                }"
            >
                <!-- Page Header -->
                <header v-if="$slots.header" class="content-header">
                    <div class="content-header-inner flex items-center w-full">
                        <div class="flex items-center flex-1 w-full">
                            <button v-if="!hideSidebar" @click="toggleSidebar" class="sidebar-toggle mr-4" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="sidebar-toggle-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>
                            </button>
                            
                            <Link :href="route('home')" class="sidebar-brand flex items-center gap-2">
                                <img class="sidebar-logo w-8 h-8" src="/logo.webp" alt="logo">
                                <span class="sidebar-brand-text">Biblioteca</span>
                            </Link>

                            <div class="h-6 w-px bg-gray-300 mx-4"></div>

                            <div class="flex-1 min-w-0">
                                <slot name="header" />
                            </div>

                            <!-- User Dropdown (shown in header when sidebar is hidden) -->
                            <div v-if="hideSidebar && $page.props.auth?.user" class="header-user-area">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="header-user-btn" type="button" :aria-label="`Conta de ${$page.props.auth.user.name}`">
                                            <div class="header-user-avatar" :class="{ 'indicator': $page.props.auth.user.role?.id === $page.props.roles?.ADMIN }">
                                                <span
                                                    v-if="$page.props.auth.user.role?.id === $page.props.roles?.ADMIN"
                                                    class="indicator-item indicator-start badge badge-xs badge-primary py-2 px-1 text-[9px]"
                                                >
                                                    Admin.
                                                </span>
                                                <div class="avatar-circle">
                                                    <img
                                                        v-if="$page.props.jetstream?.managesProfilePhotos && $page.props.auth.user.profile_photo_url"
                                                        class="avatar-photo"
                                                        :src="$page.props.auth.user.profile_photo_url"
                                                        :alt="$page.props.auth.user.name"
                                                    >
                                                    <span v-else class="avatar-initials">
                                                        {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </button>
                                    </template>

                                    <template #content>
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
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Terminar Sessão
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Guest Auth Buttons (shown in header when sidebar is hidden and no user) -->
                            <div v-else-if="hideSidebar && !$page.props.auth?.user" class="header-auth-area">
                                <Link :href="route('login')" class="header-auth-link">Iniciar Sessão</Link>
                                <Link :href="route('register')" class="header-auth-btn">Registar</Link>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="content-body" :class="{ 'content-body--no-padding': noPadding }">
                    <slot />
                </main>

                <!-- Footer -->
                <AppFooter />
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
    background: var(--color-base-200);
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
    background: var(--color-base-200);
    border-right: 1px solid var(--color-silk-300);
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
    border-bottom: 1px solid var(--color-silk-300);
    min-height: 68px;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: var(--color-silk-content);
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
    color: var(--color-silk-content);
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
    color: var(--color-silk-muted);
    transition: all 0.15s ease;
    flex-shrink: 0;
}

.sidebar-toggle:hover {
    background: var(--color-silk-200);
    color: var(--color-silk-content);
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
    color: var(--color-primary);
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
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.sidebar--collapsed :deep(.nav-link) {
    justify-content: center;
    padding-left: 0;
    padding-right: 0;   
}

.sidebar--collapsed :deep(.nav-link) svg {
    flex-shrink: 0;
}

/* ───────────────────────────────────────────
   Sidebar Footer / User
   ─────────────────────────────────────────── */
.sidebar-footer {
    padding: 12px;
    border-top: 1px solid var(--color-silk-300);
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
    background: var(--color-silk-200);
    border-color: var(--color-silk-300);
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
    background: linear-gradient(135deg, var(--color-primary), #00a86b);
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
    color: var(--color-silk-content);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sidebar-user-email {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    font-weight: 400;
    color: var(--color-silk-muted);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sidebar-user-chevron {
    width: 16px;
    height: 16px;
    color: var(--color-silk-muted);
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
    color: var(--color-silk-muted);
}

.sidebar-dropdown-divider {
    height: 1px;
    background: var(--color-silk-300);
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

.main-content--full {
    margin-left: 0;
}

.content-header {
    position: sticky;
    top: 16px;
    z-index: 30;
    margin: 16px auto 0;
    max-width: 1400px;
    width: 90%;
    background: var(--color-base-100);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--color-silk-300);
    border-radius: 12px;
    box-shadow: 0px 4px 20px rgba(0, 108, 73, 0.05);
}

.content-header-inner {
    padding: 16px 24px;
}

.content-body {
    flex: 1;
    padding: 24px;
    display: flex;
    flex-direction: column;
}

.content-body--no-padding {
    padding: 0;
}

/* ───────────────────────────────────────────
   Header User (for hideSidebar mode)
   ─────────────────────────────────────────── */
.header-user-area {
    flex-shrink: 0;
    margin-left: 16px;
}

.header-user-btn {
    display: flex;
    align-items: center;
    background: none;
    border: 1px solid transparent;
    border-radius: 50%;
    cursor: pointer;
    padding: 2px;
    transition: all 0.15s ease;
}

.header-user-btn:hover {
    border-color: var(--color-silk-300);
}

.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary), #00a86b);
    color: #fff;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 700;
}

.avatar-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-initials {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 700;
}

/* ───────────────────────────────────────────
   Guest Auth Buttons (header)
   ─────────────────────────────────────────── */
.header-auth-area {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    margin-left: 16px;
}

.header-auth-link {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-silk-content);
    text-decoration: none;
    transition: opacity 0.15s ease;
    white-space: nowrap;
}

.header-auth-link:hover {
    opacity: 0.7;
}

.header-auth-btn {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    background: var(--color-primary);
    padding: 8px 20px;
    border-radius: 6px;
    text-decoration: none;
    transition: background 0.15s ease;
    white-space: nowrap;
}

.header-auth-btn:hover {
    background: var(--color-primary-dark);
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
