<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ShoppingCart, Trash2, CreditCard, BookOpen, CircleX } from '@lucide/vue';
import type { Cart } from '@/types';

interface Props {
    cart: Cart;
}

const props = defineProps<Props>();

const form = useForm({
    street: '',
    city: '',
    postal_code: '',
});

const totalCarrinho = computed<number>(() => {
    return props.cart.items.reduce((acc, item) => {
        const precoUnidade = Number(item.book.price ?? 0);
        return acc + (item.quantity * precoUnidade);
    }, 0);
});

const totalItems = computed<number>(() => {
    return props.cart.items.reduce((acc, item) => acc + item.quantity, 0);
});

const limparCarrinho = (): void => {
    if (confirm('Tens a certeza que desejas esvaziar o carrinho?')) {
        router.delete(route('catalog.carrinho.destroy'));
    }
};

const formatPostalCode = (): void => {
    let value = form.postal_code.replace(/\D/g, ''); // Manter apenas números
    if (value.length > 4) {
        value = value.substring(0, 4) + '-' + value.substring(4, 7);
    }
    form.postal_code = value;
};

const procederParaCheckout = (): void => {
    form.post(route('catalog.encomendas.store'));
};

</script>

<template>
    <AppLayout title="Carrinho">
        <Head title="Carrinho" />
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                    <ShoppingCart :size="20" /> Carrinho
                </h2>

            </div>
        </template>

        <!-- Erro vindo do checkout (quando o payment foi rejeitado, por exemplo) -->
        <div v-if="$page.props.flash?.error" class="alert alert-error max-w-3xl mx-auto mb-4">
           <img src="/storage/imagens/error.webp" alt="Erro" class="max-w-16" /> <span class="font-semibold text-xl text-base-100"> {{ $page.props.flash.error }}</span>
        </div>

        <!-- Empty State -->
        <div v-if="cart.items.length === 0" class="cart-empty">
            <div class="cart-empty-icon">
                <ShoppingCart :size="48" />
            </div>
            <p class="cart-empty-title">O teu carrinho está vazio</p>
            <p class="cart-empty-text">Explora o catálogo e adiciona livros ao teu carrinho.</p>
            <Link :href="route('catalog.livros.index')" class="cart-empty-btn">
                <BookOpen :size="16" />
                Ver catálogo
            </Link>
        </div>

        <!-- Cart Items -->
        <div v-else class="cart-layout">
            <div class="cart-items-wrapper">
                <div class="cart-items-header">
                    <span class="cart-items-count">{{ totalItems }} {{ totalItems === 1 ? 'item' : 'itens' }}</span>
                    <button @click="limparCarrinho" class="cart-clear-btn" type="button">
                        Esvaziar
                    </button>
                </div>

                <div class="cart-items-list">
                    <div v-for="item in cart.items" :key="item.id" class="cart-item">
                        <Link :href="route('catalog.livros.show', item.book.id)" class="cart-item-image-link">
                            <img
                                :src="item.book.image_url"
                                :alt="item.book.title"
                                class="cart-item-image"
                            />
                        </Link>
                        <div class="cart-item-details">
                            <Link :href="route('catalog.livros.show', item.book.id)" class="cart-item-title">
                                {{ item.book.title }}
                            </Link>
                            <p v-if="item.book.authors?.length" class="cart-item-authors">
                                <span v-for="(author, index) in item.book.authors" :key="author.id">
                                    {{ author.name }}<span v-if="index < item.book.authors.length - 1">, </span>
                                </span>
                            </p>
                            <p v-if="item.book.publisher" class="cart-item-publisher">
                                {{ item.book.publisher.name }}
                            </p>
                            <div class="cart-item-qty">
                                Qtd: {{ item.quantity }}
                            </div>
                        </div>
                        <div class="cart-item-pricing">
                            <span class="cart-item-total">{{ (Number(item.book.price ?? 0) * item.quantity).toFixed(2) }}€</span>
                            <span class="cart-item-unit">{{ Number(item.book.price ?? 0).toFixed(2) }}€/un</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Morada e Resumo -->
            <form @submit.prevent="procederParaCheckout" class="cart-summary">
                <div class="cart-summary-card">
                    <h3 class="cart-summary-title !mb-0">Morada para entrega</h3>
                    <div>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend mt-2">Rua</legend>
                            <input required v-model="form.street" type="text" placeholder="Rua das Flores" class="input input-sm border focus:border-primary focus:border-2 p-4 focus:outline-none w-full" :class="{ 'border-error': form.errors.street }" />
                            <div v-if="form.errors.street" class="text-error text-xs mt-1">{{ form.errors.street }}</div>

                            <legend class="fieldset-legend mt-2">Localidade</legend>
                            <input required v-model="form.city" type="text" placeholder="Lisboa" class="input input-sm border focus:border-primary focus:border-2 p-4 focus:outline-none w-full" :class="{ 'border-error': form.errors.city }" />
                            <div v-if="form.errors.city" class="text-error text-xs mt-1">{{ form.errors.city }}</div>

                            <legend class="fieldset-legend mt-2">Código Postal</legend>
                            <input required pattern="^\d{4}-\d{3}$" title="Código postal inválido. Deve ter o formato 0000-000." v-model="form.postal_code" @input="formatPostalCode" type="text" placeholder="1234-567" maxlength="8" class="input input-sm border focus:border-primary focus:border-2 p-4 focus:outline-none w-full" :class="{ 'border-error': form.errors.postal_code }" />
                            <div v-if="form.errors.postal_code" class="text-error text-xs mt-1">{{ form.errors.postal_code }}</div>
                        </fieldset>
                    </div>
                </div>

                <div class="cart-summary-card mt-4">
                    <h3 class="cart-summary-title">Resumo</h3>
                    <div class="cart-summary-row">
                        <span>Subtotal ({{ totalItems }} {{ totalItems === 1 ? 'item' : 'itens' }})</span>
                        <span>{{ totalCarrinho.toFixed(2) }}€</span>
                    </div>
                    <div class="cart-summary-divider"></div>
                    <div class="cart-summary-row cart-summary-total">
                        <span>Total</span>
                        <span>{{ totalCarrinho.toFixed(2) }}€</span>
                    </div>
                    <button :disabled="form.processing" class="cart-checkout-btn disabled:opacity-75" type="submit">
                        <CreditCard :size="16" />
                        Proceder para o Pagamento
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.page-title {
    font-family: 'Manrope', sans-serif;
    font-size: 17px;
    font-weight: 600;
    color: var(--color-silk-content);
    line-height: 1.4;
}

