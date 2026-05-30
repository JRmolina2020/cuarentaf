<template>
    <div>
        <Modal-Resource
            v-on:clear="clear"
            title="Registro de documentos"
            sone="modal-dialog modal-md"
        >
            <section slot="titlebutton">Registrar</section>

            <section v-if="!form.id" slot="title">Registro</section>
            <section v-else slot="title">Editar</section>
            <section slot="closebtn">
                <button
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
                    @submit.enter.prevent="add(form.id, actions, urldocuments)"
                    autocomplete="off"
                    onKeyPress="if(event.keyCode == 13) event.returnValue = false;"
                >
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for>Prefijo</label>
                                <input
                                    type="text"
                                    ref="name"
                                    v-validate="'required|max:5|min:1'"
                                    class="form-control form-control-sm"
                                    :class="{
                                        'is-invalid':
                                            submitted && errors.has('prefijo'),
                                    }"
                                    placeholder="prefijo"
                                    v-model="form.prefijo"
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
                            <div class="form-group">
                                <label for="">Tipo</label>
                                <select
                                    v-model="form.type"
                                    class="form-control form-control-sm"
                                    @click="valueFac()"
                                >
                                    <option value="Interna">Interna</option>
                                    <option value="Electronica">
                                        Electronica
                                    </option>
                                    <option value="Nc">Nota credito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for>Enlace</label>
                                <input
                                    type="number"
                                    v-validate="'required|numeric|max:3|min:1'"
                                    class="form-control form-control-sm"
                                    :class="{
                                        'is-invalid':
                                            submitted && errors.has('code'),
                                    }"
                                    placeholder="Codigo de enlace"
                                    v-model.number="form.code"
                                    name="code"
                                    @input="checkCode()"
                                />
                                <div
                                    v-if="submitted && errors.has('code')"
                                    class="invalid-feedback"
                                >
                                    {{ errors.first("code") }}
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
            actions: "Documentactions",
            submitted: true,
            send: true,
            form: {
                id: null,
                prefijo: "",
                code: "",
                type: "Interna",
                fac_int: 1, //si es interna 1
            },
        };
    },
    mixins: [add],

    computed: {
        ...mapState(["urldocuments"]),
    },
    methods: {
        show(row) {
            this.form.id = row.id;
            this.form.prefijo = row.prefijo;
            this.form.code = row.code;
            this.form.type = row.type;

            $("#model").modal("show");
            this.send = true;
        },

        clear() {
            this.form.id = null;
            this.form.prefijo = null;
            this.form.code = null;
            this.form.type = "Interna";
            this.form.fac_int = 1;
            this.$validator.reset();
            this.send = true;
        },
        async checkCode() {
            // Validar que el valor no sea null o undefined antes de usar trim()
            const code = this.form.code ? this.form.code.toString().trim() : "";

            if (code !== "") {
                try {
                    const response = await axios.get(
                        `/api/documents/check-code/${code}`
                    );

                    if (response.data.exists) {
                        console.log("existe");
                        Swal.fire({
                            icon: "warning",
                            title: "Código duplicado",
                            text: "Este código ya está registrado.",
                            width: "400px", // Ajusta el ancho de la alerta
                            padding: "1.5rem", // Ajusta el espaciado interno
                            customClass: {
                                popup: "swal-custom-popup", // Clase CSS personalizada
                                title: "swal-custom-title", // Clase CSS para el título
                                content: "swal-custom-text", // Clase CSS para el texto
                            },
                        });
                        this.send = false;
                    } else {
                        this.send = true;
                    }
                } catch (error) {
                    console.error("Error verificando el código", error);
                }
            }
        },
        valueFac() {
            if (this.form.type == "Interna") {
                this.form.fac_int = 1;
            } else {
                this.form.fac_int = 0;
            }
        },
    },
};
</script>
