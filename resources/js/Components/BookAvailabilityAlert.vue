<script setup>
import { router } from '@inertiajs/vue3';
import { Bell, BellOff } from '@lucide/vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    book: [Object, Number, String],
    user: [Object, Number, String],
    has_alert: Boolean,
});

function createAlert() {
    router.post(route('catalog.livros.alerta.store', props.book), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Você será notificado por e-mail assim que o livro for devolvido.');
        }
    });
}

function deleteAlert() {
    router.delete(route('catalog.livros.alerta.destroy', props.book), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Deixou de receber notificações para este livro.');
        }
    });
}

</script>

<template>
    <div v-if="!has_alert">
        <button @click="createAlert" class="btn-add">
            <Bell :size="16" />
            Notificar quando disponível
        </button>
    </div>
    <div v-else-if="has_alert">
        <button @click="deleteAlert" class="btn-add">
            <BellOff :size="16" />
            Desativar notificações
        </button>
    </div>
</template>