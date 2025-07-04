<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $title; ?></title>

    <!-- Custom fonts for this template-->
    <link rel="icon" type="image/png" href="<?= base_url(); ?>/logo.png" sizes="32x32" />
    <link href="<?= base_url(); ?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url(); ?>/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= base_url(); ?>/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?= $this->include('templates/sidebar'); ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?= $this->include('templates/topbar'); ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <?= $this->renderSection('page-content'); ?>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website <?= date('Y'); ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?= base_url('logout'); ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <?= $this->renderSection('custom-js'); ?>
    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url(); ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url(); ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url(); ?>/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url(); ?>/js/sb-admin-2.min.js"></script>
    <!-- Page level plugins -->
    <script src="<?= base_url(); ?>/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?= base_url(); ?>/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="<?= base_url(); ?>/js/demo/datatables-demo.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            $('#substance').select2({
                placeholder: "-- Pilih Zat Aktif --",
                allowClear: true
            });

            $('#md_id').select2({
                placeholder: "-- Pilih Zat Aktif --",
                allowClear: true
            });

            //form add stockout select2
            $('#trx_type').select2({
                placeholder: "-- Pilih Jenis Transaksi --",
                allowClear: true
            });

            $('#preparation_by').select2({
                placeholder: "-- Disiapkan Oleh --",
                allowClear: true
            });

            $('.medicine-select').select2({
                placeholder: "-- Pilih --",
                allowClear: true
            });

            // start Select2 untuk kolom area checkout
            $('#area_co').select2({
                placeholder: "-- Pilih --",
                allowClear: true
            });
            //end

            // Function load stok & satuan
            function loadStockAndUnit(row, medicineId, selectedOption) {
                // Ambil satuan dari attribute option
                var unit = selectedOption.data('unit');
                row.find('.unit_now').val(unit);

                // Ambil stok dari server pakai Ajax
                $.ajax({
                    url: '<?= base_url('get-stok-obat') ?>', // route ke controller
                    type: 'GET',
                    data: {
                        id: medicineId
                    },
                    dataType: 'json',
                    success: function(response) {
                        row.find('.stock_now').val(response.total_qty);
                    },
                    error: function() {
                        alert('Gagal mengambil stok!');
                    }
                });
            }

            // Saat dropdown Nama Obat berubah
            $('#stockTable').on('change', '.medicine-select', function() {
                var row = $(this).closest('tr');
                var medicineId = $(this).val();
                var selectedOption = $(this).find(':selected');
                loadStockAndUnit(row, medicineId, selectedOption);
            });

            //stockout
            $('#stockTableOut').on('change', '.medicine-select-Out', function() {
                var row = $(this).closest('tr');
                var medicineId = $(this).val();
                var selectedOption = $(this).find(':selected');
                loadStockAndUnit(row, medicineId, selectedOption);
            });

            // Button Tambah Barang
            // 1️⃣ Ambil data dari controller (medicineList)
            var medicineListData = <?= isset($medicineList) ? json_encode($medicineList) : '[]'; ?>;

            // 2️⃣ Generate options
            var options = '<option value="" selected disabled>-- Pilih --</option>';
            medicineListData.forEach(function(item) {
                options += `<option value="${item.id}" data-unit="${item.unit}" data-stock="${item.stock_qty}">${item.name}</option>`;
            });

            // 3️⃣ Isi select default (row pertama) sebelum select2 init
            $('#medicineSelectDefault').html(options);

            // 4️⃣ Init select2 di row pertama
            // $('#medicineSelectDefault').select2({
            //     placeholder: "-- Pilih --",
            //     allowClear: true
            // });

            // 5️⃣ Setup tambah row baru
            var rowIdx = 1;
            $('#addRow').click(function() {
                var newRow = `<tr>
<td>
    <select class="form-control select2 medicine-select" name="items[${rowIdx}][md_id]" style="width: 100%;">
        ${options}
    </select>
</td>
<td><input type="text" class="form-control stock_now" name="items[${rowIdx}][stock_now]" readonly></td>
<td><input type="text" class="form-control unit_now" name="items[${rowIdx}][unit]" readonly></td>
<td><input type="number" class="form-control" name="items[${rowIdx}][stock_qty]"></td>
<td><input type="text" class="form-control" name="items[${rowIdx}][unit_price]"></td>
<td><input type="date" class="form-control" name="items[${rowIdx}][expire_date]"></td>
<td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
</tr>`;

                $('#stockTable tbody').append(newRow);
                rowIdx++;

                // Re-init select2
                $('.medicine-select').select2({
                    placeholder: "-- Pilih --",
                    allowClear: true
                });

                // ⬇️ Panggil validasi duplikat option
                disableSelectedOptions();
            });

            // untuk stockout
            $('#addRowOut').click(function() {
                var newRowOut = `<tr>
<td>
    <select class="form-control select2 medicine-select-Out" name="items[${rowIdx}][md_id]" style="width: 100%;">
        ${options}
    </select>
</td>
<td><input type="text" class="form-control stock_now" name="items[${rowIdx}][stock_now]" readonly></td>
<td><input type="text" class="form-control unit_now" name="items[${rowIdx}][unit]" readonly></td>
<td><input type="number" class="form-control" name="items[${rowIdx}][stock_qty]"></td>
<td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
</tr>`;

                $('#stockTableOut tbody').append(newRowOut);
                rowIdx++;

                // Re-init select2
                $('.medicine-select-Out').select2({
                    placeholder: "-- Pilih --",
                    allowClear: true
                });

                // ⬇️ Panggil validasi duplikat option
                disableSelectedOptions();
            });


            // ✅ Function anti duplikat option
            function disableSelectedOptions() {
                let selectedValues = [];

                // Ambil semua yang dipilih di setiap select (medicine-select-Out)
                $('.medicine-select-Out').each(function() {
                    let val = $(this).val();
                    if (val) {
                        selectedValues.push(val);
                    }
                });

                // stockin
                $('.medicine-select').each(function() {
                    let val = $(this).val();
                    if (val) {
                        selectedValues.push(val);
                    }
                });

                // Loop semua select stockout
                $('.medicine-select-Out').each(function() {
                    let $select = $(this);

                    $select.find('option').each(function() {
                        let optionVal = $(this).val();

                        // Disable kalau sudah dipilih di select lain
                        if (selectedValues.includes(optionVal) && optionVal !== $select.val()) {
                            $(this).attr('disabled', 'disabled');
                        } else {
                            $(this).removeAttr('disabled');
                        }
                    });

                    // Refresh Select2
                    $select.select2();
                });
                // Loop semua select stockin
                $('.medicine-select').each(function() {
                    let $select = $(this);

                    $select.find('option').each(function() {
                        let optionVal = $(this).val();

                        // Disable kalau sudah dipilih di select lain
                        if (selectedValues.includes(optionVal) && optionVal !== $select.val()) {
                            $(this).attr('disabled', 'disabled');
                        } else {
                            $(this).removeAttr('disabled');
                        }
                    });

                    // Refresh Select2
                    $select.select2();
                });
            }

            // ✅ Panggil pertama kali saat halaman load
            disableSelectedOptions();

            // ✅ Panggil saat user ganti select
            $(document).on('change', '.medicine-select-Out', function() {
                disableSelectedOptions();
            });
            // ✅ Panggil saat user ganti select stockin
            $(document).on('change', '.medicine-select', function() {
                disableSelectedOptions();
            });



            // Button Hapus Baris
            $('#stockTable').on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
            // Button Hapus Baris - stockout
            $('#stockTableOut').on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });

            // view log total stok
            $(document).on('click', '.view-log', function() {
                var medicineId = $(this).data('id');

                $.ajax({
                    url: '<?= base_url('getlogs') ?>/' + medicineId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var html = '<table class="table table-bordered">';
                        html += '<tr><th>Tanggal Submit</th><th>Jumlah</th><th>Jenis</th><th>Petugas</th></tr>';

                        response.forEach(function(item) {
                            let jenis = '';
                            if (item.stockin_id) {
                                jenis = '+ (Stock In)';
                            } else if (item.stockout_id) {
                                jenis = '- (Stock Out)';
                            } else if (item.checkout_id) {
                                jenis = '- (Checkout)';
                            } else if (item.refund_id) {
                                jenis = '+ (Refund)';
                            }

                            html += `<tr>
                    <td>${item.updated_at}</td>
                    <td>${item.stock_qty}</td>
                    <td>${jenis}</td>
                    <td>${item.fullname ?? '-'}</td>
                </tr>`;
                        });

                        html += '</table>';
                        $('#exampleModal .modal-body').html(html);
                    },
                    error: function() {
                        $('#exampleModal .modal-body').html('<p class="text-danger">Gagal memuat data log.</p>');
                    }
                });
            });




            // !--Live Search Script-- >
            $('#searchInput').on('keyup', function() {
                let filter = $(this).val().toLowerCase();
                $('.medicine-card').each(function() {
                    let title = $(this).find('.card-title').text().toLowerCase();
                    $(this).toggle(title.includes(filter));
                });
            });

            // === Load More Button ===
            let offset = 12; // Awalnya sudah tampil 12
            const limit = 12;

            document.getElementById('loadMoreBtn').addEventListener('click', function() {
                loadMore();
            });

            function loadMore() {
                document.getElementById('loader').style.display = 'block';
                document.getElementById('loadMoreBtn').disabled = true; // Disable sementara tombol

                fetch(`<?= base_url('medicine/loadMore') ?>?limit=${limit}&offset=${offset}`)
                    .then(response => response.json())
                    .then(data => {
                        let container = document.getElementById('medicineContainer');

                        if (data.length === 0) {
                            document.getElementById('loadMoreBtn').style.display = 'none';
                            document.getElementById('endMessage').style.display = 'block';
                        } else {
                            data.forEach(mdlist => {
                                const medicineData = {
                                    id: mdlist.id,
                                    name: mdlist.name,
                                    price: mdlist.price,
                                    stock: mdlist.qty,
                                    description: mdlist.other_data,
                                    image: mdlist.medicine_pict,
                                    image_url: "<?= base_url('uploads/medicine/') ?>" + mdlist.medicine_pict
                                };

                                let card = `
    <div class="col-md-4 mb-4 medicine-card">
        <div class="card" style="width: 100%;">
            <img src="${medicineData.image_url}" class="card-img-top" alt="${medicineData.name}" width="100">
            <div class="card-body">
                <h5 class="card-title"><b>${medicineData.name}</b></h5>
                ${medicineData.discount_price && medicineData.discount_price > medicineData.price
                    ? `<p class="card-text mb-1"><del class="text-danger">Rp ${Number(medicineData.discount_price).toLocaleString('id-ID')}</del></p>`
                    : ''
                }
                <p class="card-text font-weight-bold text-success">Rp ${Number(medicineData.price).toLocaleString('id-ID')}</p>
                <a href="javascript:void(0)" class="btn btn-primary"
                   onclick='showMedicineDetail(${JSON.stringify(medicineData)})'>
                    Detail
                </a>
            </div>
        </div>
    </div>
`;


                                container.insertAdjacentHTML('beforeend', card);
                            });

                            offset += limit;

                            // ✅ Jika data yang didapat LEBIH KECIL dari limit, artinya sudah habis
                            if (data.length < limit) {
                                document.getElementById('loadMoreBtn').style.display = 'none';
                                document.getElementById('endMessage').style.display = 'block';
                            } else {
                                document.getElementById('loadMoreBtn').disabled = false; // Aktifkan lagi tombol
                            }
                        }

                        document.getElementById('loader').style.display = 'none';
                    });
            }

            // Modal Katalog - input quantity
            $('#modalQuantity').on('input', function() {
                const stok = parseInt($('#modalMedicineStockValue').val());
                const qty = parseInt($(this).val());

                if (qty > stok) {
                    $('#quantityError').removeClass('d-none');
                    $('#addToCartBtn').prop('disabled', true);
                } else {
                    $('#quantityError').addClass('d-none');
                    $('#addToCartBtn').prop('disabled', false);
                }
            });

            $(document).on('click', '.show-detail-btn', function() {
                const data = $(this).data('medicine');
                showMedicineDetail(data);
            });

            // end js 1
        });

        // Proses menampilkan data di modal katalog    
        function showMedicineDetail(data) {
            $('#modalImage').attr('src', data.image_url);
            $('#modalName').text(data.name);
            $('#modalPrice').text('Rp ' + data.price.toLocaleString('id-ID'));
            $('#modalStock').text(data.stock); // <- sebelumnya salah: data.qty
            $('#modalDescription').text(data.description); // <- sebelumnya salah: data.other_data

            $('#modalMedicineId').val(data.id);
            $('#modalMedicineName').val(data.name);
            $('#modalMedicinePriceValue').val(data.price);
            $('#modalMedicineImageValue').val(data.image);
            $('#modalMedicineStockValue').val(data.stock); // <- sebelumnya salah: data.qty
            $('#modalQuantity').val(1); // ✅ inisialisasi value

            $('#quantityError').addClass('d-none');
            $('#addToCartBtn').prop('disabled', false);
            $('#detailModal').modal('show');
        }

        // update quantity cart
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.increase').forEach(button => {
                button.addEventListener('click', function() {
                    const medicineId = this.dataset.medicineId;
                    const stock = parseInt(this.dataset.stock);
                    const quantityInput = this.closest('.d-flex').querySelector('.quantity-input');
                    let quantity = parseInt(quantityInput.value);

                    // Pastikan tidak melebihi stok
                    if (quantity < stock) {
                        quantity += 1;
                        quantityInput.value = quantity;
                        updateTotal(medicineId, quantity); // Update total harga
                        updateCartQuantity(medicineId, quantity); // Update database
                    }
                });
            });

            document.querySelectorAll('.decrease').forEach(button => {
                button.addEventListener('click', function() {
                    const medicineId = this.dataset.medicineId;
                    const quantityInput = this.closest('.d-flex').querySelector('.quantity-input');
                    let quantity = parseInt(quantityInput.value);

                    // Pastikan tidak kurang dari 1
                    if (quantity > 1) {
                        quantity -= 1;
                        quantityInput.value = quantity;
                        updateTotal(medicineId, quantity); // Update total harga
                        updateCartQuantity(medicineId, quantity); // Update database
                    }
                });
            });

            // Fungsi untuk update total harga
            function updateTotal(medicineId, quantity) {
                const price = document.querySelector(`[data-medicine-id="${medicineId}"]`).dataset.price;
                const totalHarga = quantity * price;
                const totalHargaElement = document.querySelector(`[data-medicine-id="${medicineId}"]`).closest('.row').querySelector('.total-harga');

                totalHargaElement.innerHTML = 'Rp ' + totalHarga.toLocaleString();
            }

            // Fungsi untuk update data di server
            function updateCartQuantity(medicineId, quantity) {
                fetch("<?= base_url('cart/ajax-update') ?>", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': "<?= csrf_hash() ?>"
                        },
                        body: new URLSearchParams({
                            medicine_id: medicineId,
                            quantity: quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload();
                            console.log("Update success");
                        } else {
                            console.error("Update failed", data.message);
                        }
                    })
                    .catch(err => {
                        console.error("Error:", err);
                    });
            }
        });

        // filter cart/keranjang
        function filterCartItems() {
            const searchInput = document.getElementById('searchInputCard').value.toLowerCase();
            const cartItems = document.querySelectorAll('.cart-item');

            cartItems.forEach(item => {
                const itemName = item.getAttribute('data-item-name');
                if (itemName.includes(searchInput)) {
                    item.style.display = 'flex'; // Tampilkan item jika cocok
                } else {
                    item.style.display = 'none'; // Sembunyikan item jika tidak cocok
                }
            });
        }

        //JS untuk nambah biaya ongkir di halaman keranjang
        $(document).ready(function() {
            function parseRupiah(str) {
                return parseInt(str.replace(/[^0-9]/g, '')) || 0;
            }

            var $areaSelect = $('#area_co');
            var $shippingCost = $('#shippingCost');
            var $grandTotal = $('#grandTotal');
            var $cartTotal = $('#cartTotal');

            var cartTotalVal = parseRupiah($cartTotal.text());

            $areaSelect.on('change', function() {
                var shippingPrice = parseInt($(this).find('option:selected').data('price')) || 0;
                var newGrandTotal = cartTotalVal + shippingPrice;

                $shippingCost.text('Rp ' + shippingPrice.toLocaleString('id-ID'));
                $grandTotal.text('Rp ' + newGrandTotal.toLocaleString('id-ID'));

                $('#shipping_price_input').val(shippingPrice);
                $('#grand_total_input').val(newGrandTotal);
            });

            $areaSelect.trigger('change');
        });

        //Filter pencarian menu transaksi
        $(document).ready(function() {
            $('#searchInputOrder').on('keyup', function() {
                const keyword = $(this).val().toLowerCase();

                $('.transaction-card').each(function() {
                    const trxNo = $(this).data('trx');
                    if (trxNo.includes(keyword)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });

        //Filter pencarian menu list pesanan
        $(document).ready(function() {
            $('#searchInputTransaction').on('keyup', function() {
                const keyword = $(this).val().toLowerCase();

                $('.transaction-card').each(function() {
                    const trxNo = $(this).data('trx');
                    if (trxNo.includes(keyword)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });

        // JS untuk cancel transaksi
        $(document).ready(function() {
            $('.cancel-order-btn').on('click', function() {
                var orderId = $(this).data('id');
                $('#cancelOrderForm').attr('action', '/transaction/confirm/' + orderId);
            });
        });

        //JS untuk halaman edit menu persetujuan refund - hidden form ubah status
        $(document).ready(function() {
            const $statusSelect = $('#status_refund');
            const $uploadSection = $('#uploadSection');
            const $reasonSection = $('#reasonSection');

            function toggleSections() {
                const selected = parseInt($statusSelect.val());

                $uploadSection.addClass('d-none');
                $reasonSection.addClass('d-none');

                if (selected === 2) {
                    $uploadSection.removeClass('d-none');
                } else if (selected === 3) {
                    $reasonSection.removeClass('d-none');
                }
            }

            $statusSelect.on('change', toggleSections);

            // Jalankan sekali saat halaman dimuat, untuk kasus edit data
            toggleSections();
        });

        //validasi halaman checkout admin untuk pengurangan kembalian
        $(document).ready(function() {
            <?php if (in_groups('Admin')): ?>
                const grandTotal = parseInt($('#grand_total_input').val());

                function formatNumber(number) {
                    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }

                function parseNumber(str) {
                    return parseInt(str.replace(/\./g, '')) || 0;
                }

                function validatePayment(amount) {
                    if (amount >= grandTotal) {
                        $('#checkoutBtn').prop('disabled', false);
                    } else {
                        $('#checkoutBtn').prop('disabled', true);
                    }
                }

                $('#paid_display').on('input', function() {
                    let input = $(this).val().replace(/[^\d]/g, '');
                    let numericValue = parseInt(input) || 0;
                    $(this).val(formatNumber(numericValue));
                    $('#paid_amount').val(numericValue);

                    let change = numericValue - grandTotal;

                    $('#change_amount_display').val(
                        new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(change >= 0 ? change : 0)
                    );

                    $('#change_amount').val(change >= 0 ? change : 0);
                    validatePayment(numericValue);
                });


                // Inisialisasi awal: disable jika belum ada input
                validatePayment(0);
            <?php endif; ?>
        });

        //JS untuk grafik dashboard Admin
        <?php if (isset($grafikPendapatan)): ?>
            const labels = <?= json_encode(array_keys($grafikPendapatan)) ?>; // ['2024-06', '2024-07', ..., '2025-05']
            const data = <?= json_encode(array_values($grafikPendapatan)) ?>;

            // Kalau mau tampilkan label bulan dalam format "Jun 2024", bisa proses di JS sbb:
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            const formattedLabels = labels.map(l => {
                const parts = l.split('-'); // ['2024', '06']
                const y = parts[0];
                const m = parseInt(parts[1], 10) - 1;
                return monthNames[m] + ' ' + y;
            });

            // Chart.js config
            const ctx = document.getElementById('incomeChart').getContext('2d');
            const incomeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: formattedLabels,
                    datasets: [{
                        label: 'Pendapatan Per Bulan',
                        data: data,
                        borderColor: 'rgba(78, 115, 223, 1)',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                // format angka pakai ribuan separator
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

        <?php endif; ?>
    </script>
</body>

</html>