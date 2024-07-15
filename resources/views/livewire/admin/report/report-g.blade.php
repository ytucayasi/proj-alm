<div class="p-6">
  <button wire:click="toggleView" class="mb-4 rounded bg-blue-500 px-4 py-2 font-bold text-white hover:bg-blue-700">
    Cambiar
  </button>

  @if ($viewType === 'chart')
    <div class="flex justify-between space-x-4">
      <div class="w-1/2 h-64">
        <canvas id="companyChart" class="w-full h-full"></canvas>
      </div>
      <div class="w-1/2 h-64">
        <canvas id="areaChart" class="w-full h-full"></canvas>
      </div>
    </div>
  @else
    <div id="chartInfo" class="h-64 w-full rounded-lg bg-white p-4 shadow-md">
      @foreach ($data as $item)
        <p class="text-gray-700">{{ $item['company_name'] }}: {{ $item['total_reservations'] }}</p>
      @endforeach
      @foreach ($areaData as $item)
        <p class="text-gray-700">{{ $item['area_name'] }}: {{ $item['total_reservations'] }}</p>
      @endforeach
    </div>
  @endif
</div>

{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script>
  let companyChartInstance;
  let areaChartInstance;

  function initializeCompanyChart(data) {
    const chartElement = document.getElementById('companyChart');
    if (!chartElement) {
      console.error('Canvas element not found');
      return;
    }
    const ctx = chartElement.getContext('2d');

    const labels = data.map(item => item.company_name);
    const reservations = data.map(item => item.total_reservations);

    const backgroundColors = [
      'rgba(255, 99, 132, 0.2)',
      'rgba(54, 162, 235, 0.2)',
      'rgba(255, 206, 86, 0.2)',
      'rgba(75, 192, 192, 0.2)',
      'rgba(153, 102, 255, 0.2)',
      'rgba(255, 159, 64, 0.2)'
    ];

    const borderColors = [
      'rgba(255, 99, 132, 1)',
      'rgba(54, 162, 235, 1)',
      'rgba(255, 206, 86, 1)',
      'rgba(75, 192, 192, 1)',
      'rgba(153, 102, 255, 1)',
      'rgba(255, 159, 64, 1)'
    ];

    if (companyChartInstance) {
      companyChartInstance.destroy();
    }

    companyChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Reservaciones',
          data: reservations,
          backgroundColor: labels.map((_, index) => backgroundColors[index % backgroundColors.length]),
          borderColor: labels.map((_, index) => borderColors[index % borderColors.length]),
          borderWidth: 1
        }]
      },
      options: {
        responsive: false,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            onClick: function(e, legendItem, legend) {
              const index = legendItem.index;
              legend.chart.toggleDataVisibility(index);
              legend.chart.update();
            },
            labels: {
              generateLabels: function(chart) {
                const datasets = chart.data.datasets[0];
                return chart.data.labels.map((label, index) => ({
                  text: label,
                  fillStyle: datasets.backgroundColor[index],
                  hidden: chart.getDatasetMeta(0).data[index].hidden,
                  strokeStyle: datasets.borderColor[index],
                  lineWidth: 1,
                  index: index
                }));
              }
            }
          },
          title: {
            display: true,
            text: 'Reservaciones por Compañía'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

  function initializeAreaChart(data) {
    const chartElement = document.getElementById('areaChart');
    if (!chartElement) {
      console.error('Canvas element not found');
      return;
    }
    const ctx = chartElement.getContext('2d');

    const labels = data.map(item => item.area_name);
    const reservations = data.map(item => item.total_reservations);

    const backgroundColors = [
      'rgba(255, 99, 132, 0.2)',
      'rgba(54, 162, 235, 0.2)',
      'rgba(255, 206, 86, 0.2)',
      'rgba(75, 192, 192, 0.2)',
      'rgba(153, 102, 255, 0.2)',
      'rgba(255, 159, 64, 0.2)'
    ];

    const borderColors = [
      'rgba(255, 99, 132, 1)',
      'rgba(54, 162, 235, 1)',
      'rgba(255, 206, 86, 1)',
      'rgba(75, 192, 192, 1)',
      'rgba(153, 102, 255, 1)',
      'rgba(255, 159, 64, 1)'
    ];

    if (areaChartInstance) {
      areaChartInstance.destroy();
    }

    areaChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Reservaciones',
          data: reservations,
          backgroundColor: labels.map((_, index) => backgroundColors[index % backgroundColors.length]),
          borderColor: labels.map((_, index) => borderColors[index % borderColors.length]),
          borderWidth: 1
        }]
      },
      options: {
        responsive: false,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            onClick: function(e, legendItem, legend) {
              const index = legendItem.index;
              legend.chart.toggleDataVisibility(index);
              legend.chart.update();
            },
            labels: {
              generateLabels: function(chart) {
                const datasets = chart.data.datasets[0];
                return chart.data.labels.map((label, index) => ({
                  text: label,
                  fillStyle: datasets.backgroundColor[index],
                  hidden: chart.getDatasetMeta(0).data[index].hidden,
                  strokeStyle: datasets.borderColor[index],
                  lineWidth: 1,
                  index: index
                }));
              }
            }
          },
          title: {
            display: true,
            text: 'Reservaciones por Área'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

  document.addEventListener('livewire:init', () => {
    Livewire.on('chart-show', event => {
      const data = event.data;
      const observer = new MutationObserver((mutations, obs) => {
        const chartElement = document.getElementById('companyChart');
        if (chartElement) {
          initializeCompanyChart(data);
          obs.disconnect();
        }
      });

      observer.observe(document.body, {
        childList: true,
        subtree: true
      });
    });

    Livewire.on('area-chart-show', event => {
      const data = event.data;
      const observer = new MutationObserver((mutations, obs) => {
        const chartElement = document.getElementById('areaChart');
        if (chartElement) {
          initializeAreaChart(data);
          obs.disconnect();
        }
      });

      observer.observe(document.body, {
        childList: true,
        subtree: true
      });
    });

    // Inicializar el gráfico en la carga inicial
    const initialData = @json($data);
    const initialAreaData = @json($areaData);
    initializeCompanyChart(initialData);
    initializeAreaChart(initialAreaData);
  });
</script>
