<script setup>
import { useForm } from '@inertiajs/vue3';
import { toast } from "vue-sonner";
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    user: Object,
});

const emit = defineEmits(['success']);

const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: '',
    role: props.user.role || '',
});

const submit = () => {
    form.put(route('utilizadores.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success')
            toast.success(`O utilizador "${form.name}" foi atualizado com sucesso!`);
        },
        onError: () => {
            toast.error("Erro ao tentar atualizar utilizador.");
        }
    });
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                Atualizar Utilizador
            </h1>
        </template>
        <form @submit.prevent="submit" class="max-w-2xl mx-auto p-8 bg-base-100 rounded-2xl shadow-sm border border-gray-100 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label font-semibold text-base-content/70">Nome</label>
                    <input v-model="form.name" type="text" class="input input-bordered w-full" placeholder="Ex: João Silva" />
                    <span v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</span>
                </div>

                <div class="form-control">
                    <label class="label font-semibold text-base-content/70">Email</label>
                    <input v-model="form.email" type="text" class="input input-bordered w-full" placeholder="Ex: exemplo@email.com" />
                    <span v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</span>
                </div>
            </div>

            <div class="form-control mt-2">
                <label class="label font-semibold text-base-content/70">Palavra-Passe</label>
                <input v-model="form.password" type="password" class="input input-bordered w-full" placeholder="Palavra-Passe" />
                <span v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</span>
            </div>

            <div class="form-control mt-2">
                <label class="label font-semibold text-base-content/70">Confirmar Palavra-Passe</label>
                <input v-model="form.password_confirmation" type="password" class="input input-bordered w-full" placeholder="Confirmar Palavra-Passe" />
                <span v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ form.errors.password_confirmation }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div class="form-control">
                    <label class="label font-semibold text-base-content/70">Tipo de Utilizador</label>
                    <select v-model="form.role" class="select select-bordered w-full">
                        <option disabled value="">Selecione um tipo de utilizador...</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <span v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</span>
                </div>
            </div>

            <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-add" :disabled="form.processing">
                    <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                    Atualizar Utilizador
                </button>
            </div>
        </form>
    </AppLayout>
</template>
