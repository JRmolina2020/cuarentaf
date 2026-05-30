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
                :data="clients"
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
                    <tr
                        v-for="row in rows"
                        :key="row.id"
                        class="small py-1 border-0"
                    >
                        <td>{{ row.nit }}-{{ row.dv }}</td>
                        <td>{{ row.fullname }}</td>
                        <td>{{ row.phone }}</td>
                        <td>{{ row.email }}</td>

                        <td>
                            <button
                                type="button"
                                @click="$emit('show', row)"
                                class="btn bg-warning btn-xs"
                            >
                                <i class="fi fi-eye"></i>
                            </button>
                            <button
                                @click="handleToggleStatus(row)"
                                :class="
                                    row.status
                                        ? 'btn btn-success btn-xs'
                                        : 'btn btn-danger btn-xs'
                                "
                            >
                                <i
                                    :class="
                                        row.status
                                            ? 'fi fi-radio-btn-active'
                                            : 'fi fi-radio-btn-passive'
                                    "
                                    class="estado-icon"
                                ></i>
                            </button>
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
        ...mapState(["clients", "status", "urlclients"]),
    },
    created() {
        this.getList();
    },
    methods: {
        getList() {
            this.$store.dispatch("Clientactions");
        },
        async handleToggleStatus(row) {
            await toggleStatus({
                id: row.id,
                currentStatus: row.status,
                entity: "cliente",
                url: "/api/clients",
                callback: this.getList,
            });
        },
    },
};
</script>
