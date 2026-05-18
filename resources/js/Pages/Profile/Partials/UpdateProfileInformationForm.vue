<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Informação do Perfil
        </template>

        <template #description>
            Atualize as informações do perfil da sua conta e o endereço de email.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <InputLabel for="photo" value="Fotografia de Perfil" />

                <div class="flex items-center gap-4 mt-2">
                    <!-- Current Profile Photo -->
                    <div v-show="! photoPreview" class="shrink-0">
                        <img v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="rounded-full size-20 object-cover shadow-sm border border-gray-100">
                        <div v-else class="rounded-full size-20 flex items-center justify-center bg-gray-200 text-[#3c4a42] shadow-sm border border-gray-100 text-2xl font-bold font-['Manrope']">
                            {{ user.name?.charAt(0)?.toUpperCase() }}
                        </div>
                    </div>

                    <!-- New Profile Photo Preview -->
                    <div v-show="photoPreview" class="shrink-0">
                        <span
                            class="block rounded-full size-20 bg-cover bg-no-repeat bg-center shadow-sm border border-gray-100"
                            :style="'background-image: url(\'' + photoPreview + '\');'"
                        />
                    </div>

                    <div class="flex-1 form-control">
                        <input
                            id="photo"
                            ref="photoInput"
                            type="file"
                            class="file-input file-input-bordered w-full"
                            accept="image/*"
                            @change="updatePhotoPreview"
                        >
                    </div>
                </div>

                <SecondaryButton
                    v-if="user.profile_photo_path"
                    type="button"
                    class="mt-3"
                    @click.prevent="deletePhoto"
                >
                    Remover Fotografia
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Nome" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2">
                        O seu endereço de email não está verificado.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            @click.prevent="sendEmailVerification"
                        >
                            Clique aqui para reenviar o email de verificação.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                        Um novo link de verificação foi enviado para o seu endereço de email.
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Guardado.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Guardar
            </PrimaryButton>
        </template>
    </FormSection>
</template>
