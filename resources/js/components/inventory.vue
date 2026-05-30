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
                    <th>M</th>
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
                    <td v-if="row.stock > 0">
                        <input
                            type="checkbox"
                            :value="row.id"
                            v-model="selectedProducts"
                        />
                    </td>
                </tr>
                <button
                    @click="updateStock"
                    :disabled="selectedProducts.length === 0"
                    class="btn btn-danger btn-sm"
                >
                    <i class="fas fa-sync-alt"></i> Reiniciar stock
                </button>
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
            selectedProducts: [],
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
        updateStock() {
            if (this.selectedProducts.length === 0) {
                Swal.fire("Selecciona al menos un producto", "", "warning");
                return;
            }

            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esto reiniciará el stock de los productos seleccionados.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, reiniciar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    axios
                        .post("/api/products/reset-stock", {
                            ids: this.selectedProducts,
                        })
                        .then(() => {
                            Swal.fire(
                                "Reiniciado",
                                "El stock fue reiniciado con éxito.",
                                "success"
                            );
                            this.getList();
                            this.selectedProducts = [];
                        })
                        .catch(() => {
                            Swal.fire(
                                "Error",
                                "Hubo un problema al reiniciar el stock.",
                                "error"
                            );
                        });
                }
            });
        },
    },
};
</script>
