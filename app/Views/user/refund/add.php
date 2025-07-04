<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Form Refund / Pengembalian Dana</h1>

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

    <!-- Refund Form -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= base_url('refund/submit') ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="no_trx">Nomor Transaksi</label>
                    <select name="no_trx" id="no_trx" class="form-control" required>
                        <option value="">-- Pilih Nomor Transaksi --</option>
                        <?php foreach ($transactions as $trx): ?>
                            <option value="<?= esc($trx['no_trx']) ?>"><?= esc($trx['no_trx']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" name="name" class="form-control" id="name" value="<?= user()->fullname; ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="text" name="phone" class="form-control" id="phone" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" id="email" value="<?= user()->email; ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="bank">Nama Bank</label>
                    <select name="bank_id" id="bank" class="form-control" required>
                        <option value="">-- Pilih Bank --</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?= $bank['id'] ?>"><?= esc($bank['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="bank_account">Nomor Rekening</label>
                    <input type="text" name="bank_account" class="form-control" id="bank_account" required>
                </div>

                <div class="form-group">
                    <label for="reason">Alasan Refund</label>
                    <textarea name="reason" class="form-control" id="reason" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Kirim Permintaan Refund</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>