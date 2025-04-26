<template>
    <form
        method="POST"
        @submit.prevent="login"
        autocomplete="off"
        class="card p-4 shadow-sm border-0 rounded-lg bg-white mx-auto mt-4"
        style="max-width: 380px"
    >
        <div class="text-center mb-3">
            <img
                src="/img/iconf.png"
                alt="Logo"
                class="img-fluid mb-2"
                style="width: 70px"
            />
            <h5 class="font-weight-bold text-dark mb-0">CUARENTAF</h5>
        </div>

        <!-- Email input -->
        <div class="form-group">
            <input
                type="email"
                v-model="form.email"
                name="email"
                placeholder="Correo electrónico"
                v-validate="'required|max:40|min:4'"
                class="form-control form-control-sm rounded-pill"
                :class="{ 'is-invalid': submitted && errors.has('email') }"
            />
            <div
                v-if="submitted && errors.has('email')"
                class="invalid-feedback d-block small mt-1"
            >
                {{ errors.first("email") }}
            </div>
        </div>

        <!-- Password input -->
        <div class="form-group">
            <input
                type="password"
                v-model="form.password"
                name="password"
                placeholder="Contraseña"
                v-validate="'required|max:15|min:3'"
                class="form-control form-control-sm rounded-pill"
                :class="{ 'is-invalid': submitted && errors.has('password') }"
            />
            <div
                v-if="submitted && errors.has('password')"
                class="invalid-feedback d-block small mt-1"
            >
                {{ errors.first("password") }}
            </div>
        </div>

        <!-- Submit button -->
        <div v-if="status" class="text-center mt-3">
            <button
                type="submit"
                class="btn btn-primary btn-sm btn-block rounded-pill"
                :disabled="errors.any()"
            >
                Ingresar
            </button>
        </div>

        <div v-else class="text-center mt-2">
            <div class="alert alert-success mb-0 py-1">Ingresando...</div>
        </div>
    </form>
</template>

<script>
export default {
    $_veeValidate: {
        validator: "new",
    },
    name: "login",
    data() {
        return {
            url: "login",
            submitted: true,
            status: true,
            form: {
                email: null,
                password: null,
            },
        };
    },
    methods: {
        login() {
            this.$validator.validate().then((valid) => {
                if (valid) {
                    axios
                        .post(this.url, this.form)
                        .then(() => {
                            window.location.replace("/facturas");
                            this.status = false;
                        })
                        .catch(() => {
                            this.form.password = null;
                            Swal.fire({
                                icon: "error",
                                title: "Oopss...",
                                text: "Verifique las credenciales de ingreso",
                            });
                        });
                }
            });
        },
    },
};
</script>
