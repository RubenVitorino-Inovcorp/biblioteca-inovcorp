<script setup lang="ts">
    import TableWrapper from '@/Components/TableWrapper.vue';
    import Layout from '@/Layouts/AppLayout.vue'
    import { Link } from '@inertiajs/vue3'
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { Order, User } from '@/types';

    const props = defineProps<{
        order: Order;
    }>();

    const page = usePage<{
        auth: {
            user?: User | null;
        };
    }>();
    
    const isAdmin = computed<boolean>(() => !!page.props.auth.user?.is_admin);
</script>

<template>
    <Layout :title="`Encomenda ${order.order_number}`">

        <div class="show-card p-6 md:p-8 max-w-5xl w-full mx-auto my-auto space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <div class="md:col-span-6 md:col-start-4">
                    <h2 class="text-2xl font-bold text-base-content">Detalhes da Encomenda</h2>
                    <h3 class="text-xl text-base-content">{{ order.order_number }}</h3>
                </div>  
            </div> 

            <div class="divider"></div> 

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <div v-if="order.user" class="md:col-span-6 md:col-start-4">
                    <div class="flex items-center space-x-4">
                        <div class="avatar">
                            <div class="w-32 h-32 rounded-full border border-gray-100 overflow-hidden shadow-sm">
                                <img v-if="order.user.profile_photo_url" :src="order.user.profile_photo_url" :alt="order.user.name" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200 text-base-content/70 text-3xl font-bold">
                                    {{ order.user.name?.charAt(0)?.toUpperCase() }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800">Encomenda feita por</h2>
                            <Link class="text-xl text-gray-600 mt-2 hover:text-primary" :href="route('utilizadores.show', order.user.id)">{{ order.user?.name }}</Link>
                            <p class="text-gray-500 mt-1">{{ order.user?.email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>    

            <div>
                <h3 class="text-xl font-bold mb-4">Itens da Encomenda</h3>
                <div class="space-y-4">
                    <div v-for="item in order.items" :key="item.id" class="flex items-center space-x-4 bg-base-200 p-4 rounded-xl">
                        <div class="avatar">
                            <div class="w-16 h-16 rounded-md overflow-hidden">
                                <img :src="item.book?.image_url" :alt="item.book?.title" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="flex-1">
                            <Link :href="route(isAdmin ? 'livros.show' : 'catalog.livros.show', item.book?.id)" class="hover:text-primary">
                                <h4 class="font-bold text-lg">{{ item.book?.title }}</h4>
                            </Link>
                            <div class="text-sm text-gray-500">ISBN: {{ item.book?.isbn ?? 'N/A' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">{{ item.quantity }}x</div>
                            <div class="text-sm">{{ Number(item.price).toFixed(2) }} € uni.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <TableWrapper>
                <template #header>
                    <th>Número da Encomenda</th>
                    <th>Data da Encomenda</th>
                    <th>Total</th>
                    <th>Estado</th>
                </template>
                <template #body>
                    <td>{{ order.order_number }}</td>
                    <td>{{ new Date(order.created_at).toLocaleDateString('pt-PT') }}</td>
                    <td class="font-bold">{{ Number(order.total_price).toFixed(2) }} €</td>
                    <td>
                        <span class="badge badge-sm whitespace-nowrap" :class="order.status_color">
                            {{ order.status_label }}
                        </span>
                    </td>
                </template>
            </TableWrapper>
        </div>
    </Layout>
</template>
