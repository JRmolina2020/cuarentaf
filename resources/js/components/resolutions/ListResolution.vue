<template>
    <div>
        <div class="form-group">
            <input
                type="text"
                class="form-control"
                v-model="filters.name.value"
                placeholder="Buscar resoluciones"
            />
        </div>
        <div class="table-responsive">
            <VTable
                :data="resolutions"
                :filters="filters"
                :page-size="5"
                :currentPage.sync="currentPage"
                @totalPagesChanged="totalPages = $event"
                class="table"
            >
                <template #head>
                    <tr>
                        <th>Numero resolución</th>
                        <th>Prefijo</th>
                        <th>F inicio</th>
                        <th>F final</th>
                        <th>Número inicial</th>
                        <th>Número actual</th>
                        <th>Número final</th>
                        <th>Entorno</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </template>
                <template #body="{ rows }">
                    <tr v-for="row in rows" :key="row.id">
                        <td>{{ row.numero_resolucion }}</td>
                        <td>{{ row.prefijo }}</td>
                        <td>{{ row.fecha_inicio }}</td>
                        <td>{{ row.fecha_fin }}</td>
                        <td>{{ row.numero_inicial }}</td>
                        <td>{{ row.numero_actual }}</td>
                        <td>{{ row.numero_final }}</td>
                        <td v-if="row.isint">
                            <span class="badge badge-success">producción</span>
                        </td>
                        <td v-else>
                            <span class="badge badge-danger">prueba</span>
                        </td>
                        <td v-if="row.activa">
                            <span class="badge badge-success">activa</span>
                        </td>
                        <td v-else>
                            <span class="badge badge-danger">inhabilitada</span>
                        </td>

                        <td>
                            <button
                                type="button"
                                @click="$emit('show', row)"
                                class="btn bg-warning btn-sm"
                            >
                                <i class="fi fi-eye"></i>
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
export default {
    data() {
        return {
            totalPages: 1,
            currentPage: 1,
            filters: {
                name: { value: "", keys: ["prefijo"] },
            },
        };
    },
    computed: {
        ...mapState(["resolutions", "status", "urlresolutions"]),
    },
    created() {
        this.getList();
    },
    methods: {
        getList() {
            this.$store.dispatch("Resolutionsactions");
        },
    },
};
</script>
