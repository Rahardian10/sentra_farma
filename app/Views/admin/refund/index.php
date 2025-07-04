<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">List Persetujuan Refund / Pengembalian Dana</h1>

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
                            <th>Nama</th>
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
                            <th>Nama</th>
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
                                <td><?= esc($row['name']); ?></td>
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
                                    <a href="<?= base_url('edit/refund/' . $row['refund_id']) ?>" class="btn btn-info btn-sm">Detail</a>
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