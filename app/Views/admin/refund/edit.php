<?= $this->extend('templates/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Pengajuan Refund</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="post" action="<?= base_url('update/refund/' . $refundHeader['id']); ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="no_trx">Nomor Transaksi</label>
                    <input type="text" class="form-control" name="no_trx" id="no_trx" value="<?= esc($refundHeader['no_trx']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" name="name" class="form-control" id="name" value="<?= esc($refundDetail['name'] ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="text" name="phone" class="form-control" id="phone" value="<?= esc($refundDetail['phone'] ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" id="email" value="<?= esc($refundDetail['email'] ?? user()->email) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="bank">Nama Bank</label>
                    <select name="bank_id" id="bank" class="form-control" disabled>
                        <option value="">-- Pilih Bank --</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?= $bank['id'] ?>" <?= $refundDetail['bank_id'] == $bank['id'] ? 'selected' : '' ?>>
                                <?= esc($bank['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="bank_account">Nomor Rekening</label>
                    <input type="text" name="bank_account" class="form-control" id="bank_account" value="<?= esc($refundDetail['bank_account'] ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="reason">Alasan Refund</label>
                    <textarea name="reason" class="form-control" id="reason" rows="4" readonly><?= esc($refundDetail['reason'] ?? '') ?></textarea>
                </div>

                <?php
                $statusId = $refundHeader['status'];
                $isFinal = in_array($statusId, [2, 3, 4]); // status yang dianggap final
                ?>

                <?php if (!$isFinal): ?>
                    <!-- Dropdown status hanya muncul jika belum final -->
                    <div class="form-group">
                        <label for="status_refund">Status</label>
                        <select name="status_refund" id="status_refund" class="form-control">
                            <option value="">-- Pilih Status --</option>
                            <?php foreach ($statusList as $status): ?>
                                <?php if (in_array($status['id'], [1, 4])) continue; ?>
                                <option value="<?= $status['id'] ?>" <?= $status['id'] == $statusId ? 'selected' : '' ?>>
                                    <?= esc($status['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group d-none" id="uploadSection">
                        <label for="evidence_refund">Upload Bukti Transfer</label>
                        <input type="file" name="evidence_refund" id="evidence_refund" class="form-control">
                    </div>

                    <div class="form-group d-none" id="reasonSection">
                        <label for="reject_reason">Alasan Penolakan</label>
                        <textarea name="reject_reason" id="reject_reason" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                <?php else: ?>
                    <!-- Status sudah final -->
                    <div class="form-group">
                        <label>Status</label>
                        <input type="text" class="form-control" value="<?= esc($refundHeader['status_label']) ?>" readonly>
                    </div>

                    <?php if (in_array($statusId, [2, 4]) && !empty($refundApproval['evidence_refund'])): ?>
                        <div class="form-group">
                            <label>Bukti Transfer</label><br>
                            <img src="<?= base_url('uploads/refund_evidence/' . $refundApproval['evidence_refund']) ?>" alt="Bukti Transfer" class="img-fluid img-thumbnail" style="max-width: 300px;">
                        </div>
                    <?php endif; ?>

                    <?php if ($statusId == 3 && !empty($refundApproval['reject_reason'])): ?>
                        <div class="form-group">
                            <label>Alasan Penolakan</label>
                            <textarea class="form-control" rows="3" readonly><?= esc($refundApproval['reject_reason']) ?></textarea>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </form>
        </div>
    </div>

    <!-- Log Status -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Status Refund</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logStatus as $log): ?>
                            <tr>
                                <td><?= esc($log['status_label']) ?></td>
                                <td><?= esc($log['created_at']) ?></td>
                                <td><?= esc($log['updated_by_name'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logStatus)): ?>
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada riwayat status.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>