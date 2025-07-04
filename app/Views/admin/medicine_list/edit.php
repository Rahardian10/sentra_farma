<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Master Data Obat</h1>

    <!-- card -->
    <div class="card">
        <div class="card-body">
            <!-- Form -->
            <form action="<?= base_url('save/medicine'); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="kode_obat">Kode Obat</label>
                        <input type="text" class="form-control" id="kode_obat" name="code" value="<?= isset($medicineList) ? $medicineList->code : old('code') ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="medicine_name">Nama Obat</label>
                        <input type="text" class="form-control" id="medicine_name" name="name" value="<?= isset($medicineList) ? $medicineList->name : old('name') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="md_category">Kategori Obat</label>
                        <select id="md_category" class="form-control" name="md_category">
                            <option value="" selected disabled>-- Pilih data --</option>
                            <?php foreach ($md_category as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= isset($medicineList) && $category['id'] == $medicineList->md_cat_id ? 'selected' : '' ?>>
                                    <?= ucfirst($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="location">Lokasi Obat</label>
                        <select id="location" class="form-control" name="location">
                            <option value="" selected disabled>-- Pilih data --</option>
                            <?php foreach ($md_location as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= isset($medicineList) && $category['id'] == $medicineList->loc_id ? 'selected' : '' ?>>
                                    <?= ucfirst($category['name']) ?>
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
                                <option value="<?= $category['id'] ?>" <?= isset($medicineList) && $category['id'] == $medicineList->manufacturid ? 'selected' : '' ?>>
                                    <?= ucfirst($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="md_unit">Satuan</label>
                        <select id="md_unit" class="form-control" name="md_unit">
                            <option value="" selected disabled>-- Pilih data --</option>
                            <?php foreach ($md_unit as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= isset($medicineList) && $category['id'] == $medicineList->unitid ? 'selected' : '' ?>>
                                    <?= ucfirst($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="convertion_value">Nilai Konversi</label>
                        <input type="text" class="form-control" id="convertion_value" name="convertion_value" value="<?= isset($medicineList) ? esc($medicineList->convertion_value) : old('convertion_value') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="substance" class="form-label">Pilih Zat Aktif</label>
                        <select id="substance" class="form-control select2" name="substance[]" multiple="multiple" style="width: 100%;">
                            <?php foreach ($m_subs as $category): ?>
                                <option value="<?= $category['id'] ?>"
                                    <?= in_array($category['name'], $selectedSubs ?? []) ? 'selected' : '' ?>>
                                    <?= ucfirst($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="md_pict">Upload Gambar Obat</label>
                        <input type="file" class="form-control" id="md_pict" name="md_pict">
                        <input type="hidden" class="form-control" id="md_pict" name="current_md_pict" value="<?= isset($medicineList) ? $medicineList->medicine_pict : old('medicine_pict') ?>">
                        <?php if (isset($medicineList) && $medicineList->medicine_pict): ?>
                            <div>
                                <img src="<?= base_url('uploads/medicine/' . $medicineList->medicine_pict) ?>" width="100" style="margin: 10px;">
                                <p style="margin-left: 10px; margin-top:1px;"><?= $medicineList->medicine_pict; ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="other_data">Data Lainnya (Indikasi obat, efek samping, cara pemakaian)</label>
                        <textarea class="form-control" name="other_data" id="other_data"><?= isset($medicineList) ? $medicineList->other_data : old('other_data') ?></textarea>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="price">Harga (E - Katalog)</label>
                        <input type="text" class="form-control" id="price" name="price" value="<?= isset($medicineList) ? $medicineList->price : old('price') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="discount_price">Harga Diskon</label>
                        <input type="text" class="form-control" id="discount_price" name="discount_price" value="<?= isset($medicineList) ? $medicineList->discount_price : old('discount_price') ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-control select" name="status">
                            <option value="1" <?= ($medicineList->status == '1') ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= ($medicineList->status == '0') ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <br>
                <!-- Tambahan Radio Button -->
                <div class="form-group">
                    <label>Obat Kronis</label><br>
                    <?php
                    $chronicValue = isset($medicineList) ? $medicineList->md_chronic : old('md_chronic');
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="md_chronic" id="md_chronic_yes" value="Yes"
                            <?= $chronicValue == 'Yes' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="md_chronic_yes">Yes</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="md_chronic" id="md_chronic_no" value="No"
                            <?= $chronicValue == 'No' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="md_chronic_no">No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Vaksin</label><br>
                    <?php
                    $VaccineValue = isset($medicineList) ? $medicineList->vaccine : old('vaccine');
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="vaccine" id="vaccine" value="Yes"
                            <?= $VaccineValue == 'Yes' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="vaccine">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="vaccine" id="vaccine" value="No"
                            <?= $VaccineValue == 'No' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="vaccine">No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cover BPJS</label><br>
                    <?php
                    $BpjsValue = isset($medicineList) ? $medicineList->cover_bpjs : old('cover_bpjs');
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="cover_bpjs" id="cover_bpjs" value="Yes"
                            <?= $BpjsValue == 'Yes' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="cover_bpjs">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="cover_bpjs" id="cover_bpjs" value="No"
                            <?= $BpjsValue == 'No' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="cover_bpjs">No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>E - Katalog</label><br>
                    <?php
                    $ecatalog = isset($medicineList) ? $medicineList->ecatalog : old('ecatalog');
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="ecatalog" id="ecatalog" value="Yes"
                            <?= $ecatalog == 'Yes' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ecatalog">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="ecatalog" id="ecatalog" value="No"
                            <?= $ecatalog == 'No' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ecatalog">No</label>
                    </div>
                </div>

                <?php if (isset($medicineList->id)): ?>
                    <input type="hidden" name="id" value="<?= esc($medicineList->id) ?>">
                <?php endif; ?>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('medicinelist/index') ?>" class="btn btn-warning">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>