<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Transaksi Barang Masuk</h1>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <span class="icon">❌</span>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <span class="icon">✅</span>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">DataTables</h6>
        </div>
        <div class="card-body">
            <div class="text-end mb-3" style="text-align: right;">
                <a href="<?= base_url('add/medicinestockin'); ?>" class="btn btn-primary">Tambah Data</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor PO</th>
                            <th>Judul Pengadaan</th>
                            <th>Tanggal Penerimaan</th>
                            <th>Supplier</th>
                            <th>Penerima</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Nomor PO</th>
                            <th>Judul Pengadaan</th>
                            <th>Tanggal Penerimaan</th>
                            <th>Supplier</th>
                            <th>Penerima</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($medicineStockin as $mdstockin) : ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $mdstockin->po_number; ?></td>
                                <td><?= $mdstockin->title; ?></td>
                                <td><?= $mdstockin->date_of_receipt; ?></td>
                                <td><?= $mdstockin->supplier; ?></td>
                                <td><?= $mdstockin->receiver; ?></td>
                                <td>
                                    <!-- <a href="<?= base_url('studentlist/' . $mdstockin->id); ?>" class="btn btn-info">Detail</a> -->
                                    <a href="<?= base_url('edit/medicinestockin/' . $mdstockin->id); ?>" class="btn btn-warning">Update</a>
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