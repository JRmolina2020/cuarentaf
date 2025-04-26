<template>
    <div>
        <div class="row">
            <div class="col-lg-6 col-12">
                <canvas id="salesChart"></canvas>
            </div>
            <div class="col-lg-6 col-12">
                <canvas id="weeklySalesChart"></canvas>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-12">
                <h2>Top 10 Productos más Rentables</h2>
                <canvas id="profitChart"></canvas>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from "vuex";
import Chart from "chart.js";
import jsPDF from "jspdf";
import html2canvas from "html2canvas";

export default {
    data() {
        return {
            monthlyChart: null,
            weeklyChart: null,
            profitableChart: null,
            salesData: [],
            weeklySalesData: [],
            topProfitableProductsData: [],
            months: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
            ],
        };
    },

    methods: {
        async fetchWeeklySales() {
            try {
                const response = await axios.get("api/sales/weekly");
                this.weeklySalesData = response.data;

                this.renderWeeklyChart();
            } catch (error) {
                console.error("Error al obtener las ventas semanales:", error);
            }
        },

        renderWeeklyChart() {
            if (this.weeklyChart) {
                this.weeklyChart.destroy();
                this.weeklyChart = null;
            }

            if (this.weeklySalesData.length === 0) return;

            const ctx = document
                .getElementById("weeklySalesChart")
                .getContext("2d");

            // Calcular la semana actual
            const currentWeek = this.getCurrentWeekOfMonth();

            this.weeklyChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: this.weeklySalesData.map(
                        (item) => `Semana ${item.week}`
                    ),
                    datasets: [
                        {
                            label: "Ventas por Semana",
                            data: this.weeklySalesData.map(
                                (item) => item.total_sales
                            ),
                            backgroundColor: "rgba(75, 192, 192, 0.6)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 2,
                            fill: false,
                            pointBackgroundColor: this.weeklySalesData.map(
                                (item) =>
                                    item.week === currentWeek
                                        ? "red" // Resaltar la semana actual
                                        : "rgba(75, 192, 192, 1)"
                            ),
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: true,
                                    callback: function (value) {
                                        return `$${value.toLocaleString()}`;
                                    },
                                },
                            },
                        ],
                    },
                },
            });
        },

        // Función para calcular la semana actual del mes
        getCurrentWeekOfMonth() {
            const today = new Date();
            return Math.ceil(today.getDate() / 7);
        },

        async fetchSalesData() {
            try {
                const response = await axios.get("api/sales/monthly");
                this.salesData = response.data;

                this.renderMonthlyChart();
            } catch (error) {
                console.error("Error al obtener las ventas:", error);
            }
        },

        renderMonthlyChart() {
            if (this.monthlyChart) {
                this.monthlyChart.destroy();
                this.monthlyChart = null;
            }

            if (this.salesData.length === 0) return;

            const ctx = document.getElementById("salesChart").getContext("2d");
            this.monthlyChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: this.salesData.map(
                        (item) => this.months[item.month - 1]
                    ),
                    datasets: [
                        {
                            label: "Ventas por mes",
                            data: this.salesData.map(
                                (item) => item.total_sales
                            ),
                            backgroundColor: "rgba(54, 162, 235, 0.6)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: true,
                                    // Formatear el valor como moneda con separador de miles
                                    callback: function (value) {
                                        return `$${value.toLocaleString()}`;
                                    },
                                },
                            },
                        ],
                    },
                },
            });
        },

        async fetchTopProducts() {
            try {
                const response = await axios.get("api/sales/profitable");
                this.topProfitableProductsData = response.data;

                this.renderProfitableChart();
            } catch (error) {
                console.error(
                    "Error al obtener los productos más rentables:",
                    error
                );
            }
        },

        renderProfitableChart() {
            if (this.profitableChart) {
                this.profitableChart.destroy();
                this.profitableChart = null;
            }

            if (
                !this.topProfitableProductsData ||
                this.topProfitableProductsData.length === 0
            ) {
                console.warn("No hay productos para graficar.");
                return;
            }

            const ctx = document.getElementById("profitChart").getContext("2d");

            // Convertimos los valores a número para evitar problemas
            const labels = this.topProfitableProductsData.map(
                (p) => p.name || "Producto sin nombre"
            );
            const data = this.topProfitableProductsData.map(
                (p) => Number(p.total_profit) || 0
            );

            const staticColors = [
                "rgba(255, 99, 132, 0.6)", // Color 1
                "rgba(54, 162, 235, 0.6)", // Color 2
                "rgba(255, 206, 86, 0.6)", // Color 3
                "rgba(75, 192, 192, 0.6)", // Color 4
                "rgba(153, 102, 255, 0.6)", // Color 5
                "rgba(255, 159, 64, 0.6)", // Color 6
                "rgba(199, 199, 199, 0.6)", // Color 7
                "rgba(83, 102, 255, 0.6)", // Color 8
                "rgba(255, 105, 180, 0.6)", // Color 9
                "rgba(255, 206, 203, 0.6)", // Color 10
            ];

            const backgroundColors = staticColors.slice(0, labels.length);

            // Para el borde, podemos usar la misma lista pero con opacidad 1
            const borderColors = backgroundColors.map((color) =>
                color.replace("0.6", "1")
            );

            this.profitableChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels,
                    datasets: [
                        {
                            label: "Ganancia Total",
                            data,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: true,
                                    min: 0,
                                    // Formatear el valor como moneda con separador de miles
                                    callback: function (value) {
                                        return `$${value.toLocaleString()}`;
                                    },
                                },
                            },
                        ],
                    },
                },
            });
        },
    },

    mounted() {
        this.fetchSalesData();
        this.fetchWeeklySales();
        this.fetchTopProducts();
    },
};
</script>
