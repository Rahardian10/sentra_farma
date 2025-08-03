<h2>Aktivasi Akun Anda</h2>
<p>Halo <?= esc($user->fullname ?? $user->username) ?>,</p>

<p>Terima kasih telah mendaftar di Sentra Farma.</p>

<p>Silakan klik link di bawah ini untuk mengaktifkan akun Anda:</p>

<p>
    <a href="<?= site_url('activate-account/' . $hash) ?>">
        <?= site_url('activate-account/' . $hash) ?>
    </a>
</p>

<p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>