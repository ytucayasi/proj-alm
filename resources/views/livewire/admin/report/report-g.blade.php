<div>
  {{-- Encabezado --}}
  <x-slot name="header">
    {{ __('Reportes') }}
  </x-slot>
  <div>
    <div class="mb-4">
      <div class="mb-4">
        Reservas del Mes
      </div>
      <div style="width: 100%; height: 400px;">
        <canvas id="reservasMes"></canvas>
      </div>
    </div>
    <div class="mb-4">
      <div class="mb-4">
        Reservas por Áreas
      </div>
      <div style="width: 100%; height: 400px;" class="flex justify-center">
        <canvas id="reservasAreas"></canvas>
      </div>
    </div>
    <div class="mb-4">
      <div class="mb-4">
        Ganancias Diarias
      </div>
      <div style="width: 100%; height: 400px;">
        <canvas id="gananciasDiarias"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
  var ctx1 = document.getElementById('reservasMes').getContext('2d');
  var myChart1 = new Chart(ctx1, {
    type: 'line',
    data: {
      labels: @json($dataByDays['labels']),
      datasets: [{
        label: 'Reservas del Mes',
        data: @json($dataByDays['data']),
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          display: false // Ocultar la leyenda
        }
      }
    }
  });

  var ctx2 = document.getElementById('reservasAreas').getContext('2d');
  var myChart2 = new Chart(ctx2, {
    type: 'pie',
    data: {
      labels: @json($dataByArea['labels']),
      datasets: [{
        label: 'Reservas',
        data: @json($dataByArea['data']),
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: {
          display: true,
          position: 'right' // Mostrar la leyenda a la derecha
        }
      }
    }
  });

  var ctx3 = document.getElementById('gananciasDiarias').getContext('2d');

  // Datos desde el backend
  var dailyEarningsData = @json($dailyEarnings['earnings']);
  var dailyTotalCostData = @json($dailyEarnings['total_cost']);
  var dailyTotalPackData = @json($dailyEarnings['total_pack']);
  var labels = @json($dailyEarnings['labels']);

  // Colores
  var earningsColors = dailyEarningsData.map(value => value < 0 ? 'rgba(255, 99, 132, 0.2)' :
  'rgba(75, 192, 192, 0.2)');
  var earningsBorderColors = dailyEarningsData.map(value => value < 0 ? 'rgba(255, 99, 132, 1)' :
    'rgba(75, 192, 192, 1)');
  var totalCostColor = 'rgba(54, 162, 235, 0.2)';
  var totalCostBorderColor = 'rgba(54, 162, 235, 1)';
  var totalPackColor = 'rgba(255, 206, 86, 0.2)';
  var totalPackBorderColor = 'rgba(255, 206, 86, 1)';

  var myChart3 = new Chart(ctx3, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
          label: 'Ganancias Diarias',
          data: dailyEarningsData,
          backgroundColor: earningsColors,
          borderColor: earningsBorderColors,
          borderWidth: 1,
          fill: false,
          pointBackgroundColor: earningsBorderColors,
          pointBorderColor: earningsBorderColors
        },
        {
          label: 'Costo Total',
          data: dailyTotalCostData,
          backgroundColor: totalCostColor,
          borderColor: totalCostBorderColor,
          borderWidth: 1,
          fill: false,
          pointBackgroundColor: totalCostBorderColor,
          pointBorderColor: totalCostBorderColor
        },
        {
          label: 'Pago Total',
          data: dailyTotalPackData,
          backgroundColor: totalPackColor,
          borderColor: totalPackBorderColor,
          borderWidth: 1,
          fill: false,
          pointBackgroundColor: totalPackBorderColor,
          pointBorderColor: totalPackBorderColor
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          display: true,
          position: 'top' // Mostrar la leyenda en la parte superior
        }
      }
    }
  });
</script>
