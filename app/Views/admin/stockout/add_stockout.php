<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Transaksi Obat Keluar</h1>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- card -->
    <div class="card">
        <div class="card-body">
            <!-- Form -->

            <form action="<?= base_url('save/stockout'); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Informasi Supplier -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Informasi Pengeluaran</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="exit_number">Nomor Transaksi Keluar</label>
                                <input type="text" class="form-control" id="exit_number" name="exit_number" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Judul Pengeluaran</label>
                                <input type="text" class="form-control" id="title" name="title">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="date_of_public">Tanggal Penerbitan</label>
                                <input type="date" class="form-control" id="date_of_public" name="date_of_public">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="trx_type">Jenis Transaksi</label>
                                <select id="trx_type" class="form-control select2" name="trx_type" style="width: 100%;">
                                    <?php foreach ($trx_type as $category): ?>
                                        <option value="<?= esc($category['id']) ?>">
                                            <?= ucfirst(esc($category['name'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="preparation_by">Disiapkan Oleh</label>
                                <select id="preparation_by" class="form-control select2" name="preparation_by" style="width: 100%;">
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= esc($user['id']) ?>">
                                            <?= ucfirst(esc($user['fullname'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="trx_purpose">Tujuan Transaksi</label>
                                <textarea class="form-control" name="trx_purpose" id="trx_purpose"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="desc">Keterangan</label>
                                <textarea class="form-control" name="desc" id="desc"></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="envidence">Upload Bukti</label>
                                <input type="file" class="form-control" id="envidence" name="envidence">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Input Obat -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">Detail Barang Keluar</div>
                    <div class="card-body">
                        <table class="table table-bordered" id="stockTableOut">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Stok Sekarang</th>
                                    <th>Satuan</th>
                                    <th>Stok Keluar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control select2 medicine-select-Out" name="items[0][md_id]" style="width: 100%;" id="medicineSelectDefault">
                                            <option value="" selected disabled>-- Pilih --</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control stock_now" name="items[0][stock_now]" readonly></td>
                                    <td><input type="text" class="form-control unit_now" name="items[0][unit]" readonly></td>
                                    <td><input type="number" class="form-control" name="items[0][stock_qty]"></td>
                                    <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-info" id="addRowOut">Tambah Barang</button>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('medicinestockout') ?>" class="btn btn-warning">Kembali</a>
                </div>
            </form>

        </div>
    </div>
</div>
<?= $this->endSection(); ?>