<template>
    <div class="container my-4" style="max-width: 800px">
        <!-- Título principal -->
        <div class="text-center mb-4">
            <h4 class="font-weight-bold">Factura</h4>
            <p class="text-muted mb-2">Resumen del cliente y productos</p>
        </div>

        <!-- Datos del cliente -->
        <div class="table-responsive mb-4">
            <table class="table table-sm table-borderless text-center">
                <thead class="thead-light">
                    <tr class="bg-light">
                        <th>#</th>
                        <th>NIT</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in facUnique" :key="'g' + index">
                        <td>{{ item.id }}</td>
                        <td>{{ item.nit }}</td>
                        <td>{{ item.fullname }}</td>
                        <td>{{ item.phone }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Productos -->
        <div class="table-responsive mb-4">
            <table class="table table-sm table-borderless text-center">
                <thead class="thead-light">
                    <tr class="bg-light">
                        <th>Artículo</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in details" :key="'d' + index">
                        <td>{{ item.name }}</td>
                        <td>${{ item.price | currency }}</td>
                        <td>{{ item.quantity }}</td>
                        <td>${{ item.sub | currency }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Nota -->
        <div
            v-for="item2 in facUnique"
            :key="item2.id"
            class="mb-4 text-center"
        >
            <div class="alert alert-light font-italic">
                {{ item2.note }}
            </div>
        </div>

        <!-- Totales -->
        <div class="table-responsive mb-5">
            <table class="table table-sm table-borderless text-center">
                <thead class="thead-light">
                    <tr class="bg-light">
                        <th>Subtotal</th>
                        <th>Descuento</th>
                        <th>Total</th>
                        <th>Cant. Productos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in facUnique" :key="'g2' + index">
                        <td>${{ item.sub | currency }}</td>
                        <td>${{ item.disc | currency }}</td>
                        <td>${{ item.tot | currency }}</td>
                        <td>{{ sumProducts }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { mapState } from "vuex";

import VueHtml2Canvas from "vue-html2canvas";
Vue.use(VueHtml2Canvas);

export default {
    name: "modaldetails",
    props: {
        cod: {
            type: Number,
            required: true,
        },
    },

    data() {
        return {
            output: null,
            currentPage: 1,
            totalPages: 0,
        };
    },
    mounted() {
        const path = window.location.pathname; // "/facture/38"
        const id = path.split("/").pop(); // obtiene "38"
        this.getlistProducts(id);
    },
    created() {},
    computed: {
        ...mapState(["details", "facUnique"]),
        sumProducts() {
            let tot = 0;
            this.details.map((data) => {
                tot = parseInt(tot) + parseInt(data.quantity);
            });
            return tot;
        },
    },
    methods: {
        print() {
            this.$htmlToPaper("facture2");
        },
        getlistProducts(id) {
            this.$store.dispatch("FacturepDetailactions", id);
            this.$store.dispatch("FacturepUniquections", id);
        },
    },
};
</script>
