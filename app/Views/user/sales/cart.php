<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Keranjang</h1>

    <!-- Search Input -->
    <div class="mb-4">
        <input type="text" id="searchInputCard" class="form-control" placeholder="Cari Nama Obat Disini..." onkeyup="filterCartItems()">
    </div>

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

    <!-- Content -->
    <!-- Keranjang Obat -->
    <?php if (!empty($cart_items)) : ?>
        <div class="card shadow-sm p-3">
            <?php foreach ($cart_items as $item) : ?>
                <form method="post" action="<?= base_url('cart/update') ?>">
                    <?= csrf_field() ?>
                    <div class="row align-items-center border-bottom py-3 cart-item" data-item-name="<?= esc(strtolower($item['name'])) ?>">
                        <!-- Gambar Obat -->
                        <div class="col-md-2 text-center">
                            <img src="<?= base_url('uploads/medicine/' . $item['image']) ?>" alt="obat" class="img-fluid rounded" style="max-height: 80px;">
                        </div>

                        <!-- Nama + Harga -->
                        <div class="col-md-3">
                            <h5 class="mb-1"><?= esc($item['name']) ?></h5>
                            <p class="text-muted mb-0">Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-sm btn-secondary decrease" data-medicine-id="<?= $item['medicine_id'] ?>" data-stock="<?= $item['stock'] ?>">−</button>
                                <input
                                    type="text"
                                    class="form-control text-center quantity-input"
                                    value="<?= esc($item['quantity']) ?>"
                                    data-medicine-id="<?= $item['medicine_id'] ?>"
                                    data-stock="<?= $item['stock'] ?>"
                                    readonly>
                                <button type="button" class="btn btn-sm btn-secondary increase" data-medicine-id="<?= $item['medicine_id'] ?>" data-stock="<?= $item['stock'] ?>">+</button>
                            </div>
                            <small class="text-info">Stok tersedia: <?= $item['stock'] ?></small>
                        </div>

                        <!-- Total Harga -->
                        <div class="col-md-2 total-harga">
                            <strong>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></strong>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="col-md-3 text-right">
                            <a href="<?= base_url('cart/remove/' . $item['medicine_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus item ini?')">Hapus</a>
                        </div>
                    </div>
                </form>
            <?php endforeach; ?>

            <!-- Ringkasan Total -->
            <div class="row justify-content-end mt-4">
                <div class="col-md-4">
                    <h5>Total Belanja:</h5>
                    <h4 class="text-success">Rp <?= number_format($total_amount, 0, ',', '.') ?></h4>
                    <a href="<?= base_url('checkout') ?>" class="btn btn-primary btn-block mt-2">Checkout</a>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-info text-center">
            Keranjang kamu masih kosong 🛒
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection(); ?>