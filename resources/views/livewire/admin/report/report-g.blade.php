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
        Reservas por Areas
      </div>
      <div style="width: 100%; height: 400px;">
        <canvas id="reservasAreas"></canvas>
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
  console.log(@json($dataByArea));
  var myChart2 = new Chart(ctx2, {
    type: 'pie',
    data: {
      labels: @json($dataByArea['labels']),
      datasets: [{
        label: 'Reservas por Areas',
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
</script>
