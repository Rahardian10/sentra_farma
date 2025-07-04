<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <h1 class="h3 mb-4 text-gray-800">Form Edit Transaksi Obat Keluar</h1>

    <!-- card -->
    <div class="card">
        <div class="card-body">
            <!-- Form -->
            <form action="<?= base_url('save/stockout/' . $stockout['id']); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Informasi Pengeluaran -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Informasi Pengeluaran</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="exit_number">Nomor Transaksi Keluar</label>
                                <input type="text" class="form-control" id="exit_number" name="exit_number" value="<?= esc($stockout['exit_number']) ?>" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Judul Pengeluaran</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= esc($stockout['title']) ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="date_of_public">Tanggal Penerbitan</label>
                                <input type="date" class="form-control" id="date_of_public" name="date_of_public" value="<?= esc($stockout['date_of_public']) ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="trx_type">Jenis Transaksi</label>
                                <select id="trx_type" class="form-control select2" name="trx_type" style="width: 100%;">
                                    <?php foreach ($trx_type as $category): ?>
                                        <option value="<?= esc($category['id']) ?>" <?= $category['id'] == $stockout['trx_type'] ? 'selected' : '' ?>>
                                            <?= ucfirst(esc($category['name'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="preparation_by">Disiapkan Oleh</label>
                                <select id="preparation_by" class="form-control select2" name="preparation_by" style="width: 100%;">
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= esc($user->id) ?>" <?= $user->id == $stockout['preparation_by'] ? 'selected' : '' ?>>
                                            <?= ucfirst(esc($user->fullname)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="trx_purpose">Tujuan Transaksi</label>
                                <textarea class="form-control" name="trx_purpose" id="trx_purpose"><?= esc($stockout['trx_purpose']) ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="desc">Keterangan</label>
                                <textarea class="form-control" name="desc" id="desc"><?= esc($stockout['desc']) ?></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="envidence">Upload Bukti</label>
                                <input type="file" class="form-control" id="envidence" name="envidence">
                                <?php if ($stockout['envidence']): ?>
                                    <small class="form-text text-muted">File Saat Ini:
                                        <a href="<?= base_url('uploads/evidence/' . $stockout['envidence']) ?>" target="_blank"><?= $stockout['envidence'] ?></a>
                                    </small>
                                <?php endif; ?>
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
                            <!-- <pre><?php print_r($medicines); ?></pre> -->
                            <tbody>
                                <?php foreach ($details as $i => $item): ?>
                                    <tr>
                                        <td>
                                            <select class="form-control select2 medicine-select-Out" name="items[<?= $i ?>][md_id]" style="width: 100%;" disabled>
                                                <?php foreach ($medicines as $med): ?>
                                                    <option value="<?= $med['id'] ?>" <?= $med['id'] == $item['medicine_id'] ? 'selected' : '' ?>>
                                                        <?= esc($med['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <!-- Hidden input supaya value tetap dikirim -->
                                            <input type="hidden" name="items[<?= $i ?>][md_id]" value="<?= $item['medicine_id'] ?>">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control stock_now" name="items[<?= $i ?>][stock_now]" value="<?= esc($item['stock_now']) ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control unit_now" name="items[<?= $i ?>][unit]" value="<?= esc($item['unit']) ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="items[<?= $i ?>][stock_qty]">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-row">Hapus</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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