<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            background-color: #fff;
        }

        .invoice-box {
            width: 100%;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .header p {
            margin: 0;
            font-size: 12px;
            color: #777;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 18px;
            color: #2c3e50;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
            color: #888;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }

        .section-title {
            margin-top: 30px;
            font-weight: bold;
            font-size: 14px;
            color: #2c3e50;
        }

        .footer-note {
            margin-top: 40px;
            font-size: 11px;
            text-align: center;
            color: #777;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">

        <!-- HEADER -->
        <div class="header">
            <h1>Apotek Sentra Farma</h1>
            <p>Jl. Contoh Alamat No. 123, Jakarta | Telp: (021) 12345678</p>
        </div>

        <h2>INVOICE REFUND</h2>
        <div class="subtitle">Dokumen ini adalah bukti permintaan pengembalian dana</div>

        <table>
            <tr>
                <th>Nomor Transaksi</th>
                <td><?= esc($header['no_trx']) ?></td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td><?= date('d M Y H:i', strtotime($header['created_at'])) ?></td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td>Rp <?= number_format($header['total_price'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?= esc($header['status_name']) ?></td>
            </tr>
        </table>

        <div class="section-title">Informasi Pemohon</div>
        <table>
            <tr>
                <th>Nama</th>
                <td><?= esc($detail['name']) ?></td>
            </tr>
            <tr>
                <th>No. Telepon</th>
                <td><?= esc($detail['phone']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= esc($detail['email']) ?></td>
            </tr>
            <tr>
                <th>Bank</th>
                <td><?= esc($detail['bank_name']) ?></td>
            </tr>
            <tr>
                <th>No. Rekening</th>
                <td><?= esc($detail['bank_account']) ?></td>
            </tr>
            <tr>
                <th>Alasan Refund</th>
                <td><?= esc($detail['reason']) ?></td>
            </tr>
        </table>

        <?php if ($approval && !empty($approval['rejection_reason'])): ?>
            <div class="section-title">Alasan Penolakan</div>
            <p><?= esc($approval['rejection_reason']) ?></p>
        <?php endif; ?>

        <div class="footer-note">
            Invoice ini dicetak dari sistem <strong>Apotek Sentra Farma</strong>. Harap simpan sebagai bukti permintaan refund.
        </div>
    </div>
</body>

</html>