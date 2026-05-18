<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Book, UserPen, Building, LibraryBig, LayoutDashboard, User2Icon } from '@lucide/vue';

const user = usePage().props.auth.user;
const isAdmin = user.role?.id === usePage().props.roles.ADMIN;
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="page-title">
                Dashboard
            </h2>
        </template>

        <div class="dashboard-content">
            <div class="welcome-card">
                <div class="welcome-header">
                    <h3 class="welcome-title">Bem-vindo(a), {{ user.name }}!</h3>
                    <p class="welcome-subtitle">O que gostaria de fazer hoje?</p>
                </div>
            </div>
            
            <div class="quick-links">
                <h3 class="section-title">Acesso Rápido</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Link :href="route('catalog.livros.index')" class="quick-link-card">
                        <div class="quick-link-icon">
                            <Book :size="24" />
                        </div>
                        <div class="quick-link-text">
                            <h4 class="quick-link-title">Catálogo de Livros</h4>
                            <p class="quick-link-desc">Explore todos os livros disponíveis</p>
                        </div>
                    </Link>

                    <Link :href="route('catalog.requisicoes.index')" class="quick-link-card">
                        <div class="quick-link-icon">
                            <LibraryBig :size="24" />
                        </div>
                        <div class="quick-link-text">
                            <h4 class="quick-link-title">As minhas requisições</h4>
                            <p class="quick-link-desc">Acompanhe os seus empréstimos</p>
                        </div>
                    </Link>

                    <Link v-if="isAdmin" :href="route('requisicoes.index')" class="quick-link-card admin-card">
                        <div class="quick-link-icon">
                            <LayoutDashboard :size="24" />
                        </div>
                        <div class="quick-link-text">
                            <h4 class="quick-link-title">Gestão de Requisições</h4>
                            <p class="quick-link-desc">Administrar os pedidos pendentes</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.page-title {
    font-family: 'Manrope', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: #191c1e;
    line-height: 1.4;
}

.dashboard-content {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.welcome-card {
    background: white;
    border-radius: 12px;
    padding: 32px;
    border: 1px solid #E2E8F0;
    box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.05);
}

.welcome-title {
    font-family: 'Manrope', sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: #191c1e;
    margin-bottom: 8px;
}

.welcome-subtitle {
    font-family: 'Manrope', sans-serif;
    font-size: 16px;
    color: #6c7a71;
}

.section-title {
    font-family: 'Manrope', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: #191c1e;
    margin-bottom: 16px;
}

.quick-links {
    margin-top: 8px;
}

.quick-link-card {
    display: flex;
    align-items: center;
    gap: 16px;
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.03);
    transition: all 0.2s ease;
    text-decoration: none;
}

.quick-link-card:hover {
    border-color: #10b981;
    transform: translateY(-2px);
    box-shadow: 0px 8px 24px rgba(16, 185, 129, 0.1);
}

.quick-link-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: rgba(0, 108, 73, 0.1);
    color: #006c49;
    flex-shrink: 0;
}

.admin-card .quick-link-icon {
    background: rgba(217, 119, 6, 0.1);
    color: #d97706;
}

.admin-card:hover {
    border-color: #d97706;
    box-shadow: 0px 8px 24px rgba(217, 119, 6, 0.1);
}

.quick-link-title {
    font-family: 'Manrope', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: #191c1e;
    margin-bottom: 4px;
}

.quick-link-desc {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    color: #6c7a71;
}
</style>
