<!DOCTYPE html>
<html lang="en">
<head>

<link rel="website icon" type="png"
href="icons/logoMJII.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel='stylesheet' type='text/css' href="css/bootstrap.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/header.css"/>
    <title>mjii | Statistic</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script> <!-- datalabels plugin -->
    <style>
        .chart-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 90%;
            margin: auto;
            padding: 20px;
            flex-wrap: wrap;
            height: 500px;
        }
        canvas {
            max-width: 45%;
            height: 100%;
        }
    </style>
</head>
<body>
    <?php 
    session_start();
    if (!isset($_SESSION['Admin-name'])) {
        header("location: login.php");
    }
    include 'header.php'; 

    require 'connectDB.php';

    // Fetch data for 'timeout'
    $sql_timeout = "SELECT device_dep, COUNT(*) as count 
                    FROM users_logs 
                    WHERE timeout1 IS NOT NULL 
                    GROUP BY device_dep";
    $result_timeout = mysqli_query($conn, $sql_timeout);

    $device_deps = [];
    $timeout_counts = [];

    while ($row = mysqli_fetch_assoc($result_timeout)) {
        $device_deps[] = $row['device_dep'];
        $timeout_counts[] = $row['count'];
    }

    // Convert PHP arrays to JSON format
    $device_depsJSON = json_encode($device_deps);
    $timeoutCountsJSON = json_encode($timeout_counts);

    // Data for late entries (10 PM to 7 AM)
    $sql_late_entries = "SELECT device_dep, COUNT(*) as count 
                         FROM users_logs 
                         WHERE HOUR(timein1) >= 22 OR HOUR(timein1) < 7 
                         GROUP BY device_dep";
    $result_late_entries = mysqli_query($conn, $sql_late_entries);

    $late_device_deps = [];
    $late_entry_counts = [];

    while ($row = mysqli_fetch_assoc($result_late_entries)) {
        $late_device_deps[] = $row['device_dep'];
        $late_entry_counts[] = $row['count'];
    }

    // Convert PHP arrays to JSON format
    $late_device_depsJSON = json_encode($late_device_deps);
    $late_entry_countsJSON = json_encode($late_entry_counts);

    // Define fixed colors for pie charts
    $fixedColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
    $barColorsJSON = json_encode($fixedColors);

    // Define different colors for late entries
    $lateFixedColors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A6', '#FFC300', '#DAF7A6']; // New colors for late entries
    $late_barColorsJSON = json_encode($lateFixedColors);

    // Fetch Student Presence by Department
    $sql_presence_by_department = "SELECT device_dep, SUM(status = 'IN') as count_in, SUM(status = 'OUT') as count_out 
                                    FROM users_logs 
                                    GROUP BY device_dep";
    $result_presence_by_department = mysqli_query($conn, $sql_presence_by_department);

    $presence_deps = [];
    $presence_in_counts = [];
    $presence_out_counts = [];

    while ($row = mysqli_fetch_assoc($result_presence_by_department)) {
        $presence_deps[] = $row['device_dep'];
        $presence_in_counts[] = $row['count_in'];
        $presence_out_counts[] = $row['count_out'];
    }

    // Convert PHP arrays to JSON format
    $presence_depsJSON = json_encode($presence_deps);
    $presence_in_countsJSON = json_encode($presence_in_counts);
    $presence_out_countsJSON = json_encode($presence_out_counts);
    ?>
    
    <main>
        <section>
            <h1 class="slideInDown animated"></h1>

            <div class="chart-container">
                <canvas id="myChart1"></canvas>
                <canvas id="myChart2"></canvas>
                <canvas id="myChart4"></canvas>
                <div style="max-width: 90%; margin: auto;">
            </div>

            <script>
                // Pie chart for 'timeout'
                const device_deps = <?php echo $device_depsJSON; ?>;
                const timeoutCounts = <?php echo $timeoutCountsJSON; ?>;
                const barColors = <?php echo $barColorsJSON; ?>;

                new Chart("myChart1", {
                    type: "pie",
                    data: {
                        labels: device_deps,
                        datasets: [{
                            backgroundColor: barColors,
                            data: timeoutCounts
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: "Student Entry Count by Department",
                            fontSize: 30,
                            fontColor: "#000000",
                            fontFamily: "Times New Roman"
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            labels: {
                                fontColor: "#000000", 
                                fontStyle: "bold"
                            },
                            position: 'top'
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true
                        },
                        plugins: {
                            datalabels: {
                                color: '#000', // Calor teks
                                formatter: (value, ctx) => {
                                    let sum = ctx.dataset._meta[Object.keys(ctx.dataset._meta)[0]].total; // Jumlah total
                                    let percentage = (value / sum * 100).toFixed(2) + "%"; // Calculate of Percentage
                                    return percentage;
                                },
                                font: {
                                    weight: 'bold',
                                    size: 14
                                }
                            }
                        }
                    }
                });

                // Pie chart for 'late entries between 10 PM and 7 AM'
                const late_device_deps = <?php echo $late_device_depsJSON; ?>;
                const lateEntryCounts = <?php echo $late_entry_countsJSON; ?>;
                const lateBarColors = <?php echo $late_barColorsJSON; ?>;

                new Chart("myChart2", {
                    type: "pie",
                    data: {
                        labels: late_device_deps,
                        datasets: [{
                            backgroundColor: lateBarColors,
                            data: lateEntryCounts
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: "Late Student Entries (10 PM to 7 AM)",
                            fontSize: 30,
                            fontColor: "#000000",
                            fontFamily: "Times New Roman"
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            labels: {
                                fontColor: "#000000", 
                                fontStyle: "bold"
                            },
                            position: 'top'
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true
                        },
                        plugins: {
                            datalabels: {
                                color: '#000', // Calor Teks
                                formatter: (value, ctx) => {
                                    let sum = ctx.dataset._meta[Object.keys(ctx.dataset._meta)[0]].total; // total
                                    let percentage = (value / sum * 100).toFixed(2) + "%"; // Calculate  of Percentage
                                    return percentage;
                                },
                                font: {
                                    weight: 'bold',
                                    size: 14
                                }
                            }
                        }
                    }
                });


                // Modify myChart4 to be a pie chart
const presenceInCounts = <?php echo json_encode(array_sum($presence_in_counts)); ?>; // Total IN count
const presenceOutCounts = <?php echo json_encode(array_sum($presence_out_counts)); ?>; // Total OUT count
const presenceLabels = ['IN', 'OUT'];
const presenceData = [presenceInCounts, presenceOutCounts]; // Combined data

new Chart("myChart4", {
    type: "pie",
    data: {
        labels: presenceLabels,
        datasets: [{
            backgroundColor: ['#4CAF50', '#f44336'], // Colors for IN and OUT
            data: presenceData
        }]
    },
    options: {
        title: {
            display: true,
            text: "Student Presence by Department",
            fontSize: 30,
            fontColor: "#000000",
            fontFamily: "Times New Roman"
        },
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            labels: {
                fontColor: "#000000", 
                fontStyle: "bold"
            },
            position: 'top'
        },
        animation: {
            animateRotate: true,
            animateScale: true
        },
        plugins: {
            datalabels: {
                color: '#000', // Calor teks
                formatter: (value, ctx) => {
                    let sum = ctx.dataset._meta[Object.keys(ctx.dataset._meta)[0]].total; // Jumlah total
                    let percentage = (value / sum * 100).toFixed(2) + "%"; // Calculate of Percentage
                    return percentage;
                },
                font: {
                    weight: 'bold',
                    size: 14
                }
            }
        }
    }
});

                







                // Auto-refresh after 5 seconds
                setInterval(function() {
                    window.location.reload();
                }, 5000); // 5 Second
            </script>
        </section>
    </main>
</body>
</html>
