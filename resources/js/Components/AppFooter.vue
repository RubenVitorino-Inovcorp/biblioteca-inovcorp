<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="app-footer">
        <div class="footer-container">
            <!-- Brand Column -->
            <div class="footer-brand">
                <Link :href="route('home')" class="footer-logo-link">
                    <img class="footer-logo" src="/logo-white.webp" alt="Biblioteca Inovcorp" />
                    <span class="footer-brand-name">Biblioteca</span>
                </Link>
                <p class="footer-description">
                    O seu portal de acesso ao conhecimento. Explore, descubra e requisite livros de forma simples e rápida.
                </p>
            </div>

            <!-- Links Columns -->
            <div class="footer-links">
                <div class="footer-links-column">
                    <h4 class="footer-links-title">Explorar</h4>
                    <Link :href="route('catalog.livros.index')" class="footer-link">Livros</Link>
                    <Link :href="route('catalog.autores.index')" class="footer-link">Autores</Link>
                    <Link :href="route('catalog.editoras.index')" class="footer-link">Editoras</Link>
                </div>

                <div v-if="$page.props.auth?.user" class="footer-links-column">
                    <h4 class="footer-links-title">Conta</h4>
                    <Link :href="route('profile.show')" class="footer-link">Perfil</Link>
                    <Link :href="route('catalog.requisicoes.index')" class="footer-link">Requisições</Link>
                </div>

                <div v-if="$page.props.jetstream?.hasTermsAndPrivacyPolicyFeature" class="footer-links-column">
                    <h4 class="footer-links-title">Legal</h4>
                    <Link :href="route('terms.show')" class="footer-link">Termos de Serviço</Link>
                    <Link :href="route('policy.show')" class="footer-link">Política de Privacidade</Link>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <p class="footer-copyright">© {{ new Date().getFullYear() }} Biblioteca Inovcorp. Todos os direitos reservados.</p>
        </div>
    </footer>
</template>

<style scoped>
/* ───────────────────────────────────────────
   Footer
   ─────────────────────────────────────────── */
.app-footer {
    background-image: url('/footer-bg.webp');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-color: var(--color-primary);
    color: rgba(255, 255, 255, 0.85);
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 64px 64px 48px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 48px;
}

@media (min-width: 768px) {
    .footer-container {
        grid-template-columns: 1fr 3fr;
        gap: 32px;
    }
}

/* Brand */
.footer-brand {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.footer-logo-link {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #ffffff;
    transition: opacity 0.2s ease;
}

.footer-logo-link:hover {
    opacity: 0.85;
}

.footer-logo {
    width: 28px;
    height: 28px;
}

.footer-brand-name {
    font-family: 'Manrope', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.01em;
}

.footer-description {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.7);
    max-width: 280px;
}

/* Links */
.footer-links {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

@media (min-width: 768px) {
    .footer-links {
        flex-direction: row;
        justify-content: flex-end;
        gap: 80px;
    }
}

.footer-links-column {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.footer-links-title {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 4px;
}

.footer-link {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 400;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: underline;
    text-decoration-color: rgba(255, 255, 255, 0.3);
    text-underline-offset: 4px;
    text-decoration-thickness: 1px;
    transition: color 0.2s ease, text-decoration-color 0.2s ease;
}

.footer-link:hover {
    color: #ffffff;
    text-decoration-color: #ffffff;
}

/* Copyright */
.footer-bottom {
    max-width: 1280px;
    margin: 0 auto;
    padding: 24px 64px;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
}

.footer-copyright {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.5);
}

/* ───────────────────────────────────────────
   Responsive
   ─────────────────────────────────────────── */
@media (max-width: 767px) {
    .footer-container {
        padding: 40px 20px 32px;
    }

    .footer-bottom {
        padding: 20px;
    }
}
</style>
