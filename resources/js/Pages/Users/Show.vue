<script setup>
    import Layout from '@/Layouts/AppLayout.vue'
    import TableWrapper from '@/Components/TableWrapper.vue';
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3'
    import {Head, Link} from '@inertiajs/vue3'
    import { useDateFormat } from '@vueuse/core'


    const props = defineProps({
        user: Object,
        loans: Array
    })

</script>

<template>
    <Layout>
        <Head :title="props.user.name" />

        <div class="max-w-5xl mx-auto p-4 md:p-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Cabeçalho -->
                <div class="p-8 md:p-12 border-b border-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-10 items-center">
                        <!-- Avatar -->
                        <div class="md:col-span-4 flex justify-center">
                            <div class="relative group">
                                <div v-if="props.user.role?.id === $page.props.roles.ADMIN" class="absolute -top-1 -right-1 z-10">
                                    <span class="badge badge-primary font-bold shadow-md border-2 border-white px-3 py-3">Admin</span>
                                </div>
                                
                                <div class="avatar">
                                    <div class="rounded-full w-40 md:w-48">
                                        <img v-if="props.user.profile_photo_url" :src="props.user.profile_photo_url" :alt="props.user.name" class="object-cover h-full w-full" />
                                        <div v-else class="h-full w-full flex items-center justify-center bg-gray-100 text-primary text-6xl font-black font-['Manrope']">
                                            {{ props.user.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalhes -->
                        <div class="md:col-span-8 space-y-6 text-center md:text-left">
                            <div>
                                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight mb-2">{{ props.user.name }}</h1>
                                <div class="flex items-center justify-center md:justify-start gap-2 text-gray-500 font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ props.user.email }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto md:mx-0">
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 transition-all hover:bg-white hover:shadow-md">
                                    <span class="block text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1">Requisições</span>
                                    <span class="text-2xl font-black text-primary">{{ props.loans.length }}</span>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 transition-all hover:bg-white hover:shadow-md">
                                    <span class="block text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1">Membro desde</span>
                                    <span class="text-sm font-bold text-gray-700">{{ useDateFormat(props.user.created_at, 'MM/YYYY') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Histórico -->
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                            Histórico de Requisições
                            <span class="badge badge-ghost badge-sm py-3 px-3">{{ props.loans.length }}</span>
                        </h2>
                    </div>

                    <TableWrapper v-if="props.loans.length > 0">
                        <template #header>
                            <th>Número</th>
                            <th>Livro</th>
                            <th>Início</th>
                            <th>Previsão</th>
                            <th>Devolução</th>
                            <th>Estado</th>
                        </template>

                        <template #body>
                            <tr v-for="loan in props.loans" :key="loan.id" class="hover:bg-gray-50/80 transition-colors group">
                                <td class="text-xs font-bold text-gray-400 group-hover:text-primary transition-colors">
                                    <Link :href="route('requisicoes.show', loan.id)">#{{ loan.loan_number }}</Link>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <img :src="loan.book?.image_path" class="w-8 h-10 object-cover rounded shadow-sm" />
                                        <Link :href="route('catalog.livros.show', loan.book?.id)" class="font-bold text-sm hover:text-primary transition-colors line-clamp-1">
                                            {{ loan.book?.title }}
                                        </Link>
                                    </div>
                                </td>
                                <td class="text-sm text-gray-600">{{ loan.start_date }}</td>
                                <td class="text-sm text-gray-600">{{ loan.estimated_return_date }}</td>
                                <td class="text-sm text-gray-600">{{ loan.end_date ?? '---' }}</td>
                                <td>
                                    <span class="badge badge-sm font-bold px-3 py-3" :class="loan.status_color">
                                        {{ loan.status_label }}
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </TableWrapper>

                    <div v-else class="bg-gray-50 rounded-3xl py-16 px-8 text-center border-2 border-dashed border-gray-100">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white shadow-sm mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="text-gray-400 font-bold italic">Sem histórico de requisições.</h3>
                        <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Este utilizador ainda não realizou nenhum empréstimo na biblioteca.</p>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
