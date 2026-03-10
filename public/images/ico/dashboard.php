<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Selamat Datang  
            <small><b>Admin</b> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="#">Admin</a></li>
        </ol>
    </section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua" style="position:relative;">
                <?php if ($show_notification) : ?>
                    <div class="notification-dot" title="Ada siswa baru yang belum di-level kelas"></div>
                <?php endif; ?>
                <div class="inner">
                    <h3><?php echo isset($total_siswa) ? $total_siswa : '0'; ?></h3>
                    <p>DATA SISWA</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people"></i>
                </div>
                <a href="#" class="small-box-footer"></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo isset($count_robotic) ? $count_robotic : '0'; ?></h3>
                    <p>Level Kelas Robotic</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-cog"></i>
                </div>
                <i class="small-box-footer"></i>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?php echo isset($count_coding) ? $count_coding : '0'; ?></h3>
                    <p>Level Kelas Coding</p>
                </div>
                <div class="icon">
                    <i class="ion ion-laptop"></i>
                </div>
                <i class="small-box-footer"></i>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo isset($count_animasi) ? $count_animasi : '0'; ?></h3>
                    <p>Level Kelas Animasi</p>
                </div>
                <div class="icon">
                    <i class="ion ion-android-film"></i>
                </div>
                <i class="small-box-footer"></i>
            </div>
        </div>
    </div>

	<div style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; background: #ffffff; border-radius: 0.75rem; box-shadow: 0 2px 10px rgb(0 0 0 / 0.05); display: flex; gap: 20px;">

		<!-- Timeline Container -->
		<section style="flex: 0 0 200px; max-height: 352px; background: #f9fafb; border-radius: 0.75rem; padding: 20px; box-shadow: 0 1px 3px rgb(0 0 0 / 0.1); overflow-y: auto; font-family: Arial, sans-serif; font-size: 14px; color: #374151;">
			<?php if (!empty($new_students)): ?>
				<?php foreach ($new_students as $student): ?>
					<div style="margin-bottom: 16px; padding: 10px 15px; background: white; border-radius: 0.5rem; box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);">
						<div style="font-weight: 400; font-size: 12px; color: #111827;"><?php echo htmlspecialchars($student->nama_siswa); ?> </div><p style="font-size: 12px;"> - Mendaftar sebagai murid baru</p>
						<time datetime="<?php echo $student->tanggal; ?>" style="font-size: 0.875rem; color: #6b7280;">
						<?php echo date('d F Y', strtotime($student->tanggal)); ?>
						</time>
					</div>
					<?php endforeach; ?>
				<?php else: ?>
				<p style="font-style: italic; color: #6b7280;">Tidak ada Pendaftar Baru</p>
		<?php endif; ?>
		</section>

		<!-- Left Panel: Bar Chart + monthDataInfo -->
		<section style="flex: 1; display: flex; gap: 20px; background: #f9fafb; border-radius: 0.75rem; padding: 20px; box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);">
		
		<!-- Bar Chart -->
		<div class="chart" style="flex: 1; height: 300px;">
			<canvas id="barChart" style="width: 100%; height: 100%;"></canvas>
		</div>

		<!-- monthDataInfo -->
		<div id="monthDataInfo" style="flex: 0 0 180px; margin-top: 20px; font-family: Arial, sans-serif; font-size: 12px; color: #6b7280; line-height: 1.5;">
			<!-- Month data info will appear here -->
		</div>
		</section>

	</div>
	
</div>
</section>

<footer class="main-footer">
    <div class="pull-right hidden-xs"></div>
    <strong>Copyright &copy; 2019 - 2022 .</strong> All rights reserved.
</footer>

<!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?= base_url()?>assets/lib/jquery/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url()?>assets/lib/bootstrapV3/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?= base_url()?>assets/lib/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?= base_url()?>assets/lib/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url()?>assets/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= base_url()?>assets/js/demo.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    // Helper function to format month data array with colored count text
    function formatMonths(dataArray, color) {
        if (!dataArray || dataArray.length === 0) return "Tidak Ada Data";
        return dataArray.map(item => {
            const monthName = monthNames[(item.month || 1) - 1] || "Unknown";
            return monthName + ' (<span style="color: ' + color + '; font-weight: bold;">' + item.count + '</span>)';
        }).join(", ");
    }

    fetch('<?= base_url("Admin/getHistoryData") ?>')
        .then(response => response.json())
        .then(data => {
            console.log(data);

            const labels = ['Januari - Maret', 'April - Juli', 'Agustus - Desember'];
            const counts = [
                data['1_to_3'] ? data['1_to_3'].reduce((sum, item) => sum + parseInt(item.count), 0) : 0,
                data['4_to_6'] ? data['4_to_6'].reduce((sum, item) => sum + parseInt(item.count), 0) : 0,
                data['7_to_12'] ? data['7_to_12'].reduce((sum, item) => sum + parseInt(item.count), 0) : 0
            ];

            const ctx = document.getElementById('barChart').getContext('2d');
            const paymentHistoryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pembayaran',
                        data: counts,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)', // purple
                            'rgba(255, 159, 64, 0.2)',
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',  // purple
                            'rgba(255, 159, 64, 1)',
                        ],
                        borderWidth: 1,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    scales: {
                        x: {
                            categoryPercentage: 0.6,
                            barPercentage: 0.8
                        },
                        y: {
                            beginAtZero: true,
                            max: 30,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });

            const monthInfoDiv = document.getElementById("monthDataInfo");
            monthInfoDiv.innerHTML = `
                <strong>Bulan ke 1 - 3 (Jan-Mar):</strong> ${formatMonths(data['1_to_3'], 'rgba(75, 192, 192, 1)')} <br>
                <strong>Bulan ke 4 - 6 (Apr-Jun):</strong> ${formatMonths(data['4_to_6'], 'rgba(153, 102, 255, 1)')} <br>
                <strong>Bulan ke 7 - 12 (Jul-Dec):</strong> ${formatMonths(data['7_to_12'], 'rgba(255, 159, 64, 1)')}
            `;
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById("monthDataInfo").textContent = "Failed to load month data details.";
        });
</script>

</body>
</html>

