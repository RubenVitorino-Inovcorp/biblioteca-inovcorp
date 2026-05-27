<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Bem-vindo" />
    <div class="min-h-screen bg-base-200 flex flex-col justify-center items-center">
        <div class="max-w-2xl w-full px-6 text-center">
            <div class="mb-8 flex justify-center">
                <img src="/logo.webp" alt="Biblioteca Logo" class="h-32 w-auto drop-shadow-sm" />
            </div>
            
            <h1 class="text-4xl font-bold text-base-content font-['Manrope'] mb-4">
                Biblioteca Inovcorp
            </h1>
            
            <p class="text-base-content/60 font-['Manrope'] text-lg mb-10 leading-relaxed">
                O seu portal de acesso ao conhecimento. Explore o nosso catálogo de livros, descubra novos autores e faça a gestão das suas requisições de forma simples e rápida.
            </p>

            <div v-if="canLogin" class="flex flex-col sm:flex-row justify-center gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('home')"
                    class="btn-primary"
                >
                    Aceder à Biblioteca
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="btn-primary"
                    >
                        Iniciar Sessão
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="btn-secondary"
                    >
                        Registar Conta
                    </Link>
                </template>
            </div>
        </div>
        
        <footer class="absolute bottom-6 text-center text-sm text-base-content/60 font-['Manrope']">
            Biblioteca Inovcorp &copy; {{ new Date().getFullYear() }}
        </footer>
    </div>
</template>

<style scoped>
.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 28px;
    background-color: var(--color-primary);
    color: white;
    font-family: 'Manrope', sans-serif;
    font-weight: 600;
    font-size: 16px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    box-shadow: 0px 4px 10px rgba(0, 108, 73, 0.2);
}

.btn-primary:hover {
    background-color: var(--color-primary-dark);
    transform: translateY(-1px);
    box-shadow: 0px 6px 15px rgba(0, 108, 73, 0.3);
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 28px;
    background-color: transparent;
    color: var(--color-primary);
    font-family: 'Manrope', sans-serif;
    font-weight: 600;
    font-size: 16px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 2px solid var(--color-primary);
}

.btn-secondary:hover {
    background-color: rgba(0, 108, 73, 0.05);
    transform: translateY(-1px);
}
</style>
