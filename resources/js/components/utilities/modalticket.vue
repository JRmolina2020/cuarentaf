wwww
<template>
    <div>
        <div>
            <!-- Button trigger modal -->
            <button
                type="button"
                class="btn bg-success btn-sm"
                data-toggle="modal"
                :data-target="'#model' + cod"
                @click="getlistProducts"
            >
                <i class="fi fi-file-1"></i>
            </button>

            <div
                class="modal fade"
                :id="'model' + cod"
                tabindex="-1"
                role="dialog"
                aria-labelledby="modelTitleId"
                aria-hidden="true"
            >
                <div class="modal-dialog" style="width: 300px" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div v-if="status_f">Cargando...</div>
                            <div v-else id="facture">
                                <!-- Company Information -->
                                <div>
                                    <center>
                                        <br /><br />
                                        <p
                                            style="font-size: 12px"
                                            v-for="(item, index) in company"
                                            :key="index"
                                        >
                                            <strong>
                                                {{ item.name }}<br />
                                                {{ item.nit }}<br />
                                                {{ item.representative }}<br />
                                                {{ item.direction }}<br />
                                                {{ item.phone }}<br />
                                                {{ item.city }}
                                            </strong>
                                        </p>
                                    </center>

                                    <!-- Facture Details -->

                                    <div
                                        v-for="(item, index) in facUnique"
                                        :key="'a' + index"
                                    >
                                        <p
                                            v-if="item.fac_int == 0"
                                            style="font-size: 14px"
                                        >
                                            <strong style="font-size: 12px"
                                                >factura de venta :
                                                {{ item.numberf }}</strong
                                            >
                                        </p>
                                        <p v-else>
                                            <strong style="font-size: 12px"
                                                >factura de venta : POSS-{{
                                                    item.id
                                                }}</strong
                                            >
                                        </p>
                                        <p style="font-size: 9px">
                                            Fecha:
                                            {{ item.created_at }}
                                        </p>
                                        <p style="font-size: 12px">
                                            {{ item.fullname }} | {{ item.nit }}
                                        </p>
                                        <p style="font-size: 9px">
                                            {{ item.note }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Products List -->
                                *******************<br />
                                <strong>ITEMS[{{ sumProducts }}]</strong>
                                <p
                                    v-for="(item, index) in details"
                                    :key="index"
                                >
                                    {{ item.quantity }}X {{ item.name }} *{{
                                        item.price | currency
                                    }}<br />
                                    ${{
                                        item.sub | currency
                                    }}....................<br />
                                </p>

                                <!-- Summary Section -->
                                <div
                                    v-for="(item, index) in facUnique"
                                    :key="'f' + index"
                                >
                                    <p style="font-size: 13px">
                                        <strong>Sub</strong> ${{
                                            item.sub | currency
                                        }}<br />
                                        <strong>Iva%</strong> $ 0.00<br />
                                        <strong>Desc</strong> ${{
                                            item.disc | currency
                                        }}<br />
                                        <strong>Tot</strong> ${{
                                            item.tot | currency
                                        }}
                                        <strong v-if="item.type_sale == 1"
                                            >Pago: Efectivo</strong
                                        >
                                        <strong v-else
                                            >Pago: {{ item.type_sale }}</strong
                                        >
                                    </p>
                                    <p>
                                        <strong style="font-size: 9px"
                                            >TOTAL EN LETRAS
                                            {{ numeroALetras(item.tot) }}
                                            PESOS</strong
                                        >
                                    </p>
                                    <div v-if="item.fac_int == 0">
                                        <p style="font-size: 10px">
                                            cufe: {{ item.cufe }}
                                        </p>
                                        <p style="font-size: 8px">
                                            Hora de validación:
                                            {{ item.validated_at }}
                                        </p>
                                        <div
                                            class="d-flex justify-content-center"
                                        >
                                            <qrcode
                                                :text="sublink + item.cufe"
                                                :size="70"
                                                level="H"
                                            />
                                        </div>
                                    </div>
                                    <div v-if="item.fac_int == 1">
                                        <div
                                            class="d-flex justify-content-center"
                                        >
                                            <qrcode
                                                :text="linkfacture"
                                                :size="70"
                                                level="H"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <center>
                                    <p
                                        style="font-size: 8px"
                                        v-for="(item, index) in company"
                                        :key="index"
                                    >
                                        {{ item.note }}
                                    </p>
                                </center>
                            </div>

                            <!-- Print Button -->
                            <button @click="print('facture')">Imprimir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from "vuex";
import Qrcode from "vue-qrcode-component";
import numeroALetras from "numero-a-letras";

export default {
    components: {
        Qrcode,
    },
    props: {
        cod: {
            type: Number,
            required: true,
        },
        facture: Object, // Recibe la factura como parámetro
    },

    data() {
        return {
            sublink:
                "https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=",
            linkfacture: "",
        };
    },
    created() {
        this.linkfacture = `${window.location.origin}/facture/${this.cod}`;
    },

    computed: {
        ...mapState(["details", "facUnique", "company", "status_f"]),
        sumProducts() {
            let tot = 0;
            this.details.map((data) => {
                tot = parseInt(tot) + parseInt(data.quantity);
            });
            return tot;
        },
    },
    methods: {
        print(areaID) {
            setTimeout(function () {
                var printContent = document.getElementById(areaID);
                var WinPrint = window.open("", "", "width=500,height=750");
                WinPrint.document.write(printContent.innerHTML);
                WinPrint.document.close();
                WinPrint.focus();
                WinPrint.print();
            }, 300);
        },
        getlistProducts() {
            this.$store.dispatch("FactureDetailactions", this.cod);
            this.$store.dispatch("FactureUniquections", this.cod);
            this.$store.dispatch("Companyactions");
        },

        numeroALetras(numero) {
            const unidades = [
                "",
                "UNO",
                "DOS",
                "TRES",
                "CUATRO",
                "CINCO",
                "SEIS",
                "SIETE",
                "OCHO",
                "NUEVE",
            ];
            const especiales = [
                "DIEZ",
                "ONCE",
                "DOCE",
                "TRECE",
                "CATORCE",
                "QUINCE",
                "DIECISEIS",
                "DIECISIETE",
                "DIECIOCHO",
                "DIECINUEVE",
            ];
            const decenas = [
                "",
                "",
                "VEINTE",
                "TREINTA",
                "CUARENTA",
                "CINCUENTA",
                "SESENTA",
                "SETENTA",
                "OCHENTA",
                "NOVENTA",
            ];
            const centenas = [
                "",
                "CIENTO",
                "DOSCIENTOS",
                "TRESCIENTOS",
                "CUATROCIENTOS",
                "QUINIENTOS",
                "SEISCIENTOS",
                "SETECIENTOS",
                "OCHOCIENTOS",
                "NOVECIENTOS",
            ];

            if (numero === 0) return "CERO PESOS";

            let texto = "";

            if (numero >= 1000) {
                let miles = Math.floor(numero / 1000);
                texto +=
                    miles === 1 ? "MIL " : this.numeroALetras(miles) + " MIL ";
                numero %= 1000;
            }

            if (numero >= 100) {
                let cientos = Math.floor(numero / 100);
                texto += centenas[cientos] + " ";
                numero %= 100;
            }

            if (numero >= 10 && numero < 20) {
                texto += especiales[numero - 10] + " ";
                numero = 0;
            } else if (numero >= 20) {
                let dec = Math.floor(numero / 10);
                texto += decenas[dec] + (numero % 10 > 0 ? " Y " : " ");
                numero %= 10;
            }

            if (numero > 0) {
                texto += unidades[numero] + " ";
            }

            return texto.trim() + "";
        },
    },
};
//https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=144d96eab4365ab33bd4259018cae7f8c296d2feb64cb67ccf7725a9a6459d8fc0eeabd983bb1b7dcb07bcd79c8eb86e
</script>
<style type="text/css">
.qr-container {
    display: flex;
    justify-content: center; /* Centra horizontalmente */
    align-items: center; /* Centra verticalmente si el contenedor tiene altura */
    width: 100%; /* Opcional: Asegura que el div ocupe todo el ancho */
}
@media print {
    p.bodyText {
        font-family: georgia, serif;
        font-size: 14px;
        color: blue;
    }
    @page {
        margin: 2cm;
    }
}
@media screen {
    p.bodyText {
        font-family: georgia, serif;
        font-size: 14px;
        color: blue;
    }
}
</style>
