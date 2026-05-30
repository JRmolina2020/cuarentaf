<template>
    <div>
        <div class="text-center">
            <h3 class="profile-username text-center">{{ currentUser.name }}</h3>
            <p class="text-muted text-center">{{ currentUser.email }}</p>
        </div>
        <form
            method="POST"
            @submit.prevent="updatePassword(currentUser.id)"
            autocomplete="off"
            enctype="multipart/form-data"
        >
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for>password new</label>
                        <input
                            type="password"
                            v-validate="'required|max:15|min:3'"
                            class="form-control form-control-sm"
                            :class="{
                                'is-invalid':
                                    submitted && errors.has('password'),
                            }"
                            placeholder="nuevo password"
                            v-model="form.password"
                            name="password"
                            ref="password"
                        />
                        <div
                            v-if="submitted && errors.has('password')"
                            class="invalid-feedback"
                        >
                            {{ errors.first("password") }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for>password confirmation</label>
                        <input
                            type="password"
                            v-model="form.password_confirmed"
                            v-validate="'required|confirmed:password'"
                            data-vv-as="password"
                            class="form-control form-control-sm"
                            :class="{
                                'is-invalid':
                                    submitted &&
                                    errors.has('password_confirmation'),
                            }"
                            placeholder="confirmar password"
                            name="password_confirmation"
                        />
                        <div
                            v-if="
                                submitted && errors.has('password_confirmation')
                            "
                            class="invalid-feedback"
                        >
                            {{ errors.first("password_confirmation") }}
                        </div>
                    </div>
                </div>
            </div>
            <button
                :hidden="errors.any()"
                type="submit"
                class="btn btn-outline-primary"
            >
                <li class="fi fi-wink"></li>
            </button>
        </form>
        <div v-can="'electronica'">
            <span
                v-if="!loading"
                class="spinner-border spinner-border-sm mr-2"
            ></span>
            <table
                v-if="suscripcion"
                class="table table-striped table-bordered table-responsive-md"
            >
                <tbody>
                    <tr>
                        <th>Documentos Totales</th>
                        <td>{{ suscripcion.total_documents }}</td>
                    </tr>
                    <tr>
                        <th>Documentos Usados</th>
                        <td>{{ suscripcion.documents_used }}</td>
                    </tr>
                    <tr>
                        <th>Documentos Restantes</th>
                        <td>{{ suscripcion.documents_remaining }}</td>
                    </tr>
                    <tr>
                        <th>Días para Expirar</th>
                        <td>{{ suscripcion.subscription_days_to_expires }}</td>
                    </tr>
                    <tr>
                        <th>Fecha Inicio</th>
                        <td>
                            {{
                                formatDate(suscripcion.subscription_start_date)
                            }}
                        </td>
                    </tr>
                    <tr>
                        <th>Fecha Expiración</th>
                        <td>
                            {{
                                formatDate(
                                    suscripcion.subscription_expiration_date
                                )
                            }}
                        </td>
                    </tr>
                    <tr>
                        <th>Suscripción Expirada</th>
                        <td>
                            <span
                                :class="
                                    suscripcion.subscription_is_expired
                                        ? 'text-danger font-weight-bold'
                                        : 'text-success font-weight-bold'
                                "
                            >
                                {{
                                    suscripcion.subscription_is_expired
                                        ? "Sí"
                                        : "No"
                                }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
export default {
    $_veeValidate: {
        validator: "new",
    },

    data() {
        return {
            submitted: true,
            url: "/api/user/password/",
            suscripcion: null,
            error: null,
            loading: false,
            form: {
                password: null,
                password_confirmed: null,
            },
        };
    },
    created() {
        this.allsub();
    },
    methods: {
        async updatePassword(id) {
            let url = `${this.url}${id}`;
            let response = await axios.put(url, this.form);
            try {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: `${response.data.message}`,
                    showConfirmButton: false,
                    timer: 1500,
                });
                this.form.password = null;
                this.form.password_confirmed = null;
                this.$validator.reset();
            } catch (error) {
                console.log(error);
            }
        },
        allsub() {
            axios
                .get("api/suscripcion") // URL relativa a tu backend Laravel
                .then((response) => {
                    this.suscripcion = response.data.data;
                    this.error = null;
                })
                .catch((error) => {
                    this.error =
                        error.response?.data?.error ||
                        "Error al cargar la suscripción";
                    this.suscripcion = null;
                })
                .finally(() => {
                    this.loading = true;
                });
        },
        formatDate(dateStr) {
            if (!dateStr) return "";
            const options = { year: "numeric", month: "long", day: "numeric" };
            return new Date(dateStr).toLocaleDateString("es-CO", options);
        },
    },
};
</script>
