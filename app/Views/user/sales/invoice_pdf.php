<!DOCTYPE html>
<html>

<head>
    <title>Invoice - <?= $order['no_trx'] ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('logo.png') ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
            color: #333;
        }

        .header {
            border-bottom: 2px solid #0d6efd;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #0d6efd;
        }

        .company-info {
            font-size: 14px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .col {
            width: 48%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background-color: #f0f0f0;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        .total {
            margin-top: 10px;
            text-align: right;
            font-size: 14px;
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
            text-align: center;
            color: #999;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Apotik Sentra Farma</h1>
        <div class="company-info">
            Jl. Teratai Raya No.42, Rancaekek Wetan, <br>Kec. Rancaekek, Kabupaten Bandung, Jawa Barat 40394<br>
            Telp: 0818-0971-4520 | Email: erza1681@gmail.com<br>
            <?php if ($is_admin): ?>
                NPWP : 4521.9878.1234.0989
            <?php endif; ?>
        </div>
    </div>

    <div class="section-title">Informasi Transaksi</div>

    <div class="row">
        <?php if ($is_admin): ?>
            <div class="col">
                <p><strong>Nomor Transaksi:</strong> <?= esc($order['no_trx']) ?></p>
                <p><strong>Tanggal Transaksi:</strong> <?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                <p><strong>Jenis Pembayaran:</strong> <?= esc($order['payment_type'] ?? 'Tunai') ?></p>
                <p><strong>Petugas:</strong> <?= esc($order['username']) ?></p>
            </div>
            <div class="col">
                <p><strong>Customer:</strong> Umum</p>
                <p><strong>Nomor Telepon:</strong> <?= esc($order['phone_number']) ?></p>
                <p><strong>Alamat:</strong> <?= esc($order['address']) ?></p>
            </div>
        <?php else: ?>
            <div class="col">
                <p><strong>Nomor Transaksi:</strong> <?= esc($order['no_trx']) ?></p>
                <p><strong>Tanggal Transaksi:</strong> <?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                <p><strong>Status Transaksi:</strong> <?= esc($status) ?></p>
                <p><strong>Username:</strong> <?= esc($order['username']) ?></p>
            </div>
            <div class="col">
                <p><strong>Nama Penerima:</strong> <?= esc($order['recipient_name']) ?></p>
                <p><strong>Nomor Telepon:</strong> <?= esc($order['phone_number']) ?></p>
                <p><strong>Alamat:</strong> <?= esc($order['address']) ?></p>
            </div>
        <?php endif; ?>
    </div>


    <p><strong>Catatan:</strong> <?= esc($order['notes']) ?: '-' ?></p>

    <div class="section-title">Detail Pesanan</div>

    <table>
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= esc($item['medicine_name']) ?></td>
                    <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td><?= $item['qty'] ?></td>
                    <td>Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">
        <?php if ($is_admin): ?>
            <p><strong>Jumlah yang Dibayarkan:</strong> Rp <?= number_format($cash['paid_amount'] ?? 0, 0, ',', '.') ?></p>
            <p><strong>Kembalian:</strong> Rp <?= number_format($cash['change_amount'] ?? 0, 0, ',', '.') ?></p>
        <?php else: ?>
            <p><strong>Biaya Ongkir:</strong> Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></p>
        <?php endif; ?>
        <p><strong>PPN 11%:</strong> Rp <?= number_format($order['ppn'], 0, ',', '.') ?></p>
        <p><strong>Total Harga:</strong> Rp <?= number_format($order['total_price'], 0, ',', '.') ?></p>
    </div>

    <div class="footer">
        Barang yang sudah dibeli TIDAK BISA DITUKAR / DIKEMBALIKAN<br>
        Terima kasih telah berbelanja di Apotik Sentra Farma. Semoga lekas sehat!
    </div>

</body>

</html>