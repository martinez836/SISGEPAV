import {
    Chart,
    ArcElement,
    Tooltip,
    Legend,
    Title,
    BarElement,
    CategoryScale,
    LinearScale,
    PieController,
    BarController
} from 'chart.js';

// ¡Registra los controladores también!
Chart.register(
    ArcElement,
    Tooltip,
    Legend,
    Title,
    BarElement,
    CategoryScale,
    LinearScale,
    PieController,
    BarController
);

let productionChartInstance = null;
let classificationChartInstance = null;

document.addEventListener('DOMContentLoaded', () => {

    async function LoadClassificationChart() {
        try {
            const response = await fetch('/classification-by-month');
            if (!response.ok) throw new Error('Error en la petición');
            const data = await response.json();

            const chartContainer = document.querySelector('#categoryChart');
            chartContainer.innerHTML = '<canvas id="classificationChart" class="w-full h-64"></canvas>';
            const ctx = document.querySelector('#classificationChart').getContext('2d');

            classificationChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Clasificación de huevos (mes actual)',
                        data: data.data,
                        backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#F44336', '#9C27B0'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        title: {
                            display: true,
                            text: 'Clasificación de Huevos - Mes Actual'
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error cargando gráfica:', error);
        }
    }

    async function LoadProductionByMonth() {
        try {
            const response = await fetch('/production-by-month');
            if (!response.ok) throw new Error('Error en la petición');
            const data = await response.json();

            const chartContainer = document.querySelector('#productionChart');
            chartContainer.innerHTML = '<canvas id="monthChart" class="w-full h-64"></canvas>';
            const ctx = document.querySelector('#monthChart').getContext('2d');

            productionChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Huevos recolectados por mes',
                        data: data.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: {
                            display: true,
                            text: 'Producción Mensual de Huevos'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad total'
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error cargando gráfica:', error);
        }
    }

    // Ejecutar al cargar
    LoadClassificationChart();
    LoadProductionByMonth();

    // Descargar imágenes
    document.querySelector('#downloadProductionChart').addEventListener('click', () => {
        if (productionChartInstance) {
            const a = document.createElement('a');
            a.href = productionChartInstance.toBase64Image();
            a.download = 'produccion_mensual.png';
            a.click();
        }
    });

    document.querySelector('#downloadCategoryChart').addEventListener('click', () => {
        if (classificationChartInstance) {
            const a = document.createElement('a');
            a.href = classificationChartInstance.toBase64Image();
            a.download = 'clasificacion_mensual.png';
            a.click();
        }
    });
});
