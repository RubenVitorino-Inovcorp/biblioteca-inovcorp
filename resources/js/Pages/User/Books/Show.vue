<script setup>
    import Layout from '@/Layouts/AppLayout.vue'
    import LoanCreateForm from '@/Components/LoanCreateForm.vue';
    import TableWrapper from '@/Components/TableWrapper.vue';
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3'
    import {Head, Link} from '@inertiajs/vue3'
    import { LibraryBig } from '@lucide/vue';


    defineProps({
        book: Object,
        loans: Array
    })

    const isAdmin = computed(() => {
        return usePage().props.auth.user?.role?.id === usePage().props.roles.ADMIN;
    });

</script>

<template>
    <Layout>
        <Head :title="book.title" />

        <div class="show-card p-6 md:p-8 max-w-5xl w-full mx-auto my-auto space-y-6 mt-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

                <div class="md:col-span-4">
                    <img :src="book.image_path" :alt="book.title" class="show-image shadow-lg" />
                </div>

                <div class="md:col-span-8 space-y-6">
                    <div class="flex items-start justify-between">
                        <h1 class="show-title">{{ book.title }}</h1>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="show-label">Autores</div>
                            <div class="flex flex-wrap gap-2">
                                <div class="badge-author" v-for="author in book.authors" :key="author.id">
                                    <Link :href="route('catalog.autores.show', author.id)">
                                        {{ author.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="show-label">Editora</div>
                            <div class="flex flex-wrap gap-2">
                                <div class="badge-publisher" v-if="book.publisher">
                                    <Link :href="route('catalog.editoras.show', book.publisher?.id)">
                                        {{ book.publisher?.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="show-label">Bibliografia</div>
                        <p class="show-text">{{ book.bibliography }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="show-label">Disponível para requisição</div>
                            <p class="show-text" :class="book.is_available ? 'text-green-500' : 'text-red-500'">{{ book.is_available ? 'Sim' : 'Não' }}</p>
                        </div>
                        <div v-if="book.is_available" class="flex justify-end mt-2">
                            <LoanCreateForm :book="book">
                                <button type="button" class="flex items-center gap-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-green-900 transition-colors font-['Manrope'] px-4 py-2">
                                    <LibraryBig :size="16" />
                                    Requisitar
                                </button>
                            </LoanCreateForm>
                        </div>
                    </div>

                    <div class="show-divider"></div>

                    <div class="flex justify-between items-center">
                        <div class="show-isbn">
                            ISBN: {{ book.isbn }}
                        </div>
                        <div class="show-price">
                            {{ book.price }}€
                        </div>
                    </div>
                </div>
            </div>
            
            <TableWrapper v-if="loans && loans.length > 0 && isAdmin">
                <template #header>
                    <th>Número da Requisição</th>
                    <th>Requisitado por</th>
                    <th>Data de Início</th>
                    <th>Data Prevista de Devolução</th>
                    <th>Data de Devolução</th>
                    <th>Estado</th>
                </template>

                <template #body>
                    <tr v-for="loan in loans" :key="loan.id" class="hover:bg-base-300">
                        <Link class="hover:text-primary" :href="route('requisicoes.show', loan.id)">
                            <td>{{ loan.loan_number }}</td>
                        </Link>

                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="w-12 h-12 rounded-full border border-gray-100 overflow-hidden shadow-sm">
                                        <img v-if="loan.user_photo_snapshot" :src="loan.user_photo_snapshot" :alt="loan.user.name" class="w-full h-full object-cover">
                                        <div v-else class="w-full h-full flex items-center justify-center bg-gray-200 text-base-content/70 text-2xl font-bold font-['Manrope']">
                                            {{ loan.user.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    {{ loan.user.name }}
                                </div>
                            </div>
                        </td>
                        <td>{{ loan.start_date }}</td>
                        <td>{{ loan.estimated_return_date }}</td>
                        <td>{{ loan.end_date ?? 'Em Andamento' }}</td>
                        <td><span class="badge badge-sm" :class="loan.status_color">{{ loan.status_label }}</span></td>
                    </tr>
                </template>
            </TableWrapper>
            <div v-else-if="loans && loans.length === 0 && isAdmin" class="text-center p-8">
                <p class="text-gray-500">Este livro ainda não foi requisitado.</p>
            </div>
        </div>
    </Layout>
</template>
