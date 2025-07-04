<?= $this->extend('templates/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Halaman Checkout</h1>

    <div class="alert alert-info shadow-sm border-left-primary">
        <h5 class="font-weight-bold">Informasi Pembayaran</h5>
        <p class="mb-1">Silakan transfer total belanja Anda ke rekening berikut:</p>
        <ul class="mb-2">
            <li><strong>Bank:</strong> BCA</li>
            <li><strong>Nomor Rekening:</strong> 0573829001</li>
            <li><strong>Atas Nama:</strong> PT Apotek Sentra Farma</li> <!-- Ubah jika perlu -->
        </ul>
        <p class="mb-0 text-danger"><strong>Setelah transfer, harap unggah bukti pembayaran (screenshot) pada form di bawah sebelum menekan tombol Checkout.</strong></p>
    </div>


    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php
    // Hitung total keranjang lebih awal agar bisa dipakai di form
    $total_co = 0;
    if (!empty($cartItems)) {
        foreach ($cartItems as $item) {
            $subtotal = $item['price'] * $item['qty'];
            $total_co += $subtotal;
        }
    }
    ?>

    <form id="checkoutForm" method="post" action="<?= base_url('checkout/process') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row">
            <!-- Form Data Diri -->
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5>Data Diri & Alamat</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= user()->username; ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="full_name">Nama Penerima</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Alamat Lengkap</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="city">Kota</label>
                            <input type="text" class="form-control" id="city" name="city" value="Bandung" readonly>
                        </div>
                        <?php if (!in_groups('Admin')): ?>
                            <div class="form-group">
                                <label for="area_co">Area</label>
                                <select name="area" id="area_co" class="form-control select2">
                                    <option value="" data-price="0">-- Pilih Area --</option>
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?= $area['id'] ?>" data-price="<?= $area['price'] ?>">
                                            <?= esc($area['name']) ?> - Rp <?= number_format($area['price'], 0, ',', '.') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="note">Keterangan Tambahan</label>
                            <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                        </div>

                        <?php if (!in_groups('Admin')): ?>
                            <div class="form-group">
                                <label for="paid">Upload Bukti Bayar</label>
                                <input type="file" class="form-control" id="paid" name="paid" required>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <label><strong>Metode Pembayaran</strong></label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" required>
                                    <label class="form-check-label" for="cash">Cash</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="non_cash" value="non_cash">
                                    <label class="form-check-label" for="non_cash">Non-Cash</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="paid_display">Uang yang Dibayarkan</label>
                                <input type="text" id="paid_display" class="form-control" placeholder="Masukkan jumlah dibayar">
                                <input type="hidden" name="paid_amount" id="paid_amount">
                            </div>

                            <div class="form-group">
                                <label for="change_amount">Kembalian</label>
                                <input type="text" id="change_amount_display" class="form-control" readonly>
                                <input type="hidden" name="change_amount" id="change_amount">
                            </div>

                        <?php endif; ?>

                        <!-- Hidden input for total -->
                        <input type="hidden" id="shipping_price_input" name="shipping_price_input" value="0">
                        <input type="hidden" id="grand_total_input" name="grand_total_input" value="<?= $total_co ?>">

                        <button type="submit" class="btn btn-success mt-3" id="checkoutBtn">Proses Checkout</button>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Keranjang -->
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5>Ringkasan Keranjang</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($cartItems)) : ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="cartTable">
                                    <thead>
                                        <tr>
                                            <th>Nama Obat</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cartItems as $item): ?>
                                            <tr>
                                                <td><?= esc($item['medicine_name']) ?></td>
                                                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                <td><?= $item['qty'] ?></td>
                                                <td>Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-right">Total</th>
                                            <th id="cartTotal">Rp <?= number_format($total_co, 0, ',', '.') ?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">Ongkir</th>
                                            <th id="shippingCost">Rp 0</th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">Grand Total</th>
                                            <th id="grandTotal">Rp <?= number_format($total_co, 0, ',', '.') ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else : ?>
                            <p class="text-muted">Keranjang Anda kosong.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection(); ?>