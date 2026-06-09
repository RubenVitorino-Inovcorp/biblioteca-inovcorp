<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ShoppingCart, X, Trash2 } from '@lucide/vue';
import type { Cart } from '@/types';

const page = usePage<{
    cart?: Cart | null;
}>();

const open = ref(false);

const cart = computed<Cart | null>(() => page.props.cart ?? null);

const itemCount = computed<number>(() => {
    if (!cart.value?.items) return 0;
    return cart.value.items.reduce((acc, item) => acc + item.quantity, 0);
});

const totalPrice = computed<number>(() => {
    if (!cart.value?.items) return 0;
    return cart.value.items.reduce((acc, item) => {
        return acc + (item.quantity * Number(item.book.price ?? 0));
    }, 0);
});

const toggle = (): void => {
    open.value = !open.value;
};

const close = (): void => {
    open.value = false;
};

const limparCarrinho = (): void => {
    if (confirm('Tens a certeza que desejas esvaziar o carrinho?')) {
        router.delete(route('catalog.carrinho.destroy'), {
            preserveScroll: true,
            onSuccess: () => close(),
        });
    }
};

const closeOnEscape = (e: KeyboardEvent): void => {
    if (open.value && e.key === 'Escape') {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));
</script>

<template>
    <div class="cart-dropdown">
        <!-- Trigger -->
        <button
            type="button"
            class="cart-trigger"
            aria-label="Carrinho"
            @click="toggle"
        >
            <ShoppingCart :size="20" />
            <span v-if="itemCount > 0" class="cart-badge">{{ itemCount }}</span>
        </button>

        <!-- Overlay -->
        <div v-show="open" class="cart-overlay" @click="close" />

        <!-- Panel -->
        <transition
            enter-active-class="cart-panel-enter-active"
            enter-from-class="cart-panel-enter-from"
            enter-to-class="cart-panel-enter-to"
            leave-active-class="cart-panel-leave-active"
            leave-from-class="cart-panel-leave-from"
            leave-to-class="cart-panel-leave-to"
        >
            <div v-show="open" class="cart-panel">
                <!-- Header -->
                <div class="cart-panel-header">
                    <span class="cart-panel-title">Carrinho</span>
                    <button type="button" class="cart-panel-close" @click="close">
                        <X :size="18" />
                    </button>
                </div>

                <!-- Empty -->
                <div v-if="!cart || !cart.items || cart.items.length === 0" class="cart-panel-empty">
                    <ShoppingCart :size="32" class="cart-panel-empty-icon" />
                    <p>O teu carrinho está vazio.</p>
                </div>

                <!-- Items -->
                <template v-else>
                    <div class="cart-panel-items">
                        <div v-for="item in cart.items" :key="item.id" class="cart-panel-item">
                            <Link :href="route('catalog.livros.show', item.book.id)" class="cart-panel-item-img-link" @click="close">
                                <img :src="item.book.image_url" :alt="item.book.title" class="cart-panel-item-img" />
                            </Link>
                            <div class="cart-panel-item-info">
                                <Link :href="route('catalog.livros.show', item.book.id)" class="cart-panel-item-title" @click="close">
                                    {{ item.book.title }}
                                </Link>
                                <span class="cart-panel-item-qty">Qtd: {{ item.quantity }}</span>
                            </div>
                            <span class="cart-panel-item-price">
                                {{ (Number(item.book.price ?? 0) * item.quantity).toFixed(2) }}€
                            </span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="cart-panel-footer">
                        <div class="cart-panel-total-row">
                            <span>Total</span>
                            <span class="cart-panel-total-value">{{ totalPrice.toFixed(2) }}€</span>
                        </div>
                        <Link :href="route('catalog.carrinho.index')" class="cart-panel-view-btn" @click="close">
                            Ver carrinho
                        </Link>
                        <button type="button" class="cart-panel-clear-btn" @click="limparCarrinho">
                            <Trash2 :size="13" />
                            Esvaziar
                        </button>
                    </div>
                </template>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.cart-dropdown {
    position: relative;
}

