<template>
    <div>
        <button
            @click="ViewBalance()"
            type="button"
            class="btn btn-primary btn-xs"
            data-toggle="modal"
            :data-target="'#model' + cod"
        >
            FACTURAS
        </button>

        <!-- Modal -->
        <div
            class="modal fade"
            data-backdrop="static"
            data-keyboard="false"
            tabindex="-1"
            :id="'model' + cod"
            role="dialog"
            aria-labelledby="modelTitleId"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <tabs>
                            <tab name="Facture" :selected="true">
                                <form
                                    method="POST"
                                    @submit.enter.prevent="add(form.id)"
                                    autocomplete="off"
                                    onKeyPress="if(event.keyCode == 13) event.returnValue = false;"
                                >
                                    <div class="row mt-3">
                                        <div class="col-lg-4 col-12">
                                            <div class="form-group">
                                                <label for>Factura</label>
                                                <input
                                                    type="text"
                                                    v-validate="
                                                        'required|max:30|min:3'
                                                    "
                                                    class="form-control form-control-sm"
                                                    :class="{
                                                        'is-invalid':
                                                            submitted &&
                                                            errors.has('code'),
                                                    }"
                                                    v-model="form.code_facture"
                                                    name="code"
                                                />
                                                <div
                                                    v-if="
                                                        submitted &&
                                                        errors.has('code')
                                                    "
                                                    class="invalid-feedback"
                                                >
                                                    {{ errors.first("code") }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-12">
                                            <div class="form-group">
                                                <label for="">Meses</label>
                                                <select
                                                    v-model="form.term"
                                                    class="form-control form-control-sm"
                                                >
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-12">
                                            <div class="form-group">
                                                <label for>Total</label>
                                                <currency-input
                                                    v-validate="{
                                                        required: true,
                                                    }"
                                                    class="form-control form-control-sm"
                                                    v-currency="{
                                                        currency: 'USD',
                                                        precision: 0,
                                                        locale: 'en',
                                                    }"
                                                    :class="{
                                                        'is-invalid':
                                                            submitted &&
                                                            errors.has('tot'),
                                                    }"
                                                    v-model.number="form.tot"
                                                    name="tot"
                                                />
                                                <div
                                                    v-if="
                                                        submitted &&
                                                        errors.has('tot')
                                                    "
                                                    class="invalid-feedback"
                                                >
                                                    {{ errors.first("tot") }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        v-if="!send"
                                        class="btn btn-primary"
                                        type="button"
                                        disabled
                                    >
                                        <span
                                            class="spinner-border spinner-border-sm"
                                            role="status"
                                            aria-hidden="true"
                                        ></span>
                                        Loading...
                                    </button>
                                    <button
                                        v-if="send"
                                        :hidden="errors.any()"
                                        type="submit"
                                        v-bind:class="{
                                            'btn btn-outline-primary ':
                                                !this.form.id,
                                            'btn btn-outline-danger ':
                                                this.form.id,
                                        }"
                                    >
                                        <i
                                            v-bind:class="{
                                                'fi fi-wink': !this.form.id,
                                                'fi fi-like': this.form.id,
                                            }"
                                            aria-hidden="true"
                                        ></i>
                                    </button>
                                </form>
                                <div class="table-responsive mt-3">
                                    <VTable
                                        :data="balance"
                                        :page-size="4"
                                        :currentPage.sync="currentPage"
                                        @totalPagesChanged="totalPages = $event"
                                        class="table"
                                    >
                                        <template #head>
                                            <tr>
                                                <VTh sortKey="nit">Factura</VTh>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Plazo</th>
                                                <th></th>
                                            </tr>
                                        </template>
                                        <template #body="{ rows }">
                                            <tr
                                                v-for="row in rows"
                                                :key="row.id"
                                            >
                                                <td>
                                                    {{ row.code_facture }}
                                                </td>
                                                <td>{{ row.date }}</td>
                                                <td>
                                                    ${{ row.tot | currency }}
                                                </td>
                                                <td>{{ row.term }} meses</td>

                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-primary btn-sm"
                                                        @click="show(row)"
                                                    >
                                                        dd
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
                            </tab>
                            <tab name="Abonos">
                                <p>Contenido de Abonos</p>
                            </tab>
                            <tab name="data">
                                <div class="col-12 col-lg-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3>
                                                ${{ balanceTot | currency }}
                                            </h3>
                                            <p>
                                                Total en saldo pendiente
                                                <strong>Neto</strong>
                                            </p>
                                        </div>
                                        <div class="icon">
                                            <i class="fi fi-bar-chart"></i>
                                        </div>
                                    </div>
                                </div>
                            </tab>
                        </tabs>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                            @click="clear()"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from "vuex";
import { Tabs, Tab } from "vue-tabs-component";
export default {
    props: {
        cod: {
            type: Number,
            required: true,
        },
    },
    components: {
        Tabs,
        Tab,
    },
    data() {
        return {
            totalPages: 1,
            currentPage: 1,

            submitted: true,
            send: true,
            form: {
                code_facture: 0,
                term: 1,
                tot: 0,
                provider_id: this.cod,
            },
        };
    },
    methods: {
        add(id) {
            this.$validator.validate().then((valid) => {
                if (valid) {
                    this.route(id);
                }
            });
        },
        async route(id) {
            this.send = false;
            if (id) {
                let response = await axios.put(
                    this.urlbalance + "/" + id,
                    this.form
                );
                try {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: `${response.data.message}`,
                        showConfirmButton: false,
                        timer: 1500,
                    });
                    this.clear();
                    this.ViewBalance();
                } catch (error) {
                    console.log(error.response);
                }
            } else {
                let response = await axios.post(this.urlbalance, this.form);

                try {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: `${response.data.message}`,
                        showConfirmButton: false,
                        timer: 1600,
                    });
                    console.log(response);
                    this.ViewBalance();
                    this.clear();
                } catch (error) {
                    console.log(error.response);
                }
            }
        },
        ViewBalance() {
            this.$store.dispatch("Balanceactions", this.cod);
            this.$store.dispatch("BalanceTotactions", this.cod);
        },
        clear() {
            this.form.id = null;
            this.form.code_facture = 0;
            this.form.term = 1;
            this.form.tot = 0;
            this.form.provider_id = this.cod;

            this.$validator.reset();
            this.send = true;
            this.clientot.length = "";
        },
        show(row) {
            this.form.id = row.id;
            this.form.code_facture = row.code_facture;
            this.form.tot = row.tot;
            this.form.tern = row.term;
            this.form.provider_id = row.provider_id;
            console.log(this.form);
            this.send = true;
        },
    },
    computed: {
        ...mapState(["balance", "status", "urlbalance", "balanceTot"]),
    },
};
</script>
