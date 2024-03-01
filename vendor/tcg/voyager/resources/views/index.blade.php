@extends('voyager::master')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card modern-chart-card">
            <div class="card-header">
                <h5 class="card-title">Training Plan Data</h5>
            </div>
            <div class="card-body">
                <canvas id="trainingPlanChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card modern-chart-card">
            <div class="card-header">
                <h5 class="card-title">Current Completion Data</h5>
            </div>
            <div class="card-body">
                <canvas id="secondChart"></canvas>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card modern-chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Current Employee Progress</h5>
                    <button id="exportButton" class="btn btn-success">Export to Excel</button>
                </div>
                <div class="scrollable-table-container">
                    <table class="table table-bordered" id="currentProgressTable">
                        <thead>
                            <tr>
                                <th><a href="#" onclick="sortTable('emp_id', 'currentProgressTable')">Employee ID</a></th>
                                <th><a href="#" onclick="sortTable('emp_name', 'currentProgressTable')">Employee Name</a></th>
                                <th><a href="#" onclick="sortTable('department', 'currentProgressTable')">Employee Department</a></th>
                                <th><a href="#" onclick="sortTable('training_date', 'currentProgressTable')">Training Date</a></th>
                                <th><a href="#" onclick="sortTable('expired_date', 'currentProgressTable')">Expired Date</a></th>
                                <th><a href="#" onclick="sortTable('training_hours', 'currentProgressTable')">Training Hours</a></th>
                                <th><a href="#" onclick="sortTable('status', 'currentProgressTable')">Status</a></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card modern-chart-card">
                <div class="card-header">
                    <h5 class="card-title">Current Trainer Progress</h5>
                    <button id="exportTrainerButton" class="btn btn-success">Export to Excel</button>
                </div>
                <div class="scrollable-table-container">
                    <table class="table table-bordered" id="currentProgressTableTrainer">
                        <thead>
                            <tr>
                                <th><a href="#" onclick="sortTable1('emp_id', 'currentProgressTableTrainer')">Employee ID</a></th>
                                <th><a href="#" onclick="sortTable1('emp_name', 'currentProgressTableTrainer')">Employee Name</a></th>
                                <th><a href="#" onclick="sortTable1('department', 'currentProgressTableTrainer')">Employee Department</a></th>
                                <th><a href="#" onclick="sortTable1('training_date', 'currentProgressTableTrainer')">Training Date</a></th>
                                <th><a href="#" onclick="sortTable1('expired_date', 'currentProgressTableTrainer')">Expired Date</a></th>
                                <th><a href="#" onclick="sortTable1('training_hours', 'currentProgressTableTrainer')">Training Hours</a></th>
                                <th><a href="#" onclick="sortTable1('status', 'currentProgressTableTrainer')">Status</a></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script>
    // Excel button function
    document.getElementById('exportButton').addEventListener('click', function() {
        var table = document.getElementById('currentProgressTable');
        var workbook = XLSX.utils.table_to_book(table, {sheet: "Sheet 1"});
        XLSX.writeFile(workbook, 'CurrentEmployeeProgress.xlsx');
    });
    document.getElementById('exportTrainerButton').addEventListener('click', function() {
        var table = document.getElementById('currentProgressTableTrainer');
        var workbook = XLSX.utils.table_to_book(table, {sheet: "Sheet 1"});
        XLSX.writeFile(workbook, 'CurrentTrainerProgress.xlsx');
    });
    </script>
    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function sortTable(column, tableId) {
    document.querySelectorAll('#currentProgressTable th a').forEach(a => {
        a.classList.remove('sorted-asc', 'sorted-desc');
        if (a.getAttribute('onclick').includes(column) === false) {
            delete a.dataset.direction;
        }
    });

    let header = document.querySelector(`a[onclick="sortTable('${column}', '${tableId}')"]`);
    let currentDirection = header.dataset.direction || 'desc';
    let newDirection = currentDirection === 'desc' ? 'asc' : 'desc';
    header.dataset.direction = newDirection;
    header.classList.add(newDirection === 'desc' ? 'sorted-desc' : 'sorted-asc');

    fetchData({sort: column, direction: newDirection, tableId: tableId});
}
function sortTable1(column, tableId) {
    document.querySelectorAll('#currentProgressTableTrainer th a').forEach(a => {
        a.classList.remove('sorted-asc', 'sorted-desc');
        if (a.getAttribute('onclick').includes(column) === false) {
            delete a.dataset.direction;
        }
    });

    let header = document.querySelector(`a[onclick="sortTable1('${column}', '${tableId}')"]`);
    let currentDirection = header.dataset.direction || 'desc';
    let newDirection = currentDirection === 'desc' ? 'asc' : 'desc';
    header.dataset.direction = newDirection;
    header.classList.add(newDirection === 'desc' ? 'sorted-desc' : 'sorted-asc');

    fetchData({sort: column, direction: newDirection, tableId: tableId});
}
function fetchData(params) {
    let endpoint = '';
    if (params.tableId === 'currentProgressTable') {
        endpoint = '/current-progress-data';
    } else if (params.tableId === 'currentProgressTableTrainer') {
        endpoint = '/current-progress-data-trainer'; // Update this to the correct endpoint for trainers
    }
    let queryString = Object.keys(params).map(key => `${key}=${params[key]}`).join('&');
    fetch(`${endpoint}?${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (params.tableId === 'currentProgressTable') {
                updateTable(data);
            } else if (params.tableId === 'currentProgressTableTrainer') {
                updateTrainerTable(data);
            }
        })
        .catch(error => {
            console.error('Error fetching sorted data:', error);
        });
}
function updateTable(data) {
    let tableBody = document.getElementById('currentProgressTable').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear the table first

    let maxTrainingHours = 40; // You can adjust this value as needed

    data.forEach(employee => {
        if (employee.trainer_emp === 'emp') { // Add this check to filter for 'emp' only
            let row = document.createElement('tr');

            let idCell = document.createElement('td');
            idCell.textContent = employee.emp_id;

            let nameCell = document.createElement('td');
            nameCell.textContent = employee.emp_name;

            let deptCell = document.createElement('td');
            deptCell.textContent = employee.department;
            
            let trainCell = document.createElement('td');
            trainCell.textContent = employee.training_department;
            
            let expCell = document.createElement('td');
            expCell.textContent = employee.expired_date;

            let hoursCell = document.createElement('td');
            let progressBarContainer = document.createElement('div');
            progressBarContainer.style.width = '100%';
            progressBarContainer.style.backgroundColor = '#f0f0f0';
            progressBarContainer.style.borderRadius = '5px';
            progressBarContainer.style.overflow = 'hidden';

            let progressBar = document.createElement('div');
            let percentageWidth = (employee.training_hours / maxTrainingHours) * 100;
            progressBar.style.width = `${percentageWidth}%`;
            progressBar.style.height = '20px'; 
            progressBar.style.backgroundColor = percentageWidth >= 100 ? 'green' : percentageWidth >= 50 ? 'orange' : 'red';
            progressBar.style.textAlign = 'center';
            progressBar.style.lineHeight = '20px';
            progressBar.style.color = 'white';
            progressBar.textContent = `${employee.training_hours} hrs`;

            progressBarContainer.appendChild(progressBar);
            hoursCell.appendChild(progressBarContainer);

            let statusCell = document.createElement('td');
            statusCell.textContent = employee.status;

            row.appendChild(idCell);
            row.appendChild(nameCell);
            row.appendChild(deptCell);
            row.appendChild(trainCell);
            row.appendChild(expCell);
            row.appendChild(hoursCell);
            row.appendChild(statusCell);

            tableBody.appendChild(row); // The row is appended only if the condition is true
        }
    });
}
function updateTrainerTable(data) {
    let tableBody = document.getElementById('currentProgressTableTrainer').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear the table first

    let maxTrainingHours = 8; // You can adjust this value as needed

    data.forEach(employee => {
        if (employee.trainer_emp === 'trainer') { // Add this check to filter for 'emp' only
            let row = document.createElement('tr');

            let idCell = document.createElement('td');
            idCell.textContent = employee.emp_id;

            let nameCell = document.createElement('td');
            nameCell.textContent = employee.emp_name;

            let deptCell = document.createElement('td');
            deptCell.textContent = employee.department;

            let trainCell = document.createElement('td');
            trainCell.textContent = employee.training_date;
            
            let expCell = document.createElement('td');
            expCell.textContent = employee.expired_date;

            let hoursCell = document.createElement('td');
            let progressBarContainer = document.createElement('div');
            progressBarContainer.style.width = '100%';
            progressBarContainer.style.backgroundColor = '#f0f0f0';
            progressBarContainer.style.borderRadius = '5px';
            progressBarContainer.style.overflow = 'hidden';

            let progressBar = document.createElement('div');
            let percentageWidth = (employee.training_hours / maxTrainingHours) * 100;
            progressBar.style.width = `${percentageWidth}%`;
            progressBar.style.height = '20px'; 
            progressBar.style.backgroundColor = percentageWidth >= 100 ? 'green' : percentageWidth >= 50 ? 'orange' : 'red';
            progressBar.style.textAlign = 'center';
            progressBar.style.lineHeight = '20px';
            progressBar.style.color = 'white';
            progressBar.textContent = `${employee.training_hours} hrs`;

            progressBarContainer.appendChild(progressBar);
            hoursCell.appendChild(progressBarContainer);
            
            let statusCell = document.createElement('td');
            statusCell.textContent = employee.status;

            row.appendChild(idCell);
            row.appendChild(nameCell);
            row.appendChild(deptCell);
            row.appendChild(trainCell);
            row.appendChild(expCell);
            row.appendChild(hoursCell);
            row.appendChild(statusCell);

            tableBody.appendChild(row); // The row is appended only if the condition is true
        }
    });
}
</script>
<script>
fetch('/training-plan-chart-data')
    .then(response => response.json())
    .then(data => {
        var ctx = document.getElementById('trainingPlanChart').getContext('2d');
        var trainingPlanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.months,
                datasets: [{
                    label: '# of Trainings',
                    data: data.data,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    barThickness: 50
                }]
            },
            options: {
                
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total of Trainings'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: true,
                            maxRotation: 0,
                        },
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Training Plan Chart'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        bodyColor: '#333',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        borderRadius: 10,
                        titleColor: '#000',
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    backgroundColor: 'rgba(255, 99, 132, 0.4)'
                }
            }
        });
    });
fetch('/completion-chart-data')
    .then(response => response.json())
    .then(data => {
        var ctx = document.getElementById('secondChart').getContext('2d');
        var secondChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                
                datasets: [{
                    label: 'Total Employees',
                    data: data.data,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)', // Green
                        'rgba(255, 99, 132, 0.2)'  // Red 
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)', // Green
                        'rgba(255, 99, 132, 1)'  // Red 
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Employee Completion Status'
                    }
                }
            }
        });
    });
    fetch('/current-progress-data')
    .then(response => response.json())
    .then(data => {
        let tableBody = document.getElementById('currentProgressTable').querySelector('tbody');
        tableBody.innerHTML = '';

        let maxTrainingHours = 40;

        data.forEach(employee => {
            let row = document.createElement('tr');

            let idCell = document.createElement('td');
            let nameCell = document.createElement('td');
            let deptCell = document.createElement('td');
            let trainCell = document.createElement('td');
            let expCell = document.createElement('td');
            let hoursCell = document.createElement('td');
            let statusCell = document.createElement('td');

            idCell.textContent = employee.emp_id;
            nameCell.textContent = employee.emp_name;
            deptCell.textContent = employee.department;
            trainCell.textContent = employee.training_date;
            expCell.textContent = employee.expired_date;

            let progressBarContainer = document.createElement('div');
            progressBarContainer.style.width = '100%';
            progressBarContainer.style.backgroundColor = '#f0f0f0';
            progressBarContainer.style.borderRadius = '5px';
            progressBarContainer.style.overflow = 'hidden';

            let progressBar = document.createElement('div');
            let percentageWidth = (employee.training_hours / maxTrainingHours) * 100;
            progressBar.style.width = `${percentageWidth}%`;
            progressBar.style.height = '20px'; // Height
            progressBar.style.backgroundColor = percentageWidth >= 100 ? 'green' : percentageWidth >= 50 ? 'orange' : 'red';
            progressBar.style.textAlign = 'center';
            progressBar.style.lineHeight = '20px';
            progressBar.style.color = 'white';
            progressBar.textContent = `${employee.training_hours} hrs`;

            progressBarContainer.appendChild(progressBar);
            hoursCell.appendChild(progressBarContainer);

            statusCell.textContent = employee.status;

            row.appendChild(idCell);
            row.appendChild(nameCell);
            row.appendChild(deptCell);
            row.appendChild(trainCell);
            row.appendChild(expCell);
            row.appendChild(hoursCell);
            row.appendChild(statusCell);

            tableBody.appendChild(row);
        });
    });
    fetch('/current-progress-data-trainer')
    .then(response => response.json())
    .then(data => {
        let tableBody = document.getElementById('currentProgressTableTrainer').querySelector('tbody');
        tableBody.innerHTML = ''; // Clear the table first

        let maxTrainingHours = 8; // You can adjust this value as needed

        data.forEach(employee => {
            if (employee.trainer_emp === 'trainer') { // Add this check to filter for 'trainer' only
                let row = document.createElement('tr');

                let idCell = document.createElement('td');
                idCell.textContent = employee.emp_id;

                let nameCell = document.createElement('td');
                nameCell.textContent = employee.emp_name;

                let deptCell = document.createElement('td');
                deptCell.textContent = employee.department;
                
                let trainCell = document.createElement('td');
                trainCell.textContent = employee.training_date;
                
                let expCell = document.createElement('td');
                expCell.textContent = employee.expired_date;

                let hoursCell = document.createElement('td');
                let progressBarContainer = document.createElement('div');
                progressBarContainer.style.width = '100%';
                progressBarContainer.style.backgroundColor = '#f0f0f0';
                progressBarContainer.style.borderRadius = '5px';
                progressBarContainer.style.overflow = 'hidden';

                let progressBar = document.createElement('div');
                let percentageWidth = Math.min((employee.training_hours / maxTrainingHours) * 100, 100); // Ensure width doesn't exceed 100%
                progressBar.style.width = `${percentageWidth}%`;
                progressBar.style.height = '20px';
                progressBar.style.backgroundColor = percentageWidth >= 100 ? 'green' : percentageWidth >= 50 ? 'orange' : 'red';
                progressBar.style.textAlign = 'center';
                progressBar.style.lineHeight = '20px';
                progressBar.style.color = percentageWidth >= 100 ? 'white' : 'white'; // Change text color to black if progress bar is green
                progressBar.textContent = `${employee.training_hours} hrs`;

                progressBarContainer.appendChild(progressBar);
                hoursCell.appendChild(progressBarContainer);

                
                let statusCell = document.createElement('td');
                statusCell.textContent = employee.status;

                row.appendChild(idCell);
                row.appendChild(nameCell);
                row.appendChild(deptCell);
                row.appendChild(trainCell);
                row.appendChild(expCell);
                row.appendChild(hoursCell);
                row.appendChild(statusCell);

                tableBody.appendChild(row); // The row is appended only if the condition is true
            }
        });
    });
</script>
@stop
@section('css')
<style>
.sorted-asc::after {
    content: '▲';
    font-size: 12px;
}
.sorted-desc::after {
    content: '▼';
    font-size: 12px;
}
.card.modern-chart-card {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    border-radius: 10px;
    overflow: hidden;
    margin-left: 20px;
}
#trainingPlanChart {
    background: linear-gradient(180deg, rgba(255, 99, 132, 0.2) 0%, rgba(255, 99, 132, 0.02) 100%);
    border-radius: 10px;
}
#secondChart {
    background: linear-gradient(180deg, rgba(255, 99, 132, 0.2) 0%, rgba(255, 99, 132, 0.02) 100%);
    border-radius: 10px;
}
.row {
    margin-right: 0;
    margin-left: 0;
}
.scrollable-table-container {
    overflow-x: auto; /* Enables horizontal scrolling */
    padding: 0 15px; /* Adds padding on the left and right */
}
@media (max-width: 575px) {
    .card-body {
        min-height: 200px; // Adjust this value as needed to give the chart enough room
    }
}
@media (max-width: 991px) {
    .card.modern-chart-card {
        margin-left: 10px;
    }
}
@media (max-width: 768px) {
    .card.modern-chart-card {
        margin-left: 5px; 
    }
    .scrollable-table-container {
        overflow-x: auto; /* Enables horizontal scrolling */
        margin: 10px -5px; /* Adjust margins as needed */
    }
}
@media (max-width: 575px) {
    .card.modern-chart-card {
        margin-left: 0;
        margin-bottom: 10px;
    }
    .row {
        margin-right: -15px;
        margin-left: -15px;
    }
}
</style>
@stop
@section('javascript')
@stop