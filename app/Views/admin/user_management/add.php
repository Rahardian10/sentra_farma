<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Master Data Obat</h1>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-error">
            <span class="icon">❌</span>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- card -->
    <div class="card">
        <div class="card-body">
            <!-- Form -->
            <form action="<?= base_url('save/admin'); ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fullname">Fullname</label>
                        <input type="text" class="form-control" id="fullname" name="fullname">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="user_image">Upload Gambar</label>
                        <input type="file" class="form-control" id="user_image" name="user_image">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pass_confirm">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="pass_confirm" name="pass_confirm">
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('admin') ?>" class="btn btn-warning">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>