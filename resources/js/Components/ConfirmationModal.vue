<script setup>
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 mx-12">
            <div class="sm:flex sm:items-start">
                <div v-if="$slots.icon" class="mx-auto shrink-0 flex items-center justify-center sm:mx-0">
                    <slot name="icon" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:text-start w-full" :class="{ 'sm:ms-4': $slots.icon }">
                    <h3 class="text-xl font-bold text-[#191c1e] font-['Manrope']">
                        <slot name="title" />
                    </h3>

                    <div class="mt-4 text-sm text-[#3c4a42]">
                        <slot name="content" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-end">
            <slot name="footer" />
        </div>
    </Modal>
</template>