/* ─── Trigger ─── */
.cart-trigger {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: none;
    border: 1px solid transparent;
    border-radius: 8px;
    cursor: pointer;
    color: var(--color-silk-muted);
    transition: all 0.15s ease;
}

.cart-trigger:hover {
    background: var(--color-silk-200);
    border-color: var(--color-silk-300);
    color: var(--color-silk-content);
}

.cart-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    font-family: 'Manrope', sans-serif;
    font-size: 10px;
    font-weight: 700;
    color: #ffffff;
    background: var(--color-primary);
    border-radius: 9999px;
}

/* ─── Overlay ─── */
.cart-overlay {
    position: fixed;
    inset: 0;
    z-index: 40;
}

/* ─── Panel ─── */
.cart-panel {
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    width: 340px;
    max-height: 480px;
    z-index: 50;
    display: flex;
    flex-direction: column;
    background: var(--color-base-100);
    border: 1px solid var(--color-silk-300);
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

/* Transition */
.cart-panel-enter-active,
.cart-panel-leave-active {
    transition: opacity 0.18s ease, transform 0.18s ease;
}

.cart-panel-enter-from,
.cart-panel-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.97);
}

.cart-panel-enter-to,
.cart-panel-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* ─── Panel Header ─── */
.cart-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid var(--color-silk-300);
}

.cart-panel-title {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: var(--color-silk-content);
}

.cart-panel-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: none;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    color: var(--color-silk-muted);
    transition: all 0.15s ease;
}

.cart-panel-close:hover {
    background: var(--color-silk-200);
    color: var(--color-silk-content);
}

/* ─── Empty ─── */
.cart-panel-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
    text-align: center;
}

.cart-panel-empty-icon {
    color: var(--color-silk-300);
    margin-bottom: 10px;
}

.cart-panel-empty p {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    color: var(--color-silk-muted);
}

/* ─── Items List ─── */
.cart-panel-items {
    flex: 1;
    overflow-y: auto;
    padding: 8px 0;
}

.cart-panel-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 16px;
    transition: background 0.12s ease;
}

.cart-panel-item:hover {
    background: var(--color-base-200);
}

.cart-panel-item-img-link {
    flex-shrink: 0;
}

.cart-panel-item-img {
    width: 40px;
    height: 52px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid var(--color-silk-300);
}

.cart-panel-item-info {
    flex: 1;
    min-width: 0;
}

.cart-panel-item-title {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-silk-content);
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.35;
    transition: color 0.12s ease;
}

.cart-panel-item-title:hover {
    color: var(--color-primary);
}

.cart-panel-item-qty {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    color: var(--color-silk-muted);
    margin-top: 2px;
    display: block;
}

.cart-panel-item-price {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: var(--color-silk-content);
    flex-shrink: 0;
    white-space: nowrap;
}

/* ─── Footer ─── */
.cart-panel-footer {
    padding: 14px 16px;
    border-top: 1px solid var(--color-silk-300);
}

.cart-panel-total-row {
    display: flex;
    justify-content: space-between;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: var(--color-silk-content);
    margin-bottom: 12px;
}

.cart-panel-total-value {
    color: var(--color-primary);
}

.cart-panel-view-btn {
    display: block;
    width: 100%;
    padding: 10px;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    background: var(--color-primary);
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    transition: background 0.15s ease;
}

.cart-panel-view-btn:hover {
    background: var(--color-primary-dark);
}

.cart-panel-clear-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    width: 100%;
    padding: 8px;
    margin-top: 8px;
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: #ba1a1a;
    background: none;
    border: 1px solid transparent;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.cart-panel-clear-btn:hover {
    background: rgba(186, 26, 26, 0.06);
    border-color: rgba(186, 26, 26, 0.15);
}
</style>
