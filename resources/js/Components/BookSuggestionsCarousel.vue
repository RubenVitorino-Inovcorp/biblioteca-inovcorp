<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    books: Array,
});
</script>

<template>
    <div class="flex gap-4 overflow-x-auto pb-4 pt-2 snap-x snap-mandatory scrollbar-thin">
        <Link
            v-for="book in books"
            :key="book.id"
            :href="route('catalog.livros.show', book.id)"
            class="snap-start shrink-0 w-32 group"
        >
            <div class="relative overflow-hidden rounded-lg shadow-sm ring-1 ring-gray-100 transition-all duration-300 group-hover:shadow-md group-hover:ring-primary/30">
                <img
                    :src="book.image_url"
                    :alt="book.title"
                    class="w-full h-44 object-cover transition-transform duration-300 group-hover:scale-105"
                />
            </div>
            <p class="mt-2 text-xs font-semibold text-base-content/80 line-clamp-2 leading-tight group-hover:text-primary transition-colors">
                {{ book.title }}
            </p>
            <p v-if="book.authors?.length" class="mt-0.5 text-[10px] text-base-content/50 line-clamp-1">
                {{ book.authors.map(a => a.name).join(', ') }}
            </p>
        </Link>
    </div>
</template>
