<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">List Transaksi</h1>

    <div class="mb-4">
        <input type="text" id="searchInputTransaction" class="form-control" placeholder="Cari Nomor Transaksi Disini...">
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

    <!-- List Transaksi -->
    <div class="row">
        <?php if (!empty($transactions)): ?>
            <?php foreach ($transactions as $trx): ?>
                <div class="col-md-12 mb-3 transaction-card" data-trx="<?= strtolower($trx['transaction_number']) ?>">
                    <div class="card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h5 class="mb-1">Nomor Transaksi: <strong><?= esc($trx['transaction_number']) ?></strong></h5>
                                <?php
                                $badgeClass = 'secondary'; // default
                                if ($trx['status_id'] == 1) {
                                    $badgeClass = 'warning';
                                } elseif ($trx['status_id'] == 2) {
                                    $badgeClass = 'primary';
                                } elseif ($trx['status_id'] == 3) {
                                    $badgeClass = 'success';
                                } elseif ($trx['status_id'] == 4) {
                                    $badgeClass = 'danger';
                                } elseif ($trx['status_id'] == 5) {
                                    $badgeClass = 'info';
                                } elseif ($trx['status_id'] == 6) {
                                    $badgeClass = 'danger';
                                }
                                ?>
                                <p class="mb-0">Status: <span class="badge badge-<?= $badgeClass ?>"><?= esc($trx['status']) ?></span></p>
                                <?php if (in_groups('Admin')): ?>
                                    <p class="mb-0">Platform: <strong><?= esc($trx['platform']) ?> / Kasir</strong></p>
                                <?php endif; ?>
                                <p class="mb-0">Tanggal: <?= date('d M Y H:i', strtotime($trx['created_at'])) ?></p>
                            </div>
                            <div class="text-right">
                                <p class="mb-0">Total Harga:</p>
                                <h4 class="text-primary">Rp <?= number_format($trx['total_price'], 0, ',', '.') ?></h4>
                                <a href="<?= base_url('transaction/detail/' . $trx['id']); ?>" class="btn btn-sm btn-outline-info mt-2 toggle-detail">Lihat Detail</a>
                                <?php if ($trx['status_id'] == 1): ?>
                                    <a href="#"
                                        class="btn btn-sm btn-outline-danger mt-2 cancel-order-btn"
                                        data-toggle="modal"
                                        data-target="#cancelOrderModal"
                                        data-id="<?= $trx['id'] ?>">
                                        Batalkan Pesanan
                                    </a>
                                <?php elseif ($trx['status_id'] == 5): ?>
                                    <a href="<?= base_url('transaction/confirm/' . $trx['id'] . '?status=5') ?>" class="btn btn-sm btn-outline-success mt-2">Selesaikan Pesanan</a>
                                <?php endif; ?>
                                <a href="<?= base_url('transaction/invoice/' . $trx['id']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                                    <i class="fas fa-file-pdf"></i> Download Invoice
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card border-warning text-center py-4">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                        <h5 class="card-title text-warning">Belum ada transaksi</h5>
                        <p class="card-text text-muted">Anda belum melakukan transaksi apapun. Yuk, mulai berbelanja!</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="cancelOrderForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Konfirmasi Pembatalan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin membatalkan pesanan ini?<br>
                    <strong>Catatan:</strong> Proses refund akan dilakukan di menu <em>Refund</em>.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan Pesanan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection(); ?>