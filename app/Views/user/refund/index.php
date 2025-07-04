<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">List Refund / Pengembalian Dana</h1>

    <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class="fas fa-info-circle mr-2"></i>
        <div>
            Jika terdapat ketidaksesuaian dalam proses refund, silakan hubungi admin kami di nomor <strong>085774107602</strong>.
        </div>
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
    <div class="text-end mb-3" style="text-align: right;">
        <a href="<?= base_url('form_refund'); ?>" class="btn btn-primary">Form Refund</a>
    </div>

    <!-- Content -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">DataTables</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Transaksi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Nomor Transaksi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($refunds as $row) : ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= esc($row['no_trx']) ?></td>
                                <td>
                                    <?php
                                    $badgeClass = 'secondary'; // default
                                    if ($row['status_id'] == 1) {
                                        $badgeClass = 'warning';
                                    } elseif ($row['status_id'] == 2) {
                                        $badgeClass = 'primary';
                                    } elseif ($row['status_id'] == 3) {
                                        $badgeClass = 'danger';
                                    } elseif ($row['status_id'] == 4) {
                                        $badgeClass = 'success';
                                    }
                                    ?>
                                    <span class="badge badge-<?= $badgeClass; ?>">
                                        <?= esc($row['status_name']); ?>
                                    </span>
                                </td>
                                <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                                <td>Rp <?= number_format($row['total_price'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="<?= base_url('detail/refund/' . $row['refund_id']) ?>" class="btn btn-info btn-sm">Detail</a>
                                    <?php if ($row['status_id'] == 1): ?>
                                        <a href="<?= base_url('confirm/refund/' . $row['refund_id'] . '?status=3') ?>" class="btn btn-danger btn-sm">Batalkan Pengajuan</a>
                                    <?php elseif ($row['status_id'] == 2): ?>
                                        <a href="<?= base_url('confirm/refund/' . $row['refund_id'] . '?status=4') ?>" class="btn btn-success btn-sm">Selesai</a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('refund/invoice/' . $row['refund_id']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-file-pdf"></i> Download Invoice
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>