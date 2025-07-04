<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">List Pesanan</h1>
    <!-- Search Input -->
    <div class="mb-4">
        <input type="text" id="searchInputOrder" class="form-control" placeholder="Cari Nomor Transaksi Disini...">
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Content -->
    <div class="row">
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <div class="col-md-12 mb-3 transaction-card" data-trx="<?= strtolower($order['no_trx']) ?>">
                    <?php
                    $leftClass = 'secondary'; // default
                    if ($order['status_id'] == 1) {
                        $leftClass = 'warning';
                    } elseif ($order['status_id'] == 2) {
                        $leftClass = 'primary';
                    } elseif ($order['status_id'] == 3) {
                        $leftClass = 'success';
                    } elseif ($order['status_id'] == 4) {
                        $leftClass = 'danger';
                    } elseif ($order['status_id'] == 5) {
                        $leftClass = 'info';
                    } elseif ($order['status_id'] == 6) {
                        $leftClass = 'danger';
                    }
                    ?>
                    <div class="card shadow border-left-<?= $leftClass ?>">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                            <div class="mb-2">
                                <h5 class="mb-1">
                                    <strong>No Transaksi:</strong> <?= esc($order['no_trx']) ?>
                                </h5>
                                <p class="mb-0">
                                    <strong>Username:</strong> <?= esc($order['username']) ?> <br>
                                    <strong>Status:</strong>
                                    <?php
                                    $badgeClass = 'secondary'; // default
                                    if ($order['status_id'] == 1) {
                                        $badgeClass = 'warning';
                                    } elseif ($order['status_id'] == 2) {
                                        $badgeClass = 'primary';
                                    } elseif ($order['status_id'] == 3) {
                                        $badgeClass = 'success';
                                    } elseif ($order['status_id'] == 4) {
                                        $badgeClass = 'danger';
                                    } elseif ($order['status_id'] == 5) {
                                        $badgeClass = 'info';
                                    } elseif ($order['status_id'] == 6) {
                                        $badgeClass = 'danger';
                                    }
                                    ?>
                                    <span class="badge badge-<?= $badgeClass ?>"><?= esc($order['status_name']) ?></span> <br>
                                    <strong>Total:</strong> Rp <?= number_format($order['total_price'], 0, ',', '.') ?> <br>
                                    <strong>Tanggal:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                                </p>
                            </div>
                            <div>
                                <a href="<?= base_url('order/detail/' . $order['id']) ?>" class="btn btn-sm btn-primary">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">Belum ada transaksi yang ditemukan.</div>
            </div>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection(); ?>