<script setup>
</script>

<template>
    <div class="overflow-x-auto border border-base-200 rounded-xl bg-base-100">
        <table class="table">
            <!-- head -->
            <thead class="bg-base-200">
            <tr>
                <slot name="header" />
            </tr>
            </thead>

            <!-- body -->
            <tbody>
            <slot name="body" />
            </tbody>

            <!-- foot -->
            <tfoot class="bg-base-200" v-if="$slots.footer">
            <tr>
                <slot name="footer" />
            </tr>
            </tfoot>
        </table>
    </div>
</template>
<style scoped>

</style>
