<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Edit Transaksi Obat Masuk</h1>

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">List Obat</h1>
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

    <!-- card -->
    <div class="card">
        <div class="card-body">
            <!-- Form -->

            <form action="<?= base_url('update/stockin/' . $stockin['id']); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Informasi Supplier -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Informasi Penerimaan</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="po_number">Nomor PO</label>
                                <input type="text" class="form-control" id="po_number" name="po_number" value="<?= esc($stockin['po_number']) ?>" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Judul Pengadaan</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= esc($stockin['title']) ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="date_of_receipt">Tanggal Penerimaan</label>
                                <input type="date" class="form-control" id="date_of_receipt" name="date_of_receipt" value="<?= esc($stockin['date_of_receipt']) ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="supplier">Supplier</label>
                                <input type="text" class="form-control" id="supplier" name="supplier" value="<?= esc($stockin['supplier']) ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="supplier_address">Alamat Supplier</label>
                                <textarea class="form-control" name="supplier_address" id="supplier_address"><?= esc($stockin['supplier_address']) ?></textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="supplier_contact">Kontak Supplier</label>
                                <input type="text" class="form-control" id="supplier_contact" name="supplier_contact" value="<?= esc($stockin['supplier_contact']) ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="receiver">Penerima</label>
                                <input type="text" class="form-control" id="receiver" name="receiver" value="<?= esc($stockin['receiver']) ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="invoice">Upload Invoice</label>
                                <input type="file" class="form-control" id="invoice" name="invoice">
                                <?php if ($stockin['invoice']): ?>
                                    <small class="form-text text-muted">File Saat Ini: <a href="<?= base_url('uploads/invoice/' . $stockin['invoice']) ?>" target="_blank"><?= $stockin['invoice'] ?></a></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Input Obat -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">Detail Barang Masuk</div>
                    <div class="card-body">
                        <table class="table table-bordered" id="stockTable">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Stok Sekarang</th>
                                    <th>Satuan</th>
                                    <th>Stok Masuk</th>
                                    <th>Harga Satuan</th>
                                    <th>Tanggal Kadaluarsa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $i => $item): ?>
                                    <tr>
                                        <td>
                                            <select class="form-control select2 medicine-select" name="items[<?= $i ?>][md_id]" style="width: 100%;">
                                                <option value="" disabled>-- Pilih --</option>
                                                <?php foreach ($medicines as $med): ?>
                                                    <option value="<?= $med['id'] ?>" <?= $med['id'] == $item['medicine_id'] ? 'selected' : '' ?>><?= esc($med['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control stock_now" name="items[<?= $i ?>][qty]" value="<?= esc($item['qty']) ?>" readonly></td>
                                        <td><input type="text" class="form-control unit_now" name="items[<?= $i ?>][unit]" value="<?= esc($item['unit']) ?>" readonly></td>
                                        <td><input type="number" class="form-control" name="items[<?= $i ?>][stock_qty]" value="<?= esc($item['stock_qty']) ?>"></td>
                                        <td><input type="text" class="form-control" name="items[<?= $i ?>][unit_price]" value="<?= esc($item['unit_price']) ?>"></td>
                                        <td><input type="date" class="form-control" name="items[<?= $i ?>][expire_date]" value="<?= esc($item['expire_date']) ?>"></td>
                                        <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-info" id="addRow">Tambah Barang</button>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('medicinestockin') ?>" class="btn btn-warning">Kembali</a>
                </div>
            </form>

        </div>
    </div>
</div>
<?= $this->endSection(); ?>