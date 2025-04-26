<template>
    <div>
        <div class="form-group">
            <input
                type="text"
                class="form-control"
                v-model="filters.name.value"
                placeholder="Buscar productos"
            />
        </div>
        <VTable
            :data="productsInventory"
            :filters="filters"
            :page-size="6"
            :currentPage.sync="currentPage"
            @totalPagesChanged="totalPages = $event"
            class="table"
        >
            <template #head>
                <tr>
                    <th>Codigo</th>
                    <VTh sortKey="name">Nombre</VTh>
                    <th>Stock</th>
                </tr>
            </template>
            <template #body="{ rows }">
                <tr v-for="row in rows" :key="row.id">
                    <td>{{ row.code }}</td>
                    <td>{{ row.name }}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <span :class="getStockClass(row.stock)">
                                {{ row.stock }}
                            </span>
                        </div>
                    </td>
                </tr>
            </template>
        </VTable>
        <div class="text-xs-center">
            <VTPagination
                :currentPage.sync="currentPage"
                :total-pages="totalPages"
                :boundary-links="true"
                :maxPageLinks="4"
            />
        </div>
    </div>
</template>
<script>
import { mapState } from "vuex";
export default {
    data() {
        return {
            totalPages: 1,
            currentPage: 1,
            filters: {
                name: { value: "", keys: ["name"] },
            },
        };
    },
    created() {
        this.getList();
    },
    computed: {
        ...mapState(["productsInventory", "status", "urlproducts"]),
    },

    methods: {
        getList() {
            this.$store.dispatch("ProductIactions");
        },
        getStockClass(stock) {
            if (stock === 0) {
                return "badge badge-danger right"; // rojo
            } else if (stock >= 5 && stock <= 10) {
                return "badge badge-warning right"; // amarillo
            } else if (stock > 10) {
                return "badge badge-success right"; // verde
            }
            return "badge badge-secondary right"; // por si aca
        },
    },
};
</script>
