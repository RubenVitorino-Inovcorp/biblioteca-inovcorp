<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { CalendarClock, Info, Clock } from '@lucide/vue';

const props = defineProps({
    loan: {
        type: Object,
        required: true
    }
});

const showModal = ref(false);
const todayStr = computed(() => new Date().toISOString().split('T')[0]);

// Parse start_date from "d/m/Y H:i" format to "Y-m-d" for the HTML date input min attribute
const startDateISO = computed(() => {
    if (!props.loan.start_date) return '';
    const parts = props.loan.start_date.split(' ')[0].split('/');
    if (parts.length === 3) {
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
    }
    return '';
});

const form = useForm({
    return_date: todayStr.value,
});

// Preview of elapsed days based on selected return date
const elapsedDaysPreview = computed(() => {
    if (!form.return_date || !startDateISO.value) return null;
    const start = new Date(startDateISO.value);
    const end = new Date(form.return_date);
    if (isNaN(start) || isNaN(end)) return null;
    const diffTime = end - start;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays >= 0 ? diffDays : null;
});

const submitReturn = () => {
    form.post(route('requisicoes.devolver', props.loan.id), {
        onSuccess: () => {
            showModal.value = false;
            toast.success(`O livro da requisição ${props.loan.loan_number} foi devolvido.`);
        },
        onError: (errors) => {
            toast.error(errors.return_date || 'Erro ao processar a devolução.');
        }
    });
};
</script>

<template>
    <span @click.prevent="showModal = true" class="inline-block cursor-pointer">
        <slot></slot>
    </span>

    <ConfirmationModal :show="showModal" @close="showModal = false">
        <template #title>
            Confirmar Receção do Livro
        </template>

        <template #content>
            <div class="space-y-4 font-['Manrope']">
                <p class="text-gray-600 text-sm">
                    Insira a data real em que o livro foi entregue na biblioteca para calcular os dias decorridos.
                </p>

                <div class="bg-base-200/50 border border-base-300 rounded-xl p-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Número da Requisição:</span>
                        <span class="font-bold text-base-content">{{ loan.loan_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Data de Início:</span>
                        <span class="font-medium text-base-content">{{ loan.start_date }}</span>
                    </div>
                </div>

                <div class="form-control w-full">
                    <label class="label font-bold text-xs text-gray-700 uppercase tracking-wider">
                        Data de Receção Real
                    </label>
                    <div class="relative">
                        <input 
                            type="date" 
                            v-model="form.return_date" 
                            class="input input-bordered w-full pl-10"
                            :min="startDateISO"
                            :max="todayStr"
                        />
                        <CalendarClock :size="18" class="absolute left-3 top-3.5 text-gray-400" />
                    </div>
                    <p v-if="form.errors.return_date" class="text-error text-xs mt-1 font-semibold">
                        {{ form.errors.return_date }}
                    </p>
                </div>

                <!-- Elapsed days preview -->
                <div v-if="elapsedDaysPreview !== null" class="bg-primary/5 border border-primary/20 rounded-xl p-3 flex items-center justify-center gap-2">
                    <Clock :size="16" class="text-primary shrink-0" />
                    <span class="text-gray-600 text-sm">Dias decorridos:</span>
                    <span class="font-bold text-lg text-primary">{{ elapsedDaysPreview }}</span>
                    <span class="text-gray-500 text-sm">{{ elapsedDaysPreview === 1 ? 'dia' : 'dias' }}</span>
                </div>

                <p class="text-xs text-gray-500 flex gap-2 bg-warning/10 p-3 rounded-lg border border-warning/20">
                    <Info :size="16" class="text-warning shrink-0 mt-0.5" />
                    <span>Esta ação irá encerrar a requisição e repor automaticamente 1 unidade ao stock disponível do livro.</span>
                </p>
            </div>
        </template>

        <template #footer>
            <button 
                type="button" 
                class="px-4 py-2 text-sm font-bold text-base-content/60 hover:text-base-content transition-colors" 
                @click="showModal = false"
            >
                Cancelar
            </button>
            <button
                type="button"
                class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-green-900 transition-colors disabled:opacity-50"
                :disabled="form.processing"
                @click="submitReturn"
            >
                Confirmar Devolução
            </button>
        </template>
    </ConfirmationModal>
</template>