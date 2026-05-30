<template>
    <div>
        <section class="section">
            <store-documents ref="form_documents"></store-documents>
            <list-documents v-on:show="show" class="mt-3"></list-documents>
        </section>
    </div>
</template>
<script>
import StoreDocuments from "./Store.vue";
import ListDocuments from "./List.vue";

export default {
    components: {
        StoreDocuments,
        ListDocuments,
    },
    methods: {
        show(row) {
            this.$refs.form_documents.show(row);
        },
    },
};
</script>
