<script setup>
  import {Link} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import { computed } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import { CirclePlus, User2Icon } from "@lucide/vue";
  
  const props = defineProps({
      users: Object,
      filters: Object,
  })

//   const bookSortOptions = [
//       { value: 'preco_asc', label: 'Preço: Baixo para Alto' },
//       { value: 'preco_desc', label: 'Preço: Alto para Baixo' },
//       { value: 'titulo_az', label: 'Título (A-Z)' },
//       { value: 'titulo_za', label: 'Título (Z-A)'},
//   ];
</script>

<template>
    <AppLayout title="Utilizadores">
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                    <User2Icon :size="20"/> Utilizadores
                </h2>
               <InputSearch :filters="filters" route="utilizadores.index" placeholder="Pesquisar utilizador..." />

                <div class="header-actions">
                    <Link :href="route('utilizadores.create')" class="btn-add">
                        <CirclePlus :size="16" /> Criar Utilizador
                    </Link>
                    <!-- <ExportButton route-name="utilizadores.export" :filters="filters">
                        <DownloadIcon :size="16" /> Exportar Utilizadores
                    </ExportButton> -->
                    <!-- <FilterDropdown
                        route-name="utilizadores.index"
                        :filters="filters"
                        :sort-options="bookSortOptions"
                        :publishers="publishers"
                        :authors="authors"
                    /> -->
                </div>
            </div>
        </template>

        <TableWrapper>
                <template #header>
                    <th></th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo de Utilizador</th>
                    <th></th>
                    <th></th>
                </template>

                <template #body>
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-base-300">
                        <th></th>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <img v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-12 w-12 border border-gray-100">
                                    <div v-else class="rounded-full h-12 w-12 flex items-center justify-center bg-gray-200 text-[#3c4a42] shadow-sm border border-gray-100 text-2xl font-bold font-['Manrope']">
                                        {{ user.name?.charAt(0)?.toUpperCase() }}
                                    </div>
                                </div>
                                <div>
                                    <Link class="hover:text-primary" :href="route('utilizadores.show', user.id)">
                                        <div class="font-bold">{{ user.name }}</div>
                                        <div class="text-sm opacity-50">{{ user.email }}</div>
                                    </Link>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ user.email }}
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                <div :class="{ 'badge': true, 'badge-error': user.role === 'admin', 'badge-secondary': user.role === 'user' }" v-if="user.role">
                                    {{ user.role === 'admin' ? 'Administrador' : 'Utilizador' }}
                                </div>
                            </div>
                        </td>
                        <th>
                            <!-- <UserEditModal :user="user" /> -->
                        </th>
                        <th>
                            <!-- <UserDeleteForm :user="user" /> -->
                        </th>
                    </tr>
                </template>

                <template #footer>
                    <th></th>
                    <th>Utilizador</th>
                    <th>Email</th>
                    <th>Tipo de Utilizador</th>
                    <th></th>
                    <th></th>
                </template>
        </TableWrapper>

        <div v-if="users.links && users.links.length > 3" class="pagination">
            <div class="pagination-list">
                <Link
                    v-for="(link, index) in users.links"
                    :key="index"
                    :href="link.url ?? ''"
                    class="pagination-item"
                    :class="{
                    'pagination-item--active': link.active,
                    'pagination-item--disabled': !link.url
                }"
                    v-html="link.label"
                />
            </div>
        </div>

        <div v-if="users.data.length === 0" class="text-center py-10">
            <p class="empty-state-text">Ainda não há utilizadores registados.</p>
        </div>
    </AppLayout>
</template>

<style scoped>
    .page-title {
        font-family: 'Manrope', sans-serif;
        font-size: 17px;
        font-weight: 600;
        color: #191c1e;
        line-height: 1.4;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .empty-state-text {
        font-family: 'Manrope', sans-serif;
        font-size: 14px;
        color: #6c7a71;
        font-style: italic;
    }
</style>
