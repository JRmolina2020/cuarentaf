<template>
    <div>
        <Modal-Resource
            v-on:clear="clear"
            title="Registro de clientes"
            sone="modal-dialog modal-lg"
        >
            <section slot="titlebutton">Registrar resolución</section>

            <section v-if="!form.id" slot="title">
                Registro de resolución
            </section>
            <section v-else slot="title">Editar resolución</section>
            <section slot="closebtn">
                <button
                    @click="clear()"
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </section>
            <section slot="body">
                <form
                    method="POST"
                    @submit.enter.prevent="
                        add(form.id, actions, urlresolutions)
                    "
                    autocomplete="off"
                    onKeyPress="if(event.keyCode == 13) event.returnValue = false;"
                >
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for>Resolución</label>
                                <input
                                    type="number"
                                    v-validate="'required|numeric|min:1'"
                                    class="form-control form-control-sm"
                                    :class="{
                                        'is-invalid':
                                            submitted && errors.has('numero r'),
                                    }"
                                    placeholder="Numero de resolucion"
                                    v-model.number="form.numero_resolucion"
                                    name="numero r"
                                />
                                <div
                                    v-if="submitted && errors.has('numero r')"
                                    class="invalid-feedback"
                                >
                                    {{ errors.first("numero r") }}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for>Prefijo</label>
                                <input
                                    type="text"
                                    v-validate="'required|max:4|min:1'"
                                    class="form-control form-control-sm"
                                    :class="{
                                        'is-invalid':
                                            submitted && errors.has('prefijo'),
                                    }"
                                    placeholder="prefijo"
                                    v-model.number="form.prefijo"
                                    name="prefijo"
                                />
                                <div
                                    v-if="submitted && errors.has('prefijo')"
                                    class="invalid-feedback"
                                >
                                    {{ errors.first("prefijo") }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <label>F inicio</label>
                            <div class="input-group">
                                <input
                                    class="form-control form-control-sm"
                                    type="date"
                                    v-model="form.fecha_inicio"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <label>F fin</label>
                            <div class="input-group">
                                <input
                                    class="form-control form-control-sm"
                                    type="date"
                                    v-model="form.fecha_fin"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for>Numero inicial</label>
                                <input
                                    type="number"
                                    v-validate="'required|numeric|min:1'"
                                    class="form-control form-control-sm"
                                    :class="{
                                        'is-invalid':
                                            submitted && errors.has('numero i'),
                                    }"
                                    placeholder="Numero inicial"
                                    v-model.number="form.numero_inicial"
                                    name="numero i"
                                />
                                <div
                                    v-if="submitted && errors.has('numero i')"
                                    class="invalid-feedback"
                                >
                                    {{ errors.first("numero i") }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for>Numero final</label>
                                <input
                                    type="number"
                                    v-validate="'required|numeric|min:1'"
                                    class="form-control form-control-sm"
                                    :class="{
                                        'is-invalid':
                                            submitted && errors.has('numero f'),
                                    }"
                                    placeholder="Numero final"
                                    v-model.number="form.numero_final"
                                    name="numero f"
                                />
                                <div
                                    v-if="submitted && errors.has('numero f')"
                                    class="invalid-feedback"
                                >
                                    {{ errors.first("numero f") }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for>Numero actual</label>
                                <input
                                    type="number"
                                    v-validate="'required|numeric|min:1'"
                                    class="form-control form-control-sm"
                                    :class="{
                                        'is-invalid':
                                            submitted && errors.has('numero a'),
                                    }"
                                    placeholder="Numero actual"
                                    v-model.number="form.numero_actual"
                                    name="numero a"
                                />
                                <div
                                    v-if="submitted && errors.has('numero a')"
                                    class="invalid-feedback"
                                >
                                    {{ errors.first("numero a") }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for>Entorno</label>

                                <select
                                    v-model="form.isint"
                                    class="form-control form-control-sm"
                                    required
                                >
                                    <option value="0">R/ Local</option>
                                    <option value="1">R/ Producción</option>
                                </select>
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
                            'btn btn-outline-primary ': !this.form.id,
                            'btn btn-outline-danger ': this.form.id,
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
            </section>
        </Modal-Resource>
    </div>
</template>
<script>
import { mapState } from "vuex";
import ModalResource from "../utilities/modal.vue";
import add from "../../mixins/add";

export default {
    $_veeValidate: {
        validator: "new",
    },
    name: "add",
    components: {
        ModalResource,
    },

    data() {
        return {
            actions: "Resolutionsactions",
            submitted: true,
            send: true,
            form: {
                id: null,
                numero_resolucion: 1,
                prefijo: "",
                fecha_inicio: "",
                fecha_fin: "",
                numero_incial: 1,
                numero_final: 0,
                numero_actual: 0,
                isint: 1,
            },
        };
    },
    mixins: [add],

    computed: {
        ...mapState(["urlresolutions"]),
    },
    methods: {
        show(row) {
            (this.form.numero_resolucion = row.numero_resolucion),
                (this.form.prefijo = row.prefijo),
                (this.form.fecha_inicio = row.fecha_inicio),
                (this.form.fecha_fin = row.fecha_fin),
                (this.form.numero_inicial = row.numero_inicial),
                (this.form.numero_final = row.numero_final),
                (this.form.numero_actual = row.numero_actual),
                (this.form.isint = row.isint),
                (this.form.id = row.id);

            $("#model").modal("show");
            this.send = true;
        },

        clear() {
            this.form.id = null;
            this.form.numero_resolucion = 1;
            (this.form.prefijo = ""),
                (this.form.fecha_inicio = ""),
                (this.form.fecha_fin = ""),
                (this.form.numero_incial = 1),
                (this.form.numero_final = 0),
                (this.form.numero_actual = 0),
                (this.form.isint = 0),
                (this.form.id = null);

            this.$validator.reset();
            this.send = true;
            this.clientot.length = "";
        },
    },
};
</script>
