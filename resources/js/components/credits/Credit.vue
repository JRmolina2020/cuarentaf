<template>
    <div>
        <div class="form-group">
            <input
                type="text"
                class="form-control"
                v-model="filters.name.value"
                placeholder="Buscar nota credito"
            />
        </div>
        <div class="table-responsive">
            <VTable
                :data="creditNote"
                :filters="filters"
                :page-size="100"
                :currentPage.sync="currentPage"
                @totalPagesChanged="totalPages = $event"
                class="table table-dark table-striped"
            >
                <template #head>
                    <tr>
                        <th>Número</th>
                        <th>Factura referenciada</th>
                        <th>Fecha validación</th>
                        <th>Valor bruto</th>
                        <th>QR</th>
                    </tr>
                </template>
                <template #body="{ rows }">
                    <tr v-for="row in rows" :key="row.id">
                        <td>{{ row.number }}</td>
                        <td>{{ row.reference_code }}</td>
                        <td>{{ row.validated }}</td>
                        <td>${{ row.gross_value | currency }}</td>
                        <td>
                            <button
                                class="btn btn-dark btn-sm"
                                @click="showQR(row.qr)"
                                data-toggle="modal"
                                data-target="#qrModal"
                            >
                                <i class="fi fi-check"></i>
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
        <div class="modal fade" id="qrModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Consulta DIAN</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                        >
                            &times;
                        </button>
                    </div>

                    <div class="modal-body d-flex justify-content-center">
                        <qrcode :text="qrLink || ''" :size="130" level="H" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Qrcode from "vue-qrcode-component";
import { mapState } from "vuex";
export default {
    data() {
        return {
            qrLink: null,
            totalPages: 1,
            currentPage: 1,
            filters: {
                name: { value: "", keys: ["reference_code"] },
            },
        };
    },
    components: {
        Qrcode,
    },
    computed: {
        ...mapState(["creditNote", "status"]),
    },
    created() {
        this.getList();
    },
    methods: {
        getList() {
            this.$store.dispatch("Creditactions");
        },
        showQR(link) {
            this.qrLink = link;
        },
    },
};
</script>
