<script setup lang="ts">
    import Layout from '@/Layouts/AppLayout.vue'
    import { Link, useForm, usePage } from '@inertiajs/vue3'
    import { computed, ref } from 'vue';
    import { Star } from '@lucide/vue';
    import ReviewPendingButton from '@/Components/ReviewPendingButton.vue';
    import { useReviewRating } from '@/Composables/useReviewRating';
    import { Review, User } from '@/types';

    const { getRatingLabel } = useReviewRating();

    const props = defineProps<{
        review: Review;
    }>();

    const page = usePage<{
        auth: {
            user?: User | null;
        };
    }>();
    
    const isAdmin = computed<boolean>(() => {
        return !!page.props.auth.user?.is_admin;
    });
</script>

<template>
    <Layout :title="`Opinião - ${review.book.title}`">

        <div class="show-card p-6 md:p-8 max-w-5xl w-full mx-auto my-auto space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <div class="md:col-span-12">
                    <h2 class="text-2xl font-bold text-base-content">Detalhes da Opinião</h2>
                </div>  
            </div> 

            <div class="divider"></div> 

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                <div class="md:col-span-4">
                    <img :src="review.book?.image_url" :alt="review.book?.title" class="w-full h-auto object-cover shadow-lg rounded-xl" />
                </div>
                <div class="md:col-span-8 space-y-6">
                    <div class="flex items-start justify-between">
                        <Link :href="route(isAdmin ? 'livros.show' : 'catalog.livros.show', review.book.id)" class="hover:text-primary">
                            <h1 class="text-3xl font-extrabold leading-tight">{{ review.book.title }}</h1>
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-2">Autores</div>
                            <div class="flex flex-wrap gap-2">
                                <div class="badge badge-outline" v-for="author in review.book.authors" :key="author.id">
                                    <Link :href="route(isAdmin ? 'autores.show' : 'catalog.autores.show', author.id)">
                                        {{ author.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500 font-medium">
                            ISBN: {{ review.book.isbn }}
                        </div>
                        <div v-if="review.status === 'pending'" class="flex items-center gap-2">
                            <ReviewPendingButton action="approve" :review="review" />
                            <ReviewPendingButton action="reject" :review="review" />
                        </div>
                        <div v-else>
                            <span v-if="review.status === 'approved'" class="badge badge-success text-white p-3 font-bold">Aprovada</span>
                            <span v-else-if="review.status === 'rejected'" class="badge badge-error text-white p-3 font-bold">Rejeitada</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>    

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                <div v-if="review.user" class="md:col-span-4">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="avatar">
                            <div class="w-32 h-32 rounded-full border border-gray-100 overflow-hidden shadow-sm">
                                <img v-if="review.user.profile_photo_url" :src="review.user.profile_photo_url" :alt="review.user.name" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200 text-[#3c4a42] text-3xl font-bold">
                                    {{ review.user.name?.charAt(0)?.toUpperCase() }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Avaliado por</h2>
                            <Link class="text-lg text-primary mt-1 hover:underline" :href="route('utilizadores.show', review.user.id)">{{ review.user?.name }}</Link>
                            <p class="text-gray-500 text-sm">{{ review.user?.email }}</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-8">
                    <div class="bg-base-200 p-6 rounded-2xl shadow-sm border border-base-300">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-base-content">{{ review.review_title }}</h3>
                            <div class="flex items-center gap-1 bg-white px-3 py-1 rounded-full shadow-sm">
                                <Star class="w-4 h-4 text-amber-400 fill-amber-400" />
                                <span class="font-bold text-sm">{{ review.rating }}/10</span>
                                <span class="text-xs text-gray-500 ml-1">({{ getRatingLabel(review.rating) }})</span>
                            </div>
                        </div>
                        <p class="text-base-content/80 whitespace-pre-wrap leading-relaxed">{{ review.review_text }}</p>
                        
                        <div v-if="review.status === 'rejected' && review.rejection_reason" class="mt-6 p-4 bg-red-50 text-red-700 border border-red-200 rounded-xl">
                            <h4 class="font-bold text-sm uppercase tracking-wide mb-1">Motivo da Rejeição:</h4>
                            <p class="text-sm">{{ review.rejection_reason }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.show-image {
    width: 100%;
    height: auto;
    object-fit: cover;
}
.show-title {
    font-size: 1.875rem;
    font-weight: 800;
    line-height: 1.2;
}
.show-label {
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--color-silk-muted);
    margin-bottom: 0.5rem;
}
</style>