/* ─── Empty State ─── */
.cart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 24px;
    text-align: center;
}

.cart-empty-icon {
    color: var(--color-silk-300);
    margin-bottom: 20px;
}

.cart-empty-title {
    font-family: 'Manrope', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--color-silk-content);
    margin-bottom: 6px;
}

.cart-empty-text {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    color: var(--color-silk-muted);
    margin-bottom: 24px;
}

.cart-empty-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    background: var(--color-primary);
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.15s ease;
}

.cart-empty-btn:hover {
    background: var(--color-primary-dark);
}

/* ─── Cart Layout ─── */
.cart-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    max-width: 1100px;
    margin: 24px auto 0;
    padding: 0 16px;
}

/* ─── Items ─── */
.cart-items-wrapper {
    min-width: 0;
}

.cart-items-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.cart-items-count {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-silk-content);
}

.cart-clear-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: #ba1a1a;
    background: none;
    border: 1px solid transparent;
    border-radius: 6px;
    padding: 5px 12px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.cart-clear-btn:hover {
    background: rgba(186, 26, 26, 0.06);
    border-color: rgba(186, 26, 26, 0.2);
}

.cart-items-list {
    display: flex;
    flex-direction: column;
    gap: 1px;
    background: var(--color-silk-300);
    border: 1px solid var(--color-silk-300);
    border-radius: 10px;
    overflow: hidden;
}

.cart-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 20px;
    background: var(--color-base-100);
    transition: background 0.15s ease;
}

.cart-item:hover {
    background: var(--color-base-200);
}

.cart-item-image-link {
    flex-shrink: 0;
}

.cart-item-image {
    width: 64px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid var(--color-silk-300);
}

.cart-item-details {
    flex: 1;
    min-width: 0;
}

.cart-item-title {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-silk-content);
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.15s ease;
}

.cart-item-title:hover {
    color: var(--color-primary);
}

.cart-item-authors {
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    color: var(--color-silk-muted);
    margin-top: 3px;
}

.cart-item-publisher {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    color: var(--color-silk-subtle);
    margin-top: 2px;
}

.cart-item-qty {
    display: inline-block;
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-silk-muted);
    background: var(--color-silk-200);
    border: 1px solid var(--color-silk-300);
    padding: 2px 8px;
    border-radius: 4px;
    margin-top: 8px;
}

.cart-item-pricing {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    flex-shrink: 0;
}

.cart-item-total {
    font-family: 'Manrope', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--color-silk-content);
}

.cart-item-unit {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    color: var(--color-silk-muted);
    margin-top: 2px;
}

/* ─── Summary ─── */
.cart-summary {
    position: sticky;
    top: 100px;
    align-self: flex-start;
}

.cart-summary-card {
    background: var(--color-base-100);
    border: 1px solid var(--color-silk-300);
    border-radius: 10px;
    padding: 20px;
}

.cart-summary-title {
    font-family: 'Manrope', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--color-silk-content);
    margin-bottom: 16px;
}

.cart-summary-row {
    display: flex;
    justify-content: space-between;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    color: var(--color-silk-muted);
    padding: 4px 0;
}

.cart-summary-divider {
    height: 1px;
    background: var(--color-silk-300);
    margin: 12px 0;
}

.cart-summary-total {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-silk-content);
}

.cart-checkout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px 16px;
    margin-top: 20px;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    background: var(--color-primary);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.15s ease;
}

.cart-checkout-btn:hover {
    background: var(--color-primary-dark);
}

/* ─── Responsive ─── */
@media (max-width: 768px) {
    .cart-layout {
        grid-template-columns: 1fr;
    }

    .cart-summary {
        position: static;
    }

    .cart-item-image {
        width: 52px;
        height: 66px;
    }
}
</style>