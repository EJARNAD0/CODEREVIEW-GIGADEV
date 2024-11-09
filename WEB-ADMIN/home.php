<?php
$user = new User();
$request = new Request();

// Get the username from the session
$username = $_SESSION['username'];

// Fetch the admin's name from the database using the username
$adminDetails = $user->get_user_by_id($user->get_user_id($username));
$adminName = $adminDetails['user_firstname'] . ' ' . $adminDetails['user_lastname'];

// Retrieve selected month and year or default to the current month and year
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// Fetch data for requests, feedback, and donations
$requestsCount = $request->count_requests_by_month($month, $year);
$feedbackCount = $request->count_feedbacks_by_month($month, $year);
$donationsCount = $request->count_donations_by_month($month, $year);

$totalCount = $requestsCount + $feedbackCount + $donationsCount;
$requestsPercentage = $totalCount ? ($requestsCount / $totalCount) * 100 : 0;
$feedbackPercentage = $totalCount ? ($feedbackCount / $totalCount) * 100 : 0;
$donationsPercentage = $totalCount ? ($donationsCount / $totalCount) * 100 : 0;
?>
<!-- Welcome Message -->
<div class="welcome-message">
    <h2>Welcome, <?php echo htmlspecialchars($adminName); ?>!</h2>
    <p>Here is the monthly percentage distribution for requests, feedbacks, and donations.</p>
 </div>
<!-- Form for Selecting Month and Year -->
<div class="form-container">
    <form method="GET" class="month-year-form">
        <div class="form-group">
            <label for="month">Month:</label>
            <select id="month" name="month">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>" 
                        <?php if ($m == $month) echo 'selected'; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Year:</label>
            <select id="year" name="year">
                <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                    <option value="<?php echo $y; ?>" <?php if ($y == $year) echo 'selected'; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="view-button">View Data</button>
    </form>
</div>

<div class="chart-section">
    <h3>Monthly Percentage Distribution for <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></h3>
    <div class="chart-container">
        <canvas id="requestsFeedbackDonationsChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    const ctx = document.getElementById('requestsFeedbackDonationsChart').getContext('2d');
    const data = {
        labels: ['Requests', 'Feedbacks', 'Donations'],
        datasets: [{
            label: 'Percentage',
            data: [
                <?php echo $requestsPercentage; ?>, 
                <?php echo $feedbackPercentage; ?>, 
                <?php echo $donationsPercentage; ?>
            ],
            backgroundColor: [
                'rgba(255, 87, 51, 0.85)',  // Requests (red-orange)
                'rgba(255, 165, 0, 0.85)',  // Feedbacks (orange)
                'rgba(34, 139, 34, 0.85)',  // Donations (green)
            ],
            borderColor: [
                'rgba(139, 69, 19, 1)',     // Darker shade for 3D effect
                'rgba(218, 165, 32, 1)',
                'rgba(0, 100, 0, 1)',
            ],
            borderWidth: 1.5,
            hoverOffset: 10  // Creates depth effect on hover
        }]
    };

    const config = {
        type: 'pie',
        data: data,
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        color: '#333',
                        font: {
                            size: 14,
                            family: 'Arial, sans-serif'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(1) + '%';
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    anchor: 'center',
                    align: 'center',
                    formatter: (value) => {
                        return value.toFixed(1) + '%';
                    },
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    bottom: 20
                }
            }
        },
        plugins: [ChartDataLabels]
    };

    new Chart(ctx, config);
</script>
