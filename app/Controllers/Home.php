<?php

namespace App\Controllers;

use Myth\Auth\Models\UserModel;

class Home extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        $builder = $db->table('medicine_list');

        // Produk populer (pakai harga diskon)
        $popularProducts = $builder->where('status', 1)
            // ->where('discount_price IS NULL')
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // Produk terbaru (order by created_at)
        $newProducts = $db->table('medicine_list')
            ->where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->limit(7)
            ->get()
            ->getResultArray();

        return view('web-profile', [
            'popularProducts' => $popularProducts,
            'newProducts' => $newProducts,
        ]);
    }

    public function activateAccount($token)
    {
        $users = new UserModel();
        $user = $users->where('activate_hash', $token)->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Token aktivasi tidak valid atau sudah digunakan.');
        }

        $users->update($user['id'], [
            'activate_hash' => null,
            'active' => 1,
        ]);

        return redirect()->to('/login')->with('message', 'Akun berhasil diaktivasi. Silakan login.');
    }

    public function dashboard()
    {
        $userId = user()->id;
        // dd($userId);
        $isUser = in_groups('User');

        $orderModel = new \App\Models\TransHOrder();
        $refundModel = new \App\Models\TransHRefund();
        $casherModel = new \App\Models\TransDCasherOrder();

        if ($isUser) {
            // Dashboard untuk USER
            $totalPembelian = $orderModel->where('userid', $userId)->countAllResults();
            $diproses = $orderModel->where('userid', $userId)->whereIn('status', [1, 2])->countAllResults();
            $totalPengeluaran = $orderModel->where('userid', $userId)->selectSum('total_price')->first()['total_price'] ?? 0;
            $totalRefund = $refundModel->where('updated_by', $userId)->countAllResults();

            $latestOrders = $orderModel->asArray()
                ->select('trans_h_order.*, status_trx.name as status_name')
                ->join('status_trx', 'status_trx.id = trans_h_order.status', 'left')
                ->where('trans_h_order.userid', $userId)
                ->orderBy('trans_h_order.created_at', 'DESC')
                ->findAll(3);

            $latestRefund = $refundModel->asArray()
                ->select('trans_h_refund.*, status_refund.name as status_name, trans_h_order.total_price')
                ->join('status_refund', 'status_refund.id = trans_h_refund.status', 'left')
                ->join('trans_h_order', 'trans_h_order.id = trans_h_refund.trans_h_orderid', 'left')
                ->where('trans_h_refund.updated_by', $userId)
                ->orderBy('trans_h_refund.created_at', 'DESC')
                ->findAll(3);

            return view('dashboard', [
                'title' => 'Dashboard',
                'isUser' => true,
                'totalPembelian' => $totalPembelian,
                'diproses' => $diproses,
                'totalPengeluaran' => $totalPengeluaran,
                'totalRefund' => $totalRefund,
                'latestOrders' => $latestOrders,
                'latestRefund' => $latestRefund
            ]);
        } else {
            // Dashboard untuk ADMIN
            $totalTransaksi = $orderModel->countAllResults();
            $transaksiHariIni = $orderModel
                ->where('DATE(created_at)', date('Y-m-d'))
                ->countAllResults();

            $pendapatan = $orderModel
                ->whereNotIn('status', [4, 6])
                ->selectSum('total_price')
                ->first()['total_price'] ?? 0;

            $totalRefund = $refundModel
                ->where('status IS NOT NULL', null, false) // exclude status null
                ->countAllResults();

            $grafikData = $orderModel
                ->select("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total_price) as total")
                ->where("created_at >=", date('Y-m-01', strtotime('-11 months'))) // 12 bulan terakhir
                ->whereNotIn('status', [4, 6])
                ->groupBy("bulan")
                ->orderBy("bulan")
                ->findAll();

            // Siapkan array 12 bulan terakhir dengan default 0
            $grafikPendapatan = [];
            for ($i = 11; $i >= 0; $i--) {
                $bulanKey = date('Y-m', strtotime("-$i months"));
                $grafikPendapatan[$bulanKey] = 0;
            }

            // Masukkan data hasil query ke array
            foreach ($grafikData as $row) {
                $grafikPendapatan[$row['bulan']] = (int)$row['total'];
            }

            $latestOrders = $orderModel->asArray()
                ->select('trans_h_order.*, users.fullname, status_trx.name as status_name')
                ->join('users', 'users.id = trans_h_order.userid', 'left')
                ->join('status_trx', 'status_trx.id = trans_h_order.status', 'left')
                ->orderBy('trans_h_order.created_at', 'DESC')
                ->findAll(5);

            $latestRefund = $refundModel->asArray()
                ->select('trans_h_refund.*, users.fullname, trans_h_order.total_price, status_refund.name as status_name')
                ->join('users', 'users.id = trans_h_refund.updated_by', 'left')
                ->join('trans_h_order', 'trans_h_order.id = trans_h_refund.trans_h_orderid', 'left')
                ->join('status_refund', 'status_refund.id = trans_h_refund.status', 'left')
                ->where('trans_h_refund.status IS NOT NULL', null, false)
                ->whereNotIn('trans_h_refund.status', [4, 6])
                ->orderBy('trans_h_refund.created_at', 'DESC')
                ->findAll(5);

            return view('dashboard', [
                'title' => 'Dashboard',
                'isUser' => false,
                'totalTransaksi' => $totalTransaksi,
                'transaksiHariIni' => $transaksiHariIni,
                'pendapatan' => $pendapatan,
                'totalRefund' => $totalRefund,
                'grafikPendapatan' => $grafikPendapatan,
                'latestOrders' => $latestOrders,
                'latestRefund' => $latestRefund
            ]);
        }
    }
}
