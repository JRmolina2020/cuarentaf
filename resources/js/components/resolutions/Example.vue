<template>
    <div>
        <section class="section">
            <store-resolution ref="form_resolution"></store-resolution>
            <list-resolution v-on:show="show" class="mt-3"></list-resolution>
        </section>
    </div>
</template>
<script>
import StoreResolution from "./StoreResolution.vue";
import ListResolution from "./ListResolution.vue";

export default {
    components: {
        StoreResolution,
        ListResolution,
    },
    methods: {
        show(row) {
            this.$refs.form_resolution.show(row);
        },
    },
};
</script>
