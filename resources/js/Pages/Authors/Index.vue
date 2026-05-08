<script setup>
import {Link, router} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";
  import AuthorEditModal from "@/Components/AuthorEditModal.vue";
  import AuthorDeleteForm from "@/Components/AuthorDeleteForm.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import {ref, watch} from "vue";
  import {debounce} from "lodash";
import InputSearch from "@/Components/InputSearch.vue";


  const props = defineProps({
      authors: Object,
      filters: Object,
  })

  const search = ref(props.filters?.search || '')

  const updateSearch = debounce((value) => {
      router.get(
          route('livros.index'),
          {
              search: value,
          },
          {
              preserveState: true,
              replace: true,
              preserveScroll: true
          }
      );
  }, 300);

  watch(search, (newValue) => {
      updateSearch(newValue);
  });

</script>

<template>
    <AppLayout title="Biblioteca - Autores">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Autores
                </h2>
                <InputSearch :filters="filters" route="autores.index" placeholder="Pesquisar autores..." />
                <Link :href="route('autores.create')" class="btn btn-primary btn-sm">
                    + Adicionar Autores
                </Link>
            </div>
        </template>

        <TableWrapper>
            <template #header>
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" />
                    </label>
                </th>
                <th>Nome</th>
                <th>Total Livros</th>
                <th></th>
                <th></th>
            </template>
            <template #body>
                <!-- row 3 -->
                <tr v-for="author in authors.data" :key="author.id" class="hover:bg-base-300">
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox" />
                        </label>
                    </th>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="mask mask-squircle h-12 w-12">
                                    <img
                                        :src="author.photo_path"
                                        :alt="author.name" />
                                </div>
                            </div>
                            <div>
                                <Link class="hover:text-secondary" :href="route('autores.show', author.id)">
                                    <div class="font-bold">{{ author.name }}</div>
                                </Link>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="badge badge-ghost">
                            {{ author.books_count }} {{ author.books_count === 1 ? 'livros' : 'livros' }}
                        </div>
                    </td>
                    <th>
                        <AuthorEditModal :author="author" />
                    </th>
                    <th>
                        <AuthorDeleteForm :author="author" />
                    </th>
                </tr>
            </template>
            <!-- foot -->
            <template #footer>
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" />
                    </label>
                </th>
                <th>Nome</th>
                <th>Total Livros</th>
                <th></th>
                <th></th>
            </template>
        </TableWrapper>

        <div v-if="authors.links && authors.links.length > 3" class="flex justify-center mt-8 mb-4">
            <div class="join">
                <Link
                    v-for="(link, index) in authors.links"
                    :key="index"
                    :href="link.url ?? ''"
                    class="join-item btn btn-sm"
                    :class="{
                    'btn-active btn-primary': link.active,
                    'btn-disabled opacity-50': !link.url
                }"
                    v-html="link.label"
                />
            </div>
        </div>

        <div v-if="authors.data.length === 0" class="text-center py-10">
            <p class="text-gray-500 italic">Ainda não há autores registados.</p>
        </div>
    </AppLayout>
</template>
