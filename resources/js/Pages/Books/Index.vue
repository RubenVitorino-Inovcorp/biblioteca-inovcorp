<script setup>

  import {Link} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";
  import BookEditModal from "@/Components/BookEditModal.vue";
  import BookDeleteForm from "@/Components/BookDeleteForm.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import FilterDropdown from "@/Components/FilterDropdown.vue";

  const props = defineProps({
      books: Object,
      authors: Array,
      publishers: Array,
      filters: Object,
  })

  const bookSortOptions = [
      { value: 'preco_asc', label: 'Preço: Baixo para Alto' },
      { value: 'preco_desc', label: 'Preço: Alto para Baixo' },
      { value: 'titulo_az', label: 'Título (A-Z)' },
  ];
</script>

<template>
    <AppLayout title="Biblioteca - Livros">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Livros
                </h2>
               <InputSearch :filters="filters" route="livros.index" placeholder="Pesquisar livro..." />

                <div>
                    <Link :href="route('livros.create')" class="btn btn-primary btn-sm">
                        + Adicionar Livro
                    </Link>
                    <FilterDropdown
                        route-name="livros.index"
                        :filters="filters"
                        :sort-options="bookSortOptions"
                        :publishers="publishers"
                        :authors="authors"
                    />
                </div>

            </div>
        </template>

        <TableWrapper>
                <template #header>
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox" />
                        </label>
                    </th>
                    <th>Livro</th>
                    <th>Preço (€)</th>
                    <th>Autor</th>
                    <th>Editora</th>
                    <th></th>
                    <th></th>
                </template>
                <template #body>
                <!-- row 3 -->
                <tr v-for="book in books.data" :key="book.id" class="hover:bg-base-300">
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
                                        :src="book.image_path"
                                        :alt="book.title" />
                                </div>
                            </div>
                            <div>
                                <Link class="hover:text-secondary" :href="route('livros.show', book.id)">
                                    <div class="font-bold">{{ book.title }}</div>
                                    <div class="text-sm opacity-50">{{ book.isbn }}</div>
                                </Link>
                            </div>
                        </div>
                    </td>
                    <td>
                        {{ book.price }}€
                    </td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <div class="badge badge-primary" v-for="author in book.authors" :key="author.id">
                                <Link :href="route('autores.show', author.id)">
                                    {{ author.name }}
                                </Link>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <div class="badge badge-secondary gap-x-6 cursor-pointer" v-if="book.publisher">
                                <Link :href="route('editoras.show', book.publisher?.id)">
                                    {{ book.publisher?.name }}
                                </Link>
                            </div>
                        </div>
                    </td>
                    <th>
                        <BookEditModal :book="book" :publishers="publishers" :authors="authors" />
                    </th>
                    <th>
                        <BookDeleteForm :book="book" />
                    </th>
                </tr>
                </template>
                <!-- foot -->
                <template #footer>
                    <th></th>
                    <th>Livro</th>
                    <th>Preço (€)</th>
                    <th>Autor</th>
                    <th>Editora</th>
                    <th></th>
                    <th></th>
                </template>
        </TableWrapper>

        <div v-if="books.links && books.links.length > 3" class="flex justify-center mt-8 mb-4">
            <div class="join">
                <Link
                    v-for="(link, index) in books.links"
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

        <div v-if="books.data.length === 0" class="text-center py-10">
            <p class="text-base-content opacity-80 italic">Ainda não há livros registados.</p>
        </div>
    </AppLayout>
</template>
