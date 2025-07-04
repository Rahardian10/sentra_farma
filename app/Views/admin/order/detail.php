<?= $this->extend('templates/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Transaksi</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (!empty($transaction)): ?>
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="mb-3">Informasi Transaksi</h5>
                <table class="table table-borderless">
                    <tr>
                        <th>Nomor Transaksi</th>
                        <td><?= esc($transaction['no_trx']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Transaksi</th>
                        <td><?= date('d M Y H:i', strtotime($transaction['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><?= esc($transaction['username']) ?></td>
                    </tr>
                    <tr>
                        <th>Nama Penerima</th>
                        <td><?= esc($transaction['recipient_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon</th>
                        <td><?= esc($transaction['phone_number']) ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td><?= esc($transaction['address']) ?>, <?= esc($transaction['city']) ?></td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td><?= esc($transaction['notes'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <?php
                        $badgeClass = 'secondary'; // default
                        if ($transaction['status_id'] == 1) {
                            $badgeClass = 'warning';
                        } elseif ($transaction['status_id'] == 2) {
                            $badgeClass = 'primary';
                        } elseif ($transaction['status_id'] == 3) {
                            $badgeClass = 'success';
                        } elseif ($transaction['status_id'] == 4) {
                            $badgeClass = 'danger';
                        } elseif ($transaction['status_id'] == 5) {
                            $badgeClass = 'info';
                        } elseif ($transaction['status_id'] == 6) {
                            $badgeClass = 'danger';
                        }
                        ?>
                        <td><span class="badge badge-<?= $badgeClass ?>"><?= esc($transaction['status_name']) ?></span></td>
                    </tr>
                    <tr>
                        <th>Ubah Status</th>
                        <td>
                            <?php if (!in_array($transaction['status'], [3, 4, 5, 6])): ?>
                                <form action="<?= base_url('order/updateStatus/' . $transaction['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="input-group" style="max-width: 300px;">
                                        <select name="status_id" class="form-control" required>
                                            <?php foreach ($statusList as $status): ?>
                                                <?php if (!in_array($status['id'], [3, 6])): ?>
                                                    <option value="<?= $status['id'] ?>" <?= $transaction['status'] == $status['id'] ? 'selected' : '' ?>>
                                                        <?= esc($status['name']) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Status tidak dapat diubah karena transaksi sudah diproses.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ongkos Kirim</th>
                        <td><strong>Rp <?= number_format($transaction['shipping_cost'], 0, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td><strong>Rp <?= number_format($transaction['total_price'], 0, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Bukti Pembayaran</th>
                        <td>
                            <?php if ($transaction['payment_file']): ?>
                                <img src="<?= base_url('uploads/bukti/' . $transaction['payment_file']) ?>" class="img-thumbnail" style="max-width: 300px;">
                            <?php else: ?>
                                <em class="text-muted">Belum diunggah</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h5>Detail Barang Dipesan</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($transaction['items'])): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Harga</th>
                                    <th>Gambar</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th> <!-- Kolom baru -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaction['items'] as $item): ?>
                                    <tr>
                                        <td><?= esc($item['medicine_name']) ?></td>
                                        <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php if ($item['medicine_image']): ?>
                                                <img src="<?= base_url('uploads/medicine/' . $item['medicine_image']) ?>" alt="gambar" style="height: 80px;">
                                            <?php else: ?>
                                                <em class="text-muted">Tidak ada gambar</em>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $item['qty'] ?></td>
                                        <td>
                                            Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada barang dalam transaksi ini.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!empty($statusLogs)): ?>
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5>Riwayat Status Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Status</th>
                                    <th>Tanggal Diubah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statusLogs as $log): ?>
                                    <tr>
                                        <td><?= esc($log['status_name']) ?></td>
                                        <td><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">Data transaksi tidak ditemukan.</div>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>