<script setup lang="ts">
    import TableWrapper from '@/Components/TableWrapper.vue';
    import Layout from '@/Layouts/AppLayout.vue'
    import { Link } from '@inertiajs/vue3'
    import ReturnBookButton from "@/Components/ReturnBookButton.vue";
    import Reviews from '@/Components/Common/Reviews.vue';
    import { Loan, Book, Review } from '@/types';

    const props = defineProps<{
        loan: Loan;
        book: Book;
        userReview?: Review | null;
        reviewableLoanId?: number | null;
    }>();

    const formattedDate = (date?: string | null): string => {
        if (!date) return '—';
        return date;
    };

    const isReturnable = (status: string): boolean => {
        return status === 'active' || status === 'overdue';
    };
</script>

<template>
    <Layout :title="`Requisição ${loan.loan_number}`">

        <div class="show-card p-6 md:p-8 max-w-5xl w-full mx-auto my-auto space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <div class="md:col-span-6 md:col-start-4">
                    <h2 class="text-2xl font-bold text-base-content">Detalhes da Requisição - {{ loan.loan_number }}</h2>
                </div>  
            </div> 

            <div class="divider"></div> 

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

                <div class="md:col-span-4">
                    <img :src="loan.book?.image_url" :alt="loan.book?.title" class="show-image" />
                </div>
                <div class="md:col-span-8 space-y-6">
                    <div class="flex items-start justify-between">
                        <Link :href="route('catalog.livros.show', loan.book.id)" class="show-title-link">
                            <h1 class="show-title">{{ loan.book.title }}</h1>
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="show-label">Autores</div>
                            <div class="flex flex-wrap gap-2">
                                <div class="badge-author" v-for="author in loan.book.authors" :key="author.id">
                                    <Link :href="route('catalog.autores.show', author.id)">
                                        {{ author.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="show-label">Editora</div>
                            <div class="flex flex-wrap gap-2">
                                <div class="badge-publisher" v-if="loan.book.publisher">
                                    <Link :href="route('catalog.editoras.show', loan.book.publisher?.id)">
                                        {{ loan.book.publisher?.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="show-label">Bibliografia</div>
                        <p class="show-text">{{ loan.book.bibliography }}</p>
                    </div>

                    <div class="show-divider"></div>

                    <div class="flex justify-between items-center">
                        <div class="show-isbn">
                            ISBN: {{ loan.book.isbn }}
                        </div>
                        <div v-if="isReturnable(loan.status)">
                            <ReturnBookButton
                                :loan="loan"
                                route-name="catalog.requisicoes.devolver"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>    

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <div v-if="loan.user" class="md:col-span-6 md:col-start-4">
                    <div class="flex items-center space-x-4">
                        <div class="avatar">
                            <div class="w-32 h-32 rounded-full border border-gray-100 overflow-hidden shadow-sm">
                                <img v-if="loan.user_photo_snapshot" :src="loan.user_photo_snapshot" :alt="loan.user.name" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200 text-base-content/70 text-3xl font-bold">
                                    {{ loan.user.name?.charAt(0)?.toUpperCase() }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800">Requisição feita por</h2>
                            <p class="text-xl text-gray-600 mt-2">{{ loan.user?.name }}</p>
                            <p class="text-gray-500 mt-1">{{ loan.user?.email }}</p>
                        </div>
                    </div>
                    </div>
            </div>

            <TableWrapper>
                <template #header>
                    <th>Número da Requisição</th>
                    <th>Data de Início</th>
                    <th>Data Prevista de Devolução</th>
                    <th>Data de Devolução</th>
                    <th>Estado</th>
                </template>
                <template #body>
                    <td>{{ loan.loan_number }}</td>
                    <td>{{ loan.start_date }}</td>
                    <td>{{ loan.estimated_return_date }}</td>
                    <td>{{ loan.end_date ?? 'Em Andamento' }}</td>
                    <td>{{ loan.status_label }}</td>
                </template>
            </TableWrapper>
        </div>
        <Reviews :reviews="props.book.reviews" :book="props.book" :userReview="props.userReview" :reviewableLoanId="props.reviewableLoanId" />
    </Layout>
</template>

