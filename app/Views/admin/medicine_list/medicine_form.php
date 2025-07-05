<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Master Data Obat</h1>

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
            <form action="<?= base_url('save/medicine'); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="kode_obat">Kode Obat</label>
                        <input type="text" class="form-control" id="kode_obat" name="code" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="medicine_name">Nama Obat</label>
                        <input type="text" class="form-control" id="medicine_name" name="name">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="md_category">Kategori Obat</label>
                        <select id="md_category" class="form-control" name="md_category">
                            <option value="" selected disabled>-- Pilih data --</option>
                            <?php foreach ($md_category as $category): ?>
                                <option value="<?= esc($category['id']) ?>">
                                    <?= ucfirst(esc($category['name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="location">Lokasi Obat</label>
                        <select id="location" class="form-control" name="location">
                            <option value="" selected disabled>-- Pilih data --</option>
                            <?php foreach ($md_location as $category): ?>
                                <option value="<?= esc($category['id']) ?>">
                                    <?= ucfirst(esc($category['name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="manufactur">Manufaktur</label>
                        <select id="manufactur" class="form-control" name="manufactur">
                            <option value="" selected disabled>-- Pilih data --</option>
                            <?php foreach ($manufactur as $category): ?>
                                <option value="<?= esc($category['id']) ?>">
                                    <?= ucfirst(esc($category['name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="md_unit">Satuan</label>
                        <select id="md_unit" class="form-control" name="md_unit">
                            <option value="" selected disabled>-- Pilih data --</option>
                            <?php foreach ($md_unit as $category): ?>
                                <option value="<?= esc($category['id']) ?>">
                                    <?= ucfirst(esc($category['name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="convertion_value">Nilai Konversi</label>
                        <input type="text" class="form-control" id="convertion_value" name="convertion_value">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="substance" class="form-label">Pilih Zat Aktif</label>
                        <select id="substance" class="form-control select2" name="substance[]" multiple="multiple" style="width: 100%;">
                            <?php foreach ($m_subs as $category): ?>
                                <option value="<?= esc($category['id']) ?>">
                                    <?= ucfirst(esc($category['name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="md_pict">Upload Gambar Obat</label>
                        <input type="file" class="form-control" id="md_pict" name="md_pict">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="other_data">Data Lainnya (Indikasi obat, efek samping, cara pemakaian)</label>
                        <textarea class="form-control" name="other_data" id="other_data"></textarea>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="price">Harga (E - Katalog)</label>
                        <input type="text" class="form-control" id="price" name="price">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="discount_price">Harga Diskon</label>
                        <input type="text" class="form-control" id="discount_price" name="discount_price">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-control select" name="status">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <br>
                <!-- Tambahan Radio Button -->
                <div class="form-group">
                    <label>Obat Kronis</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="md_chronic" id="md_chronic" value="Yes">
                        <label class="form-check-label" for="md_chronic">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="md_chronic" id="md_chronic" value="No">
                        <label class="form-check-label" for="md_chronic">No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Vaksin</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="vaccine" id="vaccine" value="Yes">
                        <label class="form-check-label" for="vaccine">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="vaccine" id="vaccine" value="No">
                        <label class="form-check-label" for="vaccine">No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cover BPJS</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="cover_bpjs" id="cover_bpjs" value="Yes">
                        <label class="form-check-label" for="cover_bpjs">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="cover_bpjs" id="cover_bpjs" value="No">
                        <label class="form-check-label" for="cover_bpjs">No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>E Catalog</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="ecatalog" id="ecatalog" value="Yes">
                        <label class="form-check-label" for="ecatalog">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="ecatalog" id="ecatalog" value="No">
                        <label class="form-check-label" for="ecatalog">No</label>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('medicinelist') ?>" class="btn btn-warning">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>