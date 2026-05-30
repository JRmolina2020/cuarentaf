<template>
    <div>
        <div v-if="viewfac">
            <div v-if="status">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input
                                class="form-control form-control-sm"
                                type="date"
                                v-model="date"
                                placeholder=".form-control-sm"
                            />

                            <div class="input-group-append">
                                <button
                                    class="btn btn-outline-secondary btn-sm"
                                    @click="getDate()"
                                    type="button"
                                >
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <select
                                v-model="search_sale"
                                class="form-control form-control-sm"
                                required
                            >
                                <option
                                    v-for="(item, index) in moneySingle"
                                    :key="index"
                                    :value="item.name"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                            <div class="input-group-append">
                                <button
                                    class="btn btn-success btn-sm"
                                    @click="getTypeSale()"
                                    type="button"
                                >
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <VTable
                        :data="factures"
                        :page-size="5"
                        :currentPage.sync="currentPage"
                        @totalPagesChanged="totalPages = $event"
                        class="table table-striped table-borderless mt-3"
                    >
                        <template #head>
                            <tr style="color: #fff; background: black">
                                <th>#</th>
                                <th>Prefijo</th>
                                <th>Total</th>
                                <th>E</th>
                                <th>O</th>
                                <th>Banco</th>
                                <th>Vendedor</th>
                                <th>Estado</th>
                                <th>POS</th>
                                <th></th>
                                <th></th>
                                <th>E</th>
                            </tr>
                        </template>

                        <template #body="{ rows }">
                            <tr
                                v-for="row in rows"
                                :key="row.id"
                                :class="{ 'table-danger': row.canceled }"
                            >
                                <td v-if="row.numbering_range_id == 1">
                                    {{ row.id }}
                                </td>
                                <td v-else class="bg-dark text-white">
                                    {{ row.id }}
                                </td>

                                <td v-if="row.numberf">
                                    {{ row.numberf }}

                                    <span
                                        v-if="row.canceled"
                                        class="badge bg-danger"
                                    >
                                        Fac Anulada
                                    </span>
                                </td>
                                <td v-else>
                                    <span class="badge bg-primary">
                                        interno
                                    </span>
                                </td>
                                <td>${{ row.tot | currency }}</td>
                                <td>${{ row.efecty | currency }}</td>
                                <td>${{ row.other | currency }}</td>
                                <td v-if="row.type_sale == 1">
                                    <i class="fi fi-dollar"></i>
                                </td>
                                <td v-else>{{ row.type_sale }}</td>
                                <th>{{ row.name }}</th>
                                <td>
                                    <span class="badge badge-success"
                                        >Pagado</span
                                    >
                                </td>

                                <td>
                                    <div
                                        class="btn-group btn-group-sm"
                                        role="group"
                                    >
                                        <!-- Botón: Ver ticket (modal) -->
                                        <Modal-Ticket :cod="row.id" />

                                        <!-- Botón: WhatsApp, solo si hay teléfono -->
                                        <button
                                            v-if="row.phonef && row.phonef != 0"
                                            class="btn btn-outline-success"
                                            type="button"
                                            @click="Wppfacture(row.id)"
                                            title="Enviar por WhatsApp"
                                        >
                                            <i class="fi fi-phone"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        @click="mostrarAlerta(row)"
                                        class="btn bg-black btn-sm"
                                    >
                                        <i class="fi fi-eye"></i>
                                    </button>
                                </td>
                                <td>
                                    <button
                                        v-can="'electronica'"
                                        v-if="!row.numberf"
                                        type="button"
                                        @click="sendfac(row)"
                                        class="btn bg-success btn-sm"
                                    >
                                        <i class="fi fi-check"></i>
                                    </button>
                                    <button
                                        v-if="row.numberf && !row.canceled"
                                        v-can="'electronica'"
                                        type="button"
                                        @click="sendNote(row)"
                                        class="btn bg-danger btn-sm"
                                    >
                                        <i class="fi fi-export"></i>
                                    </button>
                                </td>

                                <td
                                    v-if="row.numbering_range_id == 1"
                                    v-can="'eliminar factura'"
                                >
                                    <button
                                        type="button"
                                        @click="destroy(row.id)"
                                        class="btn bg-danger btn-sm"
                                    >
                                        <i class="fi fi-trash"></i>
                                    </button>
                                </td>
                                <td v-else></td>
                            </tr>
                        </template>
                    </VTable>
                    <div class="text-xs-center">
                        <VTPagination
                            :currentPage.sync="currentPage"
                            :total-pages="totalPages"
                            :boundary-links="true"
                            :maxPageLinks="4"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Monto</td>
                                    <td>Efectivo</td>
                                    <td>Otros</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr
                                    v-for="(item, index) in typeSale"
                                    :key="index"
                                >
                                    <th>{{ item.tot | currency }}</th>
                                    <th>{{ item.efecty | currency }}</th>
                                    <th>{{ item.other | currency }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-lg-2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Gasto total</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr
                                    v-for="(item, index) in billstot"
                                    :key="index"
                                >
                                    <th>{{ item.price | currency }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-lg-2">
                        <table class="table table-bordered table-dark">
                            <tbody>
                                <tr>
                                    <td>Banco total</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr
                                    v-for="(item, index) in typeSale_one"
                                    :key="index"
                                >
                                    <th>{{ item.other | currency }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-lg-2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Cierre del dia</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>
                                        <button
                                            @click="SendW()"
                                            type="button"
                                            class="btn btn-primary btn-xs px-2 py-1 shadow-sm font-weight-bold"
                                        >
                                            Generar cierre
                                        </button>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div v-if="factures.length == 0">
                    <div class="alert alert-danger" role="alert">
                        No existe información de venta para este dia
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <ModalFac :cod="cod"></ModalFac>
        </div>
        <div v-if="showAlerta" class="alerta-flotante">
            <button class="cerrar-alerta" @click="showAlerta = false">×</button>
            <div v-for="item in details" :key="item.id">
                {{ item.name }} - {{ item.quantity }} x {{ item.price }}
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from "vuex";
import ModalTicket from "../utilities/modalticket";
import VueHtmlToPaper from "vue-html-to-paper";

const options = {
    name: "_blank",
    specs: ["fullscreen=yes", "titlebar=yes", "scrollbars=yes"],
    styles: ["https://unpkg.com/bootstrap/dist/css/bootstrap.min.css"],

    timeout: 1000,
    autoClose: true,
    windowTitle: "Vue Html To Paper - Vue mixin for html elements printing.",
};

Vue.use(VueHtmlToPaper, options);
import MgetList from "../../mixins/dateFacture";

export default {
    data() {
        return {
            cod: 0,
            viewfac: 1,
            date: "",
            totalPages: 1,
            currentPage: 1,
            search_sale: "",
            showAlerta: false,
            alertaMensaje: "",
        };
    },
    mixins: [MgetList],
    components: {
        ModalTicket,
    },
    computed: {
        ...mapState([
            "viewfx",
            "factures",
            "typeSale",
            "typeSale_one",
            "status",
            "urlfactures",
            "status",
            "billstot",
            "moneySingle",
            "products",
            "company",
            "facUnique",
            "details",
        ]),
    },

    created() {
        this.getList();
    },
    methods: {
        getDate() {
            if (this.date == "") {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Te falta digitar la fecha!",
                });
            } else {
                let date = this.date;
                this.$store.dispatch("Factureactions", date);
                this.$store.dispatch("TypeSaleactions", date);
                this.$store.dispatch("Billtotactions", date);
                this.$store.dispatch("MoneySigleactions");
            }
        },
        getTypeSale() {
            if (this.date == "") {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Te falta digitar la fecha!",
                });
            } else {
                let obj = {
                    prop1: this.date,
                    prop2: this.search_sale,
                };
                this.$store.dispatch("TypeSale_one_actions", obj);
            }
        },

        async destroy(id) {
            let url = this.urlfactures + "/" + id;
            let response = await axios.delete(url);
            try {
                this.getList();
                Swal.fire({
                    title: `${response.data.message}`,
                    icon: "success",
                });
                this.$store.dispatch("Productactions");
            } catch (error) {
                console.log(error);
            }
        },

        viewTabledetail(id) {
            this.viewfac = 0;
            this.cod = id;
            this.$store.dispatch("viewactions");
        },
        SendW() {
            //format
            const formatoPesos = new Intl.NumberFormat("es-CO", {
                style: "currency",
                currency: "COP",
                minimumFractionDigits: 0, // En Colombia, los pesos suelen mostrarse sin decimales
            });
            //end

            let tot = formatoPesos.format(this.typeSale[0].tot);
            let other = formatoPesos.format(this.typeSale[0].other);
            let efecty = formatoPesos.format(this.typeSale[0].efecty);
            const numero = "57" + this.company[0].phone; // Número en formato internacional sin '+'

            const mensaje = encodeURIComponent(
                "*Hola!* \n\nEspero que estés teniendo un gran día. \n *Quería mostrarte el cierre del dia:* \n *TOTAL VENTA:*" +
                    tot +
                    "\n*EFECTIVO:*" +
                    efecty +
                    "\n *OTROS:*" +
                    other +
                    ""
            );

            const url = `https://wa.me/${numero}?text=${mensaje}`;
            window.open(url, "_blank");
        },
        Wppfacture(id) {
            axios.get("/api/factureUnique/" + id).then((response) => {
                let [{ sub, tot, phonef, date_facture }] = response.data;
                axios.get("/api/details/" + id).then((detailsResponse) => {
                    const detalles = detailsResponse.data;
                    let lineas = "\n\n* Detalles de la compra:*\n";
                    detalles.forEach((item, index) => {
                        lineas += `\n${index + 1}. ${item.quantity} x ${
                            item.name
                        } - $${item.price} = $${item.tot}`;
                    });

                    // Armar el mensaje completo
                    const mensaje = encodeURIComponent(
                        "*Hola!* \n\nEspero que estés teniendo un gran día. \n\n* Aquí está tu factura:*" +
                            "\n\n*TOTAL:* $" +
                            tot +
                            "\n*SUBTOTAL:* $" +
                            sub +
                            "\n*FECHA:* " +
                            date_facture +
                            lineas +
                            "\n\nGracias por tu compra."
                    );

                    const numero = "57" + phonef;
                    const url = `https://wa.me/${numero}?text=${mensaje}`;
                    window.open(url, "_blank");
                });
            });
        },
        mostrarAlerta(row) {
            this.showAlerta = true;
            this.$store.dispatch("FactureDetailactions", row.id);
        },
        async sendNote(row) {
            const result = await Swal.fire({
                title: "¿Enviar nota crédito a la DIAN?",
                html: `<p><strong>Total:</strong> $${parseFloat(
                    row.tot
                ).toLocaleString()}</p>`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, enviar",
                cancelButtonText: "Cancelar",
            });

            if (result.isConfirmed) {
                Swal.fire({
                    title: "Enviando nota crédito...",
                    text: "Por favor espera",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                try {
                    const response = await axios.post("/notes", {
                        id: row.id,
                    });

                    this.getList();

                    Swal.fire({
                        title: response.data.message || "Nota crédito enviada",
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                } catch (error) {
                    Swal.fire({
                        title: "Error al enviar",
                        text:
                            error.response?.data?.message ||
                            "Ocurrió un error inesperado.",
                        icon: "error",
                    });
                }
            }
        },
        async sendfac(row) {
            const result = await Swal.fire({
                title: "¿Enviar factura a la DIAN?",
                html: `<p><strong>Total:</strong> $${parseFloat(
                    row.tot
                ).toLocaleString()}</p>`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, enviar",
                cancelButtonText: "Cancelar",
            });

            if (result.isConfirmed) {
                // Mostrar loader mientras se procesa
                Swal.fire({
                    title: "Enviando factura...",
                    text: "Por favor espera",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                try {
                    const response = await axios.post("/invoices", {
                        id: row.id,
                    });

                    this.getList();

                    Swal.fire({
                        title: response.data.message || "Factura enviada",
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                } catch (error) {
                    Swal.fire({
                        title: "Error al enviar",
                        text:
                            error.response?.data?.message ||
                            "Ocurrió un error inesperado.",
                        icon: "error",
                    });
                }
            }
        },
        async sendEmail() {
            try {
                const res = await this.$axios.post("/sendemail", {
                    id: 60,
                });

                console.log(res.data);
            } catch (error) {
                console.error(error.response?.data || error.message);
            }
        },
    },
};
</script>
