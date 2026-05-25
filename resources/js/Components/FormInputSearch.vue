<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    label: {
        type: String,
        required: true
    },
    placeholder: {
        type: String,
        default: 'Procurar...'
    },
    items: {
        type: Array,
        required: true
    },
    modelValue: {
        type: [Array, Object, null],
        required: true
    },
    multiple: {
        type: Boolean,
        default: false
    },
    selectedItemsArray: {
        type: Array,
        default: []
    }
})

const emit = defineEmits(['update:modelValue'])

const search = ref('')
const isFocused = ref(false)
const searchInput = ref(null)

// Filtra itens disponíveis com base na consulta E remove itens já selecionados
const availableItems = computed(() => {
    const query = search.value.toLowerCase().trim()
    
    return props.items.filter(item => {
        // Avalia o estado de seleção com base no modo único/múltiplo
        const isSelected = props.multiple 
            ? (props.modelValue || []).some(selected => selected.id === item.id)
            : props.modelValue?.id === item.id
        
        if (isSelected) return false
        
        return item.name.toLowerCase().includes(query)
    })
})

// Normaliza seleções únicas e múltiplas em um array iterável para o loop de UI do badge
const selectedItemsArray = computed(() => {
    if (!props.modelValue) return []
    return props.multiple ? props.modelValue : [props.modelValue]
})

const selectItem = (item) => {
    if (props.multiple) {
        const currentList = Array.isArray(props.modelValue) ? props.modelValue : []
        emit('update:modelValue', [...currentList, item])
        search.value = ''
        // Manter foco e dropdown abertos para múltiplas seleções
    } else {
        emit('update:modelValue', item)
        search.value = ''
        isFocused.value = false
        if (searchInput.value) searchInput.value.blur()
    }
}

const removeItem = (itemToRemove) => {
    if (props.multiple) {
        const filteredList = props.modelValue.filter(item => item.id !== itemToRemove.id)
        emit('update:modelValue', filteredList)
    } else {
        emit('update:modelValue', null)
    }
}
</script>

<template>
    <div class="form-control w-full relative">
        <label v-if="label" class="label">
            <span class="label font-semibold text-[#3c4a42]">{{ label }}</span>
        </label>
        
        <input 
            ref="searchInput"
            v-model="search"
            type="text"
            class="input border focus:border-primary focus:border-2 p-4 focus:outline-none w-full"
            :placeholder="placeholder"
            @focus="isFocused = true"
            @blur="isFocused = false"
        />

        <ul 
            v-if="isFocused && (availableItems.length > 0 || search.trim() !== '')"
            class="menu absolute z-50 w-full mt-2 bg-base-200 rounded-box shadow-xl max-h-60 flex-nowrap overflow-y-auto top-[4.5rem] left-0"
        >
            <li v-for="item in availableItems" :key="item.id">
                <a @mousedown.prevent="selectItem(item)" class="cursor-pointer">
                    {{ item.name }}
                </a>
            </li>
            <li v-if="search.trim() !== '' && !availableItems.some(i => i.name.toLowerCase() === search.trim().toLowerCase())">
                <a @mousedown.prevent="selectItem({ id: search.trim(), name: search.trim() })" class="cursor-pointer text-[#006c49] font-bold">
                    Criar "{{ search.trim() }}"
                </a>
            </li>
        </ul>

        <div v-if="selectedItemsArray.length > 0" class="flex flex-wrap gap-2 mt-3">
            <div 
                v-for="item in selectedItemsArray" 
                :key="item.id"
                class="badge badge-primary gap-1 pl-3 pr-1 py-3 font-semibold shadow-sm"
            >
                {{ item.name }}
                <button 
                    type="button"
                    class="btn btn-ghost btn-xs btn-circle hover:bg-primary-focus text-current"
                    @click.prevent="removeItem(item)"
                    aria-label="Remover"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>