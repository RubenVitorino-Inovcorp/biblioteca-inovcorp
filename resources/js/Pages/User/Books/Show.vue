<script setup lang="ts">
    import Layout from '@/Layouts/AppLayout.vue'
    import LoanCreateForm from '@/Components/LoanCreateForm.vue';
    import Reviews from '@/Components/Common/Reviews.vue';
    import TableWrapper from '@/Components/TableWrapper.vue';
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3'
    import { Link, router } from '@inertiajs/vue3'
    import { LibraryBig, ShoppingCart } from '@lucide/vue';
    import BookAvailabilityAlert from '@/Components/BookAvailabilityAlert.vue';
    import BookSuggestionsCarousel from '@/Components/BookSuggestionsCarousel.vue';
    import { Book, Loan, Review, User } from '@/types';
    import { toast } from "vue-sonner";

    const addToCart = (bookId: number): void => {
        router.post(route('catalog.carrinho.store'), {
            book_id: bookId,
            quantity: 1
        },{
            preserveScroll: true,
            onError: (errors) => {
                toast.error(errors.message || "Erro ao adicionar o livro ao carrinho.");
            }
        });
    };
    
    const props = defineProps<{
        book: Book;
        relatedBooks: Book[];
        loans?: Loan[];
        userReview?: Review | null;
        reviewableLoanId?: number | null;
    }>();

    const page = usePage<{
        auth: {
            user?: User | null;
        };
    }>();

    const isAdmin = computed<boolean>(() => {
        return !!page.props.auth.user?.is_admin;
    });

    const hasActiveLoan = computed<boolean>(() => {
        if (!page.props.auth.user) return false;
        return props.loans?.some(loan => 
            loan.user_id === page.props.auth.user!.id &&
            ['pending', 'active', 'overdue', 'return_pending'].includes(loan.status)
        ) ?? false;
    });
</script>

<template>
    <Layout :title="book.title">


        <div class="show-card p-6 md:p-8 max-w-5xl w-full mx-auto my-auto space-y-6 mt-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

                <div class="md:col-span-4">
                    <img :src="book.image_url" :alt="book.title" class="show-image shadow-lg" />
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

                    <div v-if="book.tags?.length">
                        <div class="show-label">Tags</div>
                        <div class="flex flex-wrap gap-2">
                            <span class="badge badge-outline badge-sm" v-for="tag in book.tags" :key="tag.id">
                                {{ tag.name }}
                            </span>
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
                                <button type="button" class="hover:cursor-pointer flex items-center gap-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-green-900 transition-colors font-['Manrope'] px-4 py-2">
                                    <LibraryBig :size="16" />
                                    Requisitar
                                </button>
                            </LoanCreateForm>
                        </div>
                        <div v-else-if="!book.is_available && !hasActiveLoan && $page.props.auth.user" class="flex justify-end mt-2">
                            <BookAvailabilityAlert :book="book.id" :user="$page.props.auth.user.id" :has_alert="!!book.has_alert" />
                        </div>
                    </div>

                    <div class="show-divider"></div>

                    <div class="flex justify-between">
                        <div class="show-isbn mb-2">
                            ISBN: {{ book.isbn }}
                        </div>
                        <div class="show-price flex flex-col items-end gap-2">
                            {{ book.price }}€
                            <button v-if="book.is_available && !hasActiveLoan && $page.props.auth.user" type="button" @click="addToCart(book.id)" class="hover:cursor-pointer flex items-center gap-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-green-900 transition-colors font-['Manrope'] px-4 py-2">
                                <ShoppingCart :size="16" />
                                Adicionar ao carrinho
                            </button>
                            <button v-else-if="!book.is_available && !hasActiveLoan && $page.props.auth.user" type="button" class="flex items-center gap-2 text-sm font-bold text-white bg-gray-300 rounded-lg px-4 py-2">
                                <ShoppingCart :size="16" />
                                Indisponível
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full mx-auto flex flex-col justify-center p-4 rounded-lg" v-if="relatedBooks.length > 0">
                <div class="text-2xl font-bold text-base-content mb-4">Outros livros que poderá querer ler</div>
                <BookSuggestionsCarousel :books="relatedBooks" />
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
        </div>
        <Reviews :reviews="props.book.reviews" :book="props.book" :userReview="props.userReview" :reviewableLoanId="props.reviewableLoanId" />
    </Layout>   

</template>
