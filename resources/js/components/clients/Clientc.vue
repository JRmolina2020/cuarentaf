<template>
    <div>
        <div class="form-group">
            <input
                type="text"
                class="form-control"
                v-model="filters.name.value"
                placeholder="Buscar clientes"
            />
        </div>
        <div class="table-responsive">
            <VTable
                :data="clientsactive"
                :filters="filters"
                :page-size="5"
                :currentPage.sync="currentPage"
                @totalPagesChanged="totalPages = $event"
                class="table"
            >
                <template #head>
                    <tr>
                        <VTh sortKey="nit">Nit</VTh>
                        <VTh sortKey="fullname">Nombre</VTh>
                        <th>Tel</th>
                        <th>Correo</th>
                        <th>Op</th>
                    </tr>
                </template>
                <template #body="{ rows }">
                    <tr v-for="row in rows" :key="row.id">
                        <td>{{ row.nit }}-{{ row.dv }}</td>
                        <td>{{ row.fullname }}</td>
                        <td>{{ row.phone }}</td>
                        <td>{{ row.email }}</td>
                        <td>
                            <button
                                type="button"
                                @click="getShow(row.id)"
                                class="btn btn-primary btn-xs fi fi-like"
                                data-toggle="modal"
                                data-target="#modelId"
                            ></button>
                        </td>
                    </tr>
                </template>
            </VTable>
        </div>
        <div class="text-xs-center">
            <VTPagination
                :currentPage.sync="currentPage"
                :total-pages="totalPages"
                :boundary-links="true"
            />
        </div>

        <div
            class="modal fade"
            id="modelId"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modelTitleId"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div>
                            <table class="table table-hover border-0">
                                <thead class="border-0">
                                    <tr>
                                        <th scope="col">Fecha venta</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Vendedor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="item in clientot"
                                        :key="item.id"
                                        class="small py-1 border-0"
                                    >
                                        <td>
                                            {{ item.date | formatearFecha }}
                                        </td>
                                        <td>${{ item.tot | currency }}</td>
                                        <td>{{ item.user }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from "vuex";
import toggleStatus from "../../mixins/toggleStatus";
export default {
    data() {
        return {
            totalPages: 1,
            currentPage: 1,
            filters: {
                name: { value: "", keys: ["fullname", "nit"] },
            },
        };
    },
    computed: {
        ...mapState(["clientsactive", "status", "clientot"]),
    },
    created() {
        this.getList();
    },
    methods: {
        getList() {
            this.$store.dispatch("ClientActiveactions");
        },
        getShow(id) {
            this.$store.dispatch("clientTotactions", id);
        },
    },
};
</script>
