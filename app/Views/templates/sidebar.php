<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?= current_url() == site_url('dashboard') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">


    <?php if (in_groups('Admin')): ?>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Nav Item - User List -->
        <div class="sidebar-heading">
            Manajemen Obat
        </div>

        <li class="nav-item <?= (current_url() == base_url('medicinelist') || current_url() == base_url('add/medicine')) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('medicinelist'); ?>">
                <i class="fa fa-medkit"></i>
                <span>List Obat</span></a>
        </li>

        <li class="nav-item <?= (current_url() == base_url('medicinestockin') || current_url() == base_url('add/medicinestockin') || strpos(current_url(), base_url('edit/medicinestockin')) !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('medicinestockin'); ?>">
                <i class="fa fa-th-large"></i>
                <span>Obat Masuk</span></a>
        </li>

        <li class="nav-item <?= (current_url() == base_url('medicinestockout') || current_url() == base_url('add/medicinestockout') || strpos(current_url(), base_url('edit/medicinestockout')) !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('medicinestockout'); ?>">
                <i class="fa fa-outdent"></i>
                <span>Obat Keluar</span></a>
        </li>

        <li class="nav-item <?= (current_url() == base_url('all-stock')) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('all-stock'); ?>">
                <i class="fa fa-globe"></i>
                <span>Total Stok Obat</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Manajemen Admin / Petugas
        </div>

        <!-- Nav Item - Pages Collapse Menu -->
        <?php
        $isActive = (current_url() == base_url('admin')) ? 'active' : '';
        $isShow = (current_url() == base_url('admin')) ? 'show' : '';
        $isExpanded = (current_url() == base_url('admin')) ? 'true' : 'false';
        ?>

        <li class="nav-item">
            <a class="nav-link collapsed <?= $isActive; ?>" href="#"
                data-toggle="collapse"
                data-target="#collapseTwo"
                aria-expanded="<?= $isExpanded; ?>"
                aria-controls="collapseTwo">
                <i class="fas fa-fw fa-cog"></i>
                <span>List Admin / Petugas</span>
            </a>
            <div id="collapseTwo" class="collapse <?= $isShow; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?= $isActive; ?>" href="<?= base_url('admin'); ?>">List Data</a>
                </div>
            </div>
        </li>
    <?php endif; ?>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        E - Commerce
    </div>

    <li class="nav-item <?= (current_url() == base_url('catalog')) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?= base_url('catalog'); ?>">
            <i class="fa fa-plus-square"></i>
            <span>Katalog Obat</span></a>
    </li>

    <li class="nav-item <?= (current_url() == base_url('cart') || current_url() == base_url('checkout')) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?= base_url('cart'); ?>">
            <i class="fa fa-shopping-cart"></i>
            <span>Keranjang</span></a>
    </li>

    <?php
    $uri = service('uri');
    $segment1 = $uri->getSegment(1); // 'transaction'
    ?>

    <li class="nav-item <?= ($segment1 == 'transaction') ? 'active' : ''; ?>">
        <a class="nav-link" href="<?= base_url('transaction'); ?>">
            <i class="fa fa-credit-card"></i>
            <span>Transaksi</span></a>
    </li>

    <?php if (in_groups('Admin')): ?>
        <li class="nav-item <?= (current_url() == base_url('order') || strpos(current_url(), base_url('order/detail')) !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('order'); ?>">
                <i class="fa fa-archive"></i>
                <span>List Pesanan</span></a>
        </li>

        <li class="nav-item <?= (current_url() == base_url('refund') || strpos(current_url(), base_url('edit/refund')) !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('refund'); ?>">
                <i class="fa fa-retweet"></i>
                <span>Persetujuan Refund</span></a>
        </li>
    <?php endif; ?>

    <?php if (in_groups('User')): ?>
        <li class="nav-item <?= (current_url() == base_url('req_refund') || current_url() == base_url('form_refund') || strpos(current_url(), base_url('detail/refund')) !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('req_refund'); ?>">
                <i class="fa fa-undo"></i>
                <span>Pengajuan Refund</span></a>
        </li>
    <?php endif; ?>

    <!-- Heading -->
    <div class="sidebar-heading">
        Logout
    </div>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('logout'); ?>">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span></a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>