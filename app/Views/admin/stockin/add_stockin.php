<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Transaksi Obat Masuk</h1>

    <!-- card -->
    <div class="card">
        <div class="card-body">
            <!-- Form -->

            <form action="<?= base_url('save/stockin'); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Informasi Supplier -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Informasi Penerimaan</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="po_number">Nomor PO</label>
                                <input type="text" class="form-control" id="po_number" name="po_number" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Judul Pengadaan</label>
                                <input type="text" class="form-control" id="title" name="title">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="date_of_receipt">Tanggal Penerimaan</label>
                                <input type="date" class="form-control" id="date_of_receipt" name="date_of_receipt">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="supplier">Supplier</label>
                                <input type="text" class="form-control" id="supplier" name="supplier">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="supplier_address">Alamat Supplier</label>
                                <textarea class="form-control" name="supplier_address" id="supplier_address"></textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="supplier_contact">Kontak Supplier</label>
                                <input type="text" class="form-control" id="supplier_contact" name="supplier_contact">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="receiver">Penerima</label>
                                <input type="text" class="form-control" id="receiver" name="receiver">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="invoice">Upload Invoice</label>
                                <input type="file" class="form-control" id="invoice" name="invoice">
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
                                <tr>
                                    <td>
                                        <select class="form-control select2 medicine-select" name="items[0][md_id]" style="width: 100%;" id="medicineSelectDefault">
                                            <option value="" selected disabled>-- Pilih --</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control stock_now" name="items[0][stock_now]" readonly></td>
                                    <td><input type="text" class="form-control unit_now" name="items[0][unit]" readonly></td>
                                    <td><input type="number" class="form-control" name="items[0][stock_qty]"></td>
                                    <td><input type="text" class="form-control" name="items[0][unit_price]"></td>
                                    <td><input type="date" class="form-control" name="items[0][expire_date]"></td>
                                    <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                                </tr>
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