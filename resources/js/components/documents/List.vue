<template>
    <div>
        <div class="form-group">
            <input
                type="text"
                class="form-control"
                v-model="filters.name.value"
                placeholder="Buscar"
            />
        </div>
        <VTable
            :data="document"
            :filters="filters"
            :page-size="5"
            :currentPage.sync="currentPage"
            @totalPagesChanged="totalPages = $event"
            class="table table-hover align-middle"
        >
            <template #head>
                <tr class="align-middle text-uppercase text-muted small">
                    <VTh sortKey="prefijo">Prefijo</VTh>
                    <th>Tipo</th>
                    <th>Código</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </template>

            <template #body="{ rows }">
                <tr v-for="row in rows" :key="row.id">
                    <td>{{ row.prefijo }}</td>
                    <td>
                        {{ row.type }}
                    </td>
                    <td>{{ row.code }}</td>
                    <td class="text-center">
                        <button
                            type="button"
                            @click="$emit('show', row)"
                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                            title="Ver detalles"
                        >
                            <i class="fa fa-eye"></i> Ver
                        </button>
                        <button
                            @click="handleToggleStatus(row)"
                            :class="
                                row.status
                                    ? 'btn btn-success btn-sm'
                                    : 'btn btn-danger btn-sm'
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
                name: { value: "", keys: ["name"] },
            },
        };
    },
    computed: {
        ...mapState(["document", "status", "urldocuments"]),
    },
    created() {
        this.getList();
    },
    methods: {
        getList() {
            this.$store.dispatch("Documentactions");
        },
        async handleToggleStatus(row) {
            await toggleStatus({
                id: row.id,
                currentStatus: row.status,
                entity: "documentos",
                url: "api/documents",
                callback: this.getList,
            });
        },
    },
};
</script>
