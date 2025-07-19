<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Area;
use App\Models\LogStatusTrx;
use App\Models\Bank;
use App\Models\TransHRefund;
use App\Models\TransDRefund;
use App\Models\TotalMdStock;
use App\Models\TransHOrder;
use App\Models\LogStatusRefund;
use App\Models\TransDOrder;
use Dompdf\Dompdf;

class UserController extends BaseController
{
    protected $db, $builder;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('users');
    }

    private function generateUniqueId($model, $min = 100000, $max = 999999)
    {
        do {
            $randomId = random_int($min, $max);
        } while ($model->find($randomId)); // Ulangi kalau ID sudah ada

        return $randomId;
    }

    public function index()
    {
        $data['title'] = 'Katalog Obat';

        $db = \Config\Database::connect();
        $builder = $db->table('medicine_list');
        $builder->select('medicine_list.*, total_md_stock.qty');
        $builder->join('total_md_stock', 'total_md_stock.medicine_id = medicine_list.id', 'left');
        $builder->where('medicine_list.ecatalog', 'yes');
        $builder->where('total_md_stock.qty IS NOT NULL');
        $builder->where('total_md_stock.qty >', 0); // Optional: kalau mau pastikan stok > 0
        $builder->orderBy('medicine_list.id', 'desc');
        $builder->limit(12); // Tambahan supaya tampilan awal 12 data
        $query = $builder->get();

        $data['medicine_list'] = $query->getResult();

        return view('user/sales/catalog', $data);
    }
    public function loadMoreMedicines()
    {
        $limit = $this->request->getGet('limit') ?? 12;
        $offset = $this->request->getGet('offset') ?? 0;

        $db = \Config\Database::connect();
        $builder = $db->table('medicine_list');
        $builder->select('medicine_list.*, total_md_stock.qty');
        $builder->join('total_md_stock', 'total_md_stock.medicine_id = medicine_list.id', 'left');
        $builder->where('medicine_list.ecatalog', 'yes');
        $builder->where('total_md_stock.qty IS NOT NULL');
        $builder->where('total_md_stock.qty >', 0);
        $builder->orderBy('medicine_list.id', 'desc');
        $builder->limit($limit, $offset);

        $query = $builder->get();
        $data = $query->getResult();
        // dd($data);

        return $this->response->setJSON($data);
    }

    public function add_cart()
    {
        $db = \Config\Database::connect();
        $cartTable = $db->table('cart');
        $stockTable = $db->table('total_md_stock');

        // Ambil user ID dari session
        $userId = user()->id; // Pastikan session login menyimpan user_id

        if (!$userId) {
            return redirect()->back()->with('error', 'User tidak dikenali. Harap login.');
        }

        $medicineId = $this->request->getPost('medicine_id');
        $qty = (int) $this->request->getPost('quantity');

        // Ambil stok dari tabel total_md_stock berdasarkan medicine_id
        $stockRow = $stockTable->getWhere(['medicine_id' => $medicineId])->getRow();

        if (!$stockRow) {
            return redirect()->back()->with('error', 'Stok obat tidak ditemukan.');
        }

        $stok = (int) $stockRow->qty;

        if ($qty > $stok) {
            return redirect()->back()->with('error', 'Jumlah melebihi stok tersedia.');
        }

        // Cek apakah user sudah memiliki item ini di keranjang
        $existingCartItem = $cartTable
            ->where('medicine_id', $medicineId)
            ->where('user_id', $userId)
            ->get()
            ->getRow();

        if ($existingCartItem) {
            $newQty = $existingCartItem->qty + $qty;

            if ($newQty > $stok) {
                return redirect()->back()->with('error', 'Jumlah total melebihi stok.');
            }

            $updateData = [
                'qty' => $newQty
            ];

            $cartTable
                ->where('medicine_id', $medicineId)
                ->where('user_id', $userId)
                ->update($updateData);

            return redirect()->back()->with('success', 'Jumlah obat di keranjang diperbarui.');
        } else {
            $data = [
                'user_id'         => $userId,
                'medicine_id'     => $medicineId,
                'medicine_name'   => $this->request->getPost('medicine_name'),
                'price'           => $this->request->getPost('price'),
                'medicine_image'  => $this->request->getPost('medicine_image'),
                'qty'             => $qty
            ];

            $cartTable->insert($data);

            return redirect()->back()->with('success', 'Obat ditambahkan ke keranjang.');
        }
    }

    public function cart()
    {
        $db = \Config\Database::connect();
        $userId = user()->id;

        // Ambil data dari tabel cart berdasarkan user
        $cartData = $db->table('cart')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();

        $cartItems = [];
        $totalAmount = 0;

        foreach ($cartData as $item) {
            // Ambil stok dari total_md_stock berdasarkan medicine_id
            $stockRow = $db->table('total_md_stock')
                ->where('medicine_id', $item['medicine_id'])
                ->get()
                ->getRow();

            $availableStock = $stockRow ? (int)$stockRow->qty : 0;

            $cartItems[] = [
                'id'            => $item['id'],
                'medicine_id'   => $item['medicine_id'],
                'name'          => $item['medicine_name'],
                'price'         => $item['price'],
                'quantity'      => $item['qty'],
                'image'         => $item['medicine_image'],
                'stock'         => $availableStock, // Tambahan info stok
            ];

            $totalAmount += $item['price'] * $item['qty'];
        }

        $data = [
            'title' => 'Keranjang Obat',
            'cart_items' => $cartItems,
            'total_amount' => $totalAmount
        ];

        return view('user/sales/cart', $data);
    }

    public function remove($medicineId)
    {
        $db = \Config\Database::connect();
        $userId = user()->id;

        // Hapus item di keranjang milik user ini berdasarkan medicine_id
        $deleted = $db->table('cart')
            ->where('medicine_id', $medicineId)
            ->where('user_id', $userId)
            ->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus item dari keranjang.');
        }
    }

    public function ajaxUpdate()
    {
        $medicineId = $this->request->getPost('medicine_id');
        $quantity = $this->request->getPost('quantity');

        // Update cart
        $db = \Config\Database::connect();
        $builder = $db->table('cart');
        $builder->where('medicine_id', $medicineId)
            ->where('user_id', user()->id)
            ->update(['qty' => $quantity]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Cart updated successfully'
        ]);
    }

    public function checkout()
    {
        $data['title'] = 'Checkout';
        $userId = user()->id;

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $cartModel = new Cart();
        $areaModel = new Area();

        $data['cartItems'] = $cartModel->where('user_id', $userId)->findAll();
        $data['areas'] = $areaModel->findAll(); // ambil semua data area

        return view('user/sales/checkout', $data);
    }

    public function transaction()
    {
        $userId = user()->id;
        $db = \Config\Database::connect();

        // Ambil semua transaksi dari trans_h_order
        $builder = $db->table('trans_h_order as tho');
        $builder->select('tho.*, st.id as status_id, st.name as status_name');
        $builder->join('status_trx as st', 'st.id = tho.status', 'left'); // left join agar data tetap muncul walau status null
        $builder->join('users', 'users.id = tho.userid', 'left');
        $builder->where('userid', $userId);
        $builder->orderBy('tho.created_at', 'DESC');
        $headers = $builder->get()->getResultArray();

        $transactions = [];

        foreach ($headers as $header) {
            // Ambil detail item dari trans_d_order yang terkait dengan transaksi ini
            $details = $db->table('trans_d_order')
                ->where('trans_h_orderid', $header['id'])
                ->get()
                ->getResultArray();

            $transactions[] = [
                'id' => $header['id'],
                'transaction_number' => $header['no_trx'],
                'userid'             => $header['userid'],
                'username'           => $header['username'],
                'recipient_name'     => $header['recipient_name'],
                'phone_number'       => $header['phone_number'],
                'address'            => $header['address'],
                'city'               => $header['city'],
                'area'               => $header['area'],
                'notes'              => $header['notes'],
                'payment_file'       => $header['payment_file'],
                'total_price'        => $header['total_price'],
                'status_id'             => $header['status_id'],
                'status'             => $header['status_name'],
                'platform'             => $header['platform'],
                'created_at'         => $header['created_at'],
                'updated_at'         => $header['updated_at'],
                'updated_by'         => $header['updated_by'],
                'items'              => $details,
            ];
        }

        // Kirim data ke view dengan array $data
        $data = [
            'title'        => 'List Transaksi',
            'transactions' => $transactions,
        ];

        return view('user/sales/transaction', $data);
    }

    public function process_trx()
    {
        $userId = user()->id;
        $username = user()->username;
        $isAdmin = in_groups('Admin');
        $modelStock = new \App\Models\TotalMdStock();
        $cartModel = new \App\Models\Cart();
        $cartItems = $cartModel->where('user_id', $userId)->findAll();

        if (!$cartItems || empty($cartItems)) {
            return redirect()->back()->with('error', 'Keranjang kosong!');
        }

        $rules = [
            'full_name' => 'required',
            'phone'     => 'required',
            'address'   => 'required',
        ];

        if (!$isAdmin) {
            $rules['area'] = 'required';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $validation->getErrors()));
        }
        if (!$isAdmin) {
            $paidFile = $this->request->getFile('paid');
            $paidName = $paidFile->getRandomName();
            $paidFile->move('uploads/bukti/', $paidName);
        } else {
            $paidName = null;
        }

        $modelH = new \App\Models\TransHOrder();
        $modelD = new \App\Models\TransDOrder();
        $modelLog = new \App\Models\LogStatusTrx(); // Tambahkan ini

        $noTrx = 'TRX' . date('YmdHis') . rand(100, 999);
        $totalPrice = $this->request->getPost('grand_total_with_ppn_input');
        $shippingCost = $this->request->getPost('shipping_price_input');
        $ppn = $this->request->getPost('ppn_input');
        // dd($totalPrice);

        $headerData = [
            'no_trx'         => $noTrx,
            'userid'         => $userId,
            'username'       => $username,
            'recipient_name' => $this->request->getPost('full_name'),
            'phone_number'   => $this->request->getPost('phone'),
            'address'        => $this->request->getPost('address'),
            'city'           => $this->request->getPost('city'),
            'area'           => $this->request->getPost('area'),
            'notes'          => $this->request->getPost('note'),
            'payment_file'   => $paidName,
            'total_price'    => $totalPrice,
            'shipping_cost'  => $shippingCost,
            'ppn'            => $ppn,
            'status'         => $isAdmin ? 3 : 1, // ✅ status 3 jika admin
            'platform'       => $isAdmin ? 'offline' : 'online', // ✅ disini
            'updated_by'     => $userId,
        ];

        $headerId = $modelH->insert($headerData, true);

        // Simpan log status awal (status_id = 1)
        $modelLog->insert([
            'trans_h_orderid' => $headerId,
            'status_id'       => $isAdmin ? 3 : 1, // ✅ log sesuai status awal
            'updated_by' => user()->id
        ]);

        // Jika admin, simpan data ke trans_d_casher_order
        if ($isAdmin) {
            $casherModel = new \App\Models\TransDCasherOrder();

            $casherModel->insert([
                'trans_h_orderid'  => $headerId,
                'payment_method'   => $this->request->getPost('payment_method'),
                'paid_amount'      => str_replace(',', '', $this->request->getPost('paid_amount')), // remove comma
                'change_amount'    => str_replace(',', '', $this->request->getPost('change_amount')),
                'updated_by'       => $userId
            ]);
        }

        foreach ($cartItems as $item) {
            $modelD->insert([
                'trans_h_orderid' => $headerId,
                'medicine_id'     => $item['medicine_id'],
                'medicine_name'   => $item['medicine_name'],
                'price'           => $item['price'],
                'medicine_image'  => $item['medicine_image'] ?? null,
                'qty'             => $item['qty']
            ]);

            $currentStock = $modelStock->where('medicine_id', $item['medicine_id'])->first();
            if ($currentStock && $currentStock['qty'] >= $item['qty']) {
                $modelStock->where('medicine_id', $item['medicine_id'])->set([
                    'qty' => $currentStock['qty'] - $item['qty']
                ])->update();
            } else {
                return redirect()->back()->with('error', 'Stok untuk ' . $item['medicine_name'] . ' tidak mencukupi!');
            }
        }

        $cartModel->where('user_id', $userId)->delete();

        return redirect()->to('/transaction')->with('success', 'Transaksi berhasil disimpan!');
    }

    public function detail_transaction($id)
    {
        $db = \Config\Database::connect();

        // Ambil data header transaksi
        $builder = $db->table('trans_h_order');
        $builder->select('
    trans_h_order.*, 
    area.name as area_name,
    status_trx.id as status_id,
    status_trx.name as status_name
');
        $builder->join('area', 'area.id = trans_h_order.area', 'left');
        $builder->join('status_trx', 'status_trx.id = trans_h_order.status', 'left');
        $builder->where('trans_h_order.id', $id);
        $transaction = $builder->get()->getRowArray();

        if ($transaction) {
            // Ambil data detail barang dipesan
            $builderDetail = $db->table('trans_d_order');
            $builderDetail->where('trans_h_orderid', $id);
            $items = $builderDetail->get()->getResultArray();

            // Tambahkan data item ke array transaksi
            $transaction['items'] = $items;

            // Jika user admin, ambil data pembayaran kasir
            if (in_groups('Admin')) {
                $builderCash = $db->table('trans_d_casher_order');
                $builderCash->select('paid_amount, change_amount');
                $builderCash->where('trans_h_orderid', $id);
                $cashData = $builderCash->get()->getRowArray();

                $transaction['paid_amount'] = $cashData['paid_amount'] ?? null;
                $transaction['change_amount'] = $cashData['change_amount'] ?? null;
            }
        }

        $logModel = new LogStatusTrx();
        $statusLogs = $logModel
            ->select('log_status_trx.*, status_trx.name as status_name')
            ->join('status_trx', 'status_trx.id = log_status_trx.status_id')
            ->where('log_status_trx.trans_h_orderid', $transaction['id'])
            ->orderBy('log_status_trx.created_at', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Detail Transaksi',
            'transaction' => $transaction,
            'statusLogs'  => $statusLogs
        ];

        return view('user/sales/detail_transaction', $data);
    }

    public function cancel($id)
    {
        $userId = user()->id;

        // Load model yang dibutuhkan
        $orderModel        = new \App\Models\TransHOrder();
        $refundModel       = new \App\Models\TransHRefund();
        $logModel          = new \App\Models\LogStatusTrx();
        $orderDetailModel  = new \App\Models\TransDOrder();
        $stockModel        = new \App\Models\TotalMdStock();

        // Ambil data transaksi
        $order = $orderModel->find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan.');
        }

        // Cek status dari query string
        $status = $this->request->getGet('status');

        if ($status == 5) {
            // Status 5 = Pesanan diterima, sekarang diselesaikan menjadi 4

            $orderModel->update($id, [
                'status'     => 3, // selesai
                'updated_by' => $userId,
            ]);

            $logModel->insert([
                'trans_h_orderid' => $id,
                'status_id'       => 3,
                'updated_by'      => $userId,
            ]);

            return redirect()->to('/transaction')->with('success', 'Pesanan berhasil diselesaikan.');
        } else {
            // Default: batalkan pesanan (status 6)

            // Insert ke trans_h_refund
            $refundModel->insert([
                'trans_h_orderid' => $order['id'],
                'no_trx'          => $order['no_trx'],
                'updated_by'      => $userId,
            ]);

            // Kembalikan stok
            $items = $orderDetailModel->where('trans_h_orderid', $id)->findAll();
            foreach ($items as $item) {
                $stock = $stockModel->where('medicine_id', $item['medicine_id'])->first();
                if ($stock) {
                    $stockModel->update($stock['id'], [
                        'qty' => $stock['qty'] + $item['qty']
                    ]);
                }
            }

            // Update status ke 6 (dibatalkan)
            $orderModel->update($id, [
                'status'     => 6,
                'updated_by' => $userId,
            ]);

            // Insert ke log_status_trx
            $logModel->insert([
                'trans_h_orderid' => $id,
                'status_id'       => 6,
                'updated_by'      => $userId,
            ]);

            return redirect()->to('/transaction')->with('success', 'Pesanan berhasil dibatalkan, refund dicatat, dan stok dikembalikan.');
        }
    }

    public function req_refund_list()
    {
        $db = \Config\Database::connect();
        $userId = user()->id;

        // Subquery untuk ambil refund terbaru (created_at paling baru) per no_trx
        $subquery = $db->table('trans_h_refund thr')
            ->select('thr.no_trx, MAX(tdr.created_at) AS latest_created')
            ->join('trans_d_refund tdr', 'tdr.trans_h_refund = thr.id', 'left')
            ->groupBy('thr.no_trx');

        $builder = $db->table('trans_h_refund thr');
        $builder->select('thr.id as refund_id, thr.no_trx, thr.status, tdr.created_at, thr.status as status_id, sr.name as status_name, tho.total_price');
        $builder->join('trans_h_order tho', 'tho.id = thr.trans_h_orderid', 'left');
        $builder->join('trans_d_refund tdr', 'tdr.trans_h_refund = thr.id', 'left');
        $builder->join('status_refund sr', 'sr.id = thr.status', 'left');

        // Join ke subquery berdasarkan no_trx dan created_at
        $builder->join('(' . $subquery->getCompiledSelect() . ') latest', 'latest.no_trx = thr.no_trx AND latest.latest_created = tdr.created_at', 'inner');

        $builder->where('tho.userid', $userId);
        $builder->where('thr.status IS NOT NULL');
        $builder->orderBy('tdr.created_at', 'DESC');

        $refunds = $builder->get()->getResultArray();

        $data = [
            'title' => 'List Refund',
            'refunds' => $refunds
        ];


        return view('user/refund/index', $data);
    }

    public function add_refund()
    {
        $bankModel = new Bank();
        $refundModel = new TransHRefund();
        $orderModel = new TransHOrder();

        $userId = user()->id;

        // Ambil list nomor transaksi refund milik user yang status transaksinya = 1
        $refunds = $refundModel
            ->select('trans_h_refund.no_trx')
            ->join('trans_h_order', 'trans_h_order.no_trx = trans_h_refund.no_trx')
            ->where('trans_h_order.userid', $userId)
            ->groupStart()
            ->where('trans_h_refund.status', null)
            ->orWhereIn('trans_h_refund.status', [3])
            ->groupEnd()
            ->whereIn('trans_h_order.status', [4, 6])
            ->findAll();

        $data['title'] = 'Form Pengajuan Refund';
        $data['banks'] = $bankModel->where('status', 1)->findAll();
        $data['transactions'] = $refunds;

        return view('user/refund/add', $data);
    }

    public function submit_refund()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'no_trx'       => 'required',
            'name'         => 'required',
            'phone'        => 'required',
            'email'        => 'required|valid_email',
            'bank_id'      => 'required|numeric',
            'bank_account' => 'required',
            'reason'       => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $validation->listErrors());
        }

        $refundHModel = new \App\Models\TransHRefund();
        $refundDModel = new \App\Models\TransDRefund();
        $logStatusModel = new \App\Models\LogStatusRefund(); // tambahkan model log

        $userId = user()->id;
        $noTrx = $this->request->getPost('no_trx');

        // Cari ID dari trans_h_refund berdasarkan no_trx dan user
        $refundHeader = $refundHModel
            ->where('no_trx', $noTrx)
            // ->where('updated_by', $userId)
            ->first();

        if (!$refundHeader) {
            return redirect()->back()->with('error', 'Transaksi refund tidak ditemukan atau tidak valid.');
        }

        // Simpan ke tabel trans_d_refund
        $refundDModel->insert([
            'trans_h_refund' => $refundHeader['id'],
            'name'           => $this->request->getPost('name'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
            'bank_id'        => $this->request->getPost('bank_id'),
            'bank_account'   => $this->request->getPost('bank_account'),
            'reason'         => $this->request->getPost('reason')
        ]);

        // Update status trans_h_refund ke 1 (misalnya: 'Menunggu Proses Refund')
        $refundHModel->update($refundHeader['id'], [
            'status'     => 1,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Insert ke tabel log_status_refund
        $logStatusModel->insert([
            'trans_h_refund' => $refundHeader['id'],
            'status_refund'  => 1, // Status awal pengajuan
            'updated_by'     => $userId
        ]);

        return redirect()->to('/req_refund')->with('success', 'Pengajuan refund berhasil dikirim.');
    }

    public function confirm_refund($id)
    {
        $userId = user()->id;
        $refundModel = new TransHRefund();
        $logModel = new LogStatusRefund();

        $refund = $refundModel->find($id);
        if (!$refund) {
            return redirect()->back()->with('error', 'Data refund tidak ditemukan.');
        }

        $newStatus = $this->request->getGet('status'); // Ambil status dari URL

        if (!in_array($newStatus, [3, 4])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        // Cek hak akses / status sebelumnya jika diperlukan
        if ($newStatus == 3 && $refund['status'] != 1) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat dibatalkan.');
        }

        // Update trans_h_refund
        $refundModel->update($id, [
            'status'     => $newStatus,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Insert ke log
        $logModel->insert([
            'trans_h_refund' => $id,
            'status_refund'  => $newStatus,
            'updated_by'     => $userId,
        ]);

        $msg = $newStatus == 3 ? 'dibatalkan' : 'diselesaikan';
        return redirect()->back()->with('success', "Pengajuan refund berhasil $msg.");
    }

    public function detail_refund($id)
    {
        $userId = user()->id;

        $refundHModel = new TransHRefund();
        $refundDModel = new TransDRefund();
        $bankModel = new Bank();
        $orderModel = new TransHOrder();
        $logModel = new \App\Models\LogStatusRefund();
        $approvalModel = new \App\Models\TransDApprovalRefund();


        // Ambil data header refund
        $refundHeader = $refundHModel->find($id);
        if (!$refundHeader) {
            return redirect()->back()->with('error', 'Data refund tidak ditemukan.');
        }

        // Ambil data detail refund
        $refundDetail = $refundDModel->where('trans_h_refund', $id)->first();

        // Ambil list bank
        $banks = $bankModel->where('status', 1)->findAll();

        // Ambil transaksi milik user yang sudah masuk ke trans_h_refund dengan status 1
        $transactions = $refundHModel
            ->select('trans_h_refund.no_trx')
            ->join('trans_h_order', 'trans_h_order.id = trans_h_refund.trans_h_orderid')
            ->where('trans_h_refund.status', 1)
            ->where('trans_h_order.userid', $userId)
            ->findAll();

        $logStatus = $logModel
            ->select('log_status_refund.*, status_refund.name as status_label, users.fullname as updated_by_name')
            ->join('status_refund', 'status_refund.id = log_status_refund.status_refund')
            ->join('users', 'users.id = log_status_refund.updated_by', 'left')
            ->where('log_status_refund.trans_h_refund', $id)
            ->orderBy('log_status_refund.created_at', 'DESC')
            ->findAll();

        $approvalData = $approvalModel->where('trans_h_refund', $id)->orderBy('id', 'DESC')->first();
        $refundDetail = array_merge($refundDetail ?? [], $approvalData ?? []);

        $data = [
            'title'        => 'Detail Refund',
            'refundHeader' => $refundHeader,
            'refundDetail' => $refundDetail,
            'banks'        => $banks,
            'transactions' => $transactions,
            'logStatus'    => $logStatus
        ];

        return view('user/refund/detail', $data);
    }

    public function trxinvoicePdf($id)
    {
        $orderModel = new \App\Models\TransHOrder();
        $detailModel = new \App\Models\TransDOrder();
        $statusModel = new \App\Models\StatusTrx();

        $order = $orderModel->find($id);
        $items = $detailModel->where('trans_h_orderid', $id)->findAll();

        if (!$order) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }

        $status = $statusModel->find($order['status']);

        // Tambahkan data kasir jika admin
        $cashData = [];
        if (in_groups('Admin')) {
            $db = \Config\Database::connect();
            $builder = $db->table('trans_d_casher_order');
            $builder->select('paid_amount, change_amount');
            $builder->where('trans_h_orderid', $id);
            $cashData = $builder->get()->getRowArray() ?? [];
        }
        // dd($cashData);
        $data = [
            'order' => $order,
            'items' => $items,
            'status' => $status['name'] ?? 'Tidak diketahui',
            'is_admin' => in_groups('Admin'),
            'cash' => $cashData
        ];

        // Load view sebagai HTML
        $html = view('user/sales/invoice_pdf', $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        ob_start();                      // MULAI output buffering
        $dompdf->render();
        $pdfOutput = $dompdf->output();
        ob_end_clean();                  // BERSIHKAN buffer (hindari karakter liar)

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Invoice-' . $order['no_trx'] . '.pdf"')
            ->setBody($pdfOutput);
        // Generate PDF
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml($html);
        // $dompdf->setPaper('A4', 'portrait');
        // $dompdf->render();
        // $dompdf->stream('Invoice-' . $order['no_trx'] . '.pdf', ['Attachment' => false]); // Set true untuk auto-download
    }

    public function refundinvoicePdf($id)
    {
        $db = \Config\Database::connect();

        // Ambil data header refund
        $builder = $db->table('trans_h_refund thr');
        $builder->select('thr.no_trx, thr.created_at, thr.status, sr.name as status_name, tho.total_price');
        $builder->join('status_refund sr', 'sr.id = thr.status', 'left');
        $builder->join('trans_h_order tho', 'tho.id = thr.trans_h_orderid', 'left');
        $builder->where('thr.id', $id);
        $header = $builder->get()->getRowArray();

        if (!$header) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data refund tidak ditemukan.");
        }

        // Detail refund
        $detail = $db->table('trans_d_refund tdr')
            ->select('tdr.name, tdr.phone, tdr.email, tdr.bank_account, tdr.reason, mb.name as bank_name')
            ->join('bank mb', 'mb.id = tdr.bank_id', 'left')
            ->where('tdr.trans_h_refund', $id)
            ->get()->getRowArray();

        // Approval refund
        $approval = $db->table('trans_d_approval_refund')
            ->where('trans_h_refund', $id)
            ->orderBy('created_at', 'DESC')
            ->get()->getRowArray();

        // Siapkan data untuk view
        $data = [
            'title' => 'Invoice Refund',
            'header' => $header,
            'detail' => $detail,
            'approval' => $approval
        ];

        // Render view ke HTML
        $html = view('user/refund/invoice_pdf', $data);

        // Inisialisasi Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream PDF ke browser
        $dompdf->stream('invoice_refund_' . $header['no_trx'] . '.pdf', ['Attachment' => false]);
        exit;
    }
}
