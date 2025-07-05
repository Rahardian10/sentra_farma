<?php

namespace App\Controllers;

use App\Models\MedicineList;
use App\Models\MedicineCategory;
use App\Models\MedicineLocation;
use App\Models\Manufactur;
use App\Models\MedicineUnit;
use App\Models\MasterSubstance;
use App\Models\MedicineSubstance;
use App\Models\MedicineStockIn;
use App\Models\DetailStockIn;
use App\Models\DetailStockOut;
use App\Models\LogStatusTrx;
use App\Models\MedicineStockOut;
use App\Models\TotalMdStock;
use App\Models\TrxType;
use App\Models\StatusTrx;
use App\Models\TransHOrder;
use App\Models\TransDOrder;
use App\Models\TransHRefund;
use App\Models\TransDRefund;
use App\Models\Bank;
use App\Models\StatusRefund;
use App\Models\LogStatusRefund;
use App\Models\TransDApprovalRefund;
use Myth\Auth\Models\UserModel;

class AdminController extends BaseController
{
    protected $db, $builder;

    public function __construct()
    {
        // $this->db = \Config\Database::connect();
        // $this->builder = $this->db->table('medicine_list');
    }

    private function generateUniqueId($model, $min = 100000, $max = 999999)
    {
        do {
            $randomId = random_int($min, $max);
        } while ($model->find($randomId)); // Ulangi kalau ID sudah ada

        return $randomId;
    }

    private function generateUniqueIdOut($model)
    {
        // Contoh: SO-20250502-XXXX
        $prefix = 'SO-' . date('Ymd') . '-';
        $last = $model->like('exit_number', $prefix)->orderBy('exit_number', 'DESC')->first();

        if ($last) {
            $lastNumber = (int)substr($last['exit_number'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    public function index()
    {
        $data['title'] = 'List Data Obat';

        $db = \Config\Database::connect();
        $builder = $db->table('medicine_list');
        $builder->select('medicine_list.*, manufactur.name as manufactur, medicine_unit.name as unit,
        medicine_category.name as category ');
        $builder->join('medicine_category', 'medicine_category.id = medicine_list.md_category');
        $builder->join('medicine_location', 'medicine_location.id = medicine_list.location');
        $builder->join('manufactur', 'manufactur.id = medicine_list.manufactur');
        $builder->join('medicine_unit', 'medicine_unit.id = medicine_list.md_unit');
        $builder->orderBy('medicine_list.id', 'desc');
        $query = $builder->get();

        $data['medicineList'] = $query->getResult();
        return view('admin/medicine_list/index', $data);
    }

    public function add()
    {
        $data['title'] = 'Form Data Obat';
        $MedicineCategory = new MedicineCategory();
        $MedicineList = new MedicineLocation();
        $Manufactur = new Manufactur();
        $MedicineUnit = new MedicineUnit();
        $MasterSubstance = new MasterSubstance();
        $data['md_category'] = $MedicineCategory->findAll();
        $data['md_location'] = $MedicineList->findAll();
        $data['manufactur'] = $Manufactur->findAll();
        $data['md_unit'] = $MedicineUnit->findAll();
        $data['m_subs'] = $MasterSubstance->findAll();
        return view('admin/medicine_list/medicine_form', $data);
    }

    public function edit($id = 0)
    {
        $data['title'] = 'Form Data Obat';
        $MedicineCategory = new MedicineCategory();
        $MedicineList = new MedicineLocation();
        $Manufactur = new Manufactur();
        $MedicineUnit = new MedicineUnit();
        $MasterSubstance = new MasterSubstance();
        $MedicineSubstance = new MedicineSubstance();

        $data['md_category'] = $MedicineCategory->findAll();
        $data['md_location'] = $MedicineList->findAll();
        $data['manufactur'] = $Manufactur->findAll();
        $data['md_unit'] = $MedicineUnit->findAll();
        $data['m_subs'] = $MasterSubstance->findAll();

        // Tambahkan ini
        $db = \Config\Database::connect();
        $builder = $db->table('medicinal_substances');
        $builder->select('master_substance.id, master_substance.name');
        $builder->join('master_substance', 'master_substance.id = medicinal_substances.master_substance_id');
        $builder->where('medicinal_substances.medicine_id', $id);

        $query = $builder->get();
        $result = $query->getResultArray();
        $data['selectedSubs'] = array_column($result, 'name');
        // dd($data['selectedSubs']);

        $builder = $db->table('medicine_list');
        $builder->select('medicine_list.*, manufactur.id as manufacturid, medicine_unit.id as unitid,
        medicine_category.id as md_cat_id, medicine_location.id as loc_id');
        $builder->join('medicine_category', 'medicine_category.id = medicine_list.md_category');
        $builder->join('medicine_location', 'medicine_location.id = medicine_list.location');
        $builder->join('manufactur', 'manufactur.id = medicine_list.manufactur');
        $builder->join('medicine_unit', 'medicine_unit.id = medicine_list.md_unit');
        $builder->where('medicine_list.id', $id);
        $query = $builder->get();
        $data['medicineList'] = $query->getRow();

        return view('admin/medicine_list/edit', $data);
    }

    public function save($id = 0)
    {
        $MedicineList = new MedicineList();
        $MedicineSubstance = new MedicineSubstance();

        $id = $this->request->getPost('id'); // ID akan ada saat update
        $idUnik = $this->generateUniqueId($MedicineList);

        // Ambil data yang dikirimkan melalui form
        $name = $this->request->getPost('name');
        $md_category = $this->request->getPost('md_category');
        $location = $this->request->getPost('location');
        $manufactur = $this->request->getPost('manufactur');
        $md_unit = $this->request->getPost('md_unit');
        $convertion_value = $this->request->getPost('convertion_value');
        $substance = $this->request->getPost('substance');
        $other_data = $this->request->getPost('other_data');
        $md_chronic = $this->request->getPost('md_chronic');
        $vaccine = $this->request->getPost('vaccine');
        $cover_bpjs = $this->request->getPost('cover_bpjs');
        $price = $this->request->getPost('price');
        $discount_price = $this->request->getPost('discount_price');
        $ecatalog = $this->request->getPost('ecatalog');
        $status = $this->request->getPost('status');

        //validasi error
        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required',
            'md_category' => 'required',
            'location' => 'required',
            'manufactur' => 'required',
            'md_unit' => 'required',
            'convertion_value' => 'required|numeric',
            'substance' => 'required',
            'price' => 'required|numeric',
            'status' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // Proses upload gambar jika ada
        $imageFile = $this->request->getFile('md_pict');
        $imagePath = '';

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/medicine/', $newImageName);
            $imagePath = $newImageName;
        } else {
            $imagePath = $this->request->getPost('current_md_pict');
        }

        // === Buat data untuk disimpan ===
        $MedicineListProcess = [
            'code' => $idUnik,
            'name' => $name,
            'status' => $status,
            'md_category' => $md_category,
            'manufactur' => $manufactur,
            'location' => $location,
            'md_unit' => $md_unit,
            'convertion_value' => $convertion_value,
            'md_chronic' => $md_chronic,
            'vaccine' => $vaccine,
            'cover_bpjs' => $cover_bpjs,
            'medicine_pict' => $imagePath,
            'other_data' => $other_data,
            'price' => $price,
            'discount_price' => $discount_price,
            'ecatalog' => $ecatalog,
            'updated_by' => user()->id,
        ];
        // dd($MedicineListProcess);
        // Jika ini update, tambahkan ID dan ambil kode lama
        if ($id) {
            $existing = $MedicineList->find($id);
            if (!$existing) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            $MedicineListProcess['id'] = $id;
            $MedicineListProcess['code'] = $existing['code']; // gunakan yang lama
        } else {
            // insert baru
            $MedicineListProcess['code'] = $idUnik;
        }

        $MedicineList->save($MedicineListProcess);

        // Ambil ID transaksi: saat insert dari insertID, saat update pakai $id
        $trxId = $id ?? $MedicineList->insertID();

        // Hapus dulu substance lama jika update
        if ($id) {
            $MedicineSubstance->where('medicine_id', $trxId)->delete();
        }

        // Simpan ulang substance
        if (is_array($substance)) {
            foreach ($substance as $sub) {
                $MedicineSubstance->save([
                    'medicine_id' => $trxId,
                    'master_substance_id' => $sub,
                    'status' => 1,
                ]);
            }
        } elseif ($substance) {
            $MedicineSubstance->save([
                'medicine_id' => $trxId,
                'master_substance_id' => $substance,
                'status' => 1,
            ]);
        }

        $message = $id ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.';
        return redirect()->to('medicinelist')->with('success', $message);
    }

    public function stockin()
    {
        $data['title'] = 'List Data Obat Masuk';

        $db = \Config\Database::connect();
        $builder = $db->table('medicine_stockin');
        $builder->select('medicine_stockin.*');
        // $builder->join('detail_md_stockin', 'detail_md_stockin.stockin_id = medicine_stockin.id');
        // $builder->join('medicine_list', 'medicine_list.id = detail_md_stockin.medicine_id');
        $builder->orderBy('medicine_stockin.id', 'desc');
        $query = $builder->get();

        $data['medicineStockin'] = $query->getResult();
        return view('admin/stockin/stockin', $data);
    }

    public function add_stockin()
    {
        $data['title'] = 'Form Data Obat Masuk';

        $db = \Config\Database::connect();
        $builder = $db->table('medicine_list');
        $builder->select('medicine_list.*, medicine_unit.name as unit');
        $builder->join('medicine_unit', 'medicine_unit.id = medicine_list.md_unit');
        $builder->where('medicine_list.status', 1);
        $builder->orderBy('medicine_list.id', 'desc');
        $query = $builder->get();
        $data['medicineList'] = $query->getResult();
        // dd($data['medicineList']);
        return view('admin/stockin/add_stockin', $data);
    }

    public function getStokObat()
    {
        $medicineId = $this->request->getGet('id');

        // Query stok dari table total_md_qty
        $db = \Config\Database::connect();
        $builder = $db->table('total_md_stock');
        $builder->selectSum('qty', 'total_qty');
        $builder->where('medicine_id', $medicineId);
        $query = $builder->get()->getRow();

        $totalQty = $query ? $query->total_qty : 0;

        return $this->response->setJSON(['total_qty' => $totalQty]);
    }

    public function save_stockin($id = null)
    {
        helper(['form', 'url']);
        $MedicineStockIn = new MedicineStockIn();
        $db = \Config\Database::connect();

        try {
            // 1️⃣ Ambil data form utama (medicine_stockin)
            $title = $this->request->getPost('title');
            $supplier = $this->request->getPost('supplier');
            $supplier_address = $this->request->getPost('supplier_address');
            $supplier_contact = $this->request->getPost('supplier_contact');
            $receiver = $this->request->getPost('receiver');
            $date_of_receipt = $this->request->getPost('date_of_receipt');
            $items = $this->request->getPost('items');

            //validasi error
            $validation = \Config\Services::validation();

            $rules = [
                'title' => 'required',
                'supplier' => 'required',
                'supplier_address' => 'required',
                'supplier_contact' => 'required',
                'receiver' => 'required',
                'date_of_receipt' => 'required',
                'items' => 'required|is_array'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', implode('<br>', $this->validator->getErrors()));
            }

            // Handle Upload Invoice
            $invoiceFile = $this->request->getFile('invoice');
            $invoiceName = null;

            if ($invoiceFile && $invoiceFile->isValid() && !$invoiceFile->hasMoved()) {
                $invoiceName = $invoiceFile->getRandomName();
                $invoiceFile->move('uploads/invoices/', $invoiceName);
            }

            // 2️⃣ INSERT BARU
            if ($id == null) {
                // Buat nomor PO baru
                $po_number = $this->generateUniqueId($MedicineStockIn);

                $stockInData = [
                    'po_number' => $po_number,
                    'title' => $title,
                    'supplier' => $supplier,
                    'supplier_address' => $supplier_address,
                    'supplier_contact' => $supplier_contact,
                    'receiver' => $receiver,
                    'date_of_receipt' => $date_of_receipt,
                    'invoice' => $invoiceName,
                    'updated_by' => user()->id // opsional
                ];

                $db->table('medicine_stockin')->insert($stockInData);
                $stockInId = $db->insertID();

                // Detail & Stock Baru
                foreach ($items as $item) {
                    $detailData = [
                        'stockin_id' => $stockInId,
                        'medicine_id' => $item['md_id'],
                        'stock_qty' => $item['stock_qty'],
                        'unit_price' => $item['unit_price'],
                        'expire_date' => $item['expire_date'],
                    ];
                    $db->table('detail_md_stockin')->insert($detailData);

                    // Tambah stok
                    $existingStock = $db->table('total_md_stock')
                        ->where('medicine_id', $item['md_id'])
                        ->get()
                        ->getRow();

                    if ($existingStock) {
                        $db->table('total_md_stock')
                            ->set('qty', 'qty + ' . (int)$item['stock_qty'], false)
                            ->where('medicine_id', $item['md_id'])
                            ->update();
                    } else {
                        $db->table('total_md_stock')->insert([
                            'medicine_id' => $item['md_id'],
                            'qty' => (int)$item['stock_qty']
                        ]);
                    }
                }

                return redirect()->to(base_url('medicinestockin'))->with('success', 'Data berhasil disimpan.');
            }
            // 3️⃣ UPDATE
            else {
                // Ambil data stockin lama
                $stockinLama = $db->table('medicine_stockin')->where('id', $id)->get()->getRowArray();

                if (!$stockinLama) {
                    return redirect()->to('/medicinestockin')->with('error', 'Data tidak ditemukan.');
                }

                // Jika upload file baru
                if ($invoiceName == null) {
                    $invoiceName = $stockinLama['invoice']; // tetap file lama
                }

                // Update header
                $stockInData = [
                    'title' => $title,
                    'supplier' => $supplier,
                    'supplier_address' => $supplier_address,
                    'supplier_contact' => $supplier_contact,
                    'receiver' => $receiver,
                    'date_of_receipt' => $date_of_receipt,
                    'invoice' => $invoiceName,
                    'updated_by' => user()->id
                ];
                $db->table('medicine_stockin')->where('id', $id)->update($stockInData);

                // 4️⃣ Kembalikan stok lama sebelum update
                $detailsLama = $db->table('detail_md_stockin')->where('stockin_id', $id)->get()->getResultArray();
                foreach ($detailsLama as $d) {
                    $db->table('total_md_stock')
                        ->set('qty', 'qty - ' . (int)$d['stock_qty'], false)
                        ->where('medicine_id', $d['medicine_id'])
                        ->update();
                }

                // 5️⃣ Hapus detail lama
                $db->table('detail_md_stockin')->where('stockin_id', $id)->delete();

                // 6️⃣ Masukkan detail baru + update stok
                foreach ($items as $item) {
                    $detailData = [
                        'stockin_id' => $id,
                        'medicine_id' => $item['md_id'],
                        'stock_qty' => $item['stock_qty'],
                        'unit_price' => $item['unit_price'],
                        'expire_date' => $item['expire_date'],
                    ];
                    $db->table('detail_md_stockin')->insert($detailData);

                    // Tambah stok baru
                    $existingStock = $db->table('total_md_stock')
                        ->where('medicine_id', $item['md_id'])
                        ->get()
                        ->getRow();

                    if ($existingStock) {
                        $db->table('total_md_stock')
                            ->set('qty', 'qty + ' . (int)$item['stock_qty'], false)
                            ->where('medicine_id', $item['md_id'])
                            ->update();
                    } else {
                        $db->table('total_md_stock')->insert([
                            'medicine_id' => $item['md_id'],
                            'qty' => (int)$item['stock_qty']
                        ]);
                    }
                }

                return redirect()->to(base_url('medicinestockin'))->with('success', 'Data berhasil diupdate.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit_stockin($id)
    {
        $MedicineStockIn = new MedicineStockIn();
        $DetailStockIn = new DetailStockIn();
        $db = \Config\Database::connect(); // Ambil database connection

        // Ambil data header dari table medicine_stockin
        $stockin = $MedicineStockIn->find($id);

        if (!$stockin) {
            return redirect()->to('/medicinestockin')->with('error', 'Data tidak ditemukan.');
        }

        // Ambil detail + stok sekarang + unit (pakai join)
        $builder = $db->table('detail_md_stockin d')
            ->select('d.medicine_id, d.stock_qty, d.unit_price, d.expire_date, 
              ts.qty as stock_now, 
              mu.name as unit_name')
            ->join('total_md_stock ts', 'ts.medicine_id = d.medicine_id', 'left')
            ->join('medicine_list ml', 'ml.id = d.medicine_id', 'left')
            ->join('medicine_unit mu', 'mu.id = ml.md_unit', 'left')
            ->where('d.stockin_id', $id);

        $query = $builder->get();
        $detailsRaw = $query->getResultArray();

        $details = [];
        foreach ($detailsRaw as $detail) {
            $details[] = [
                'medicine_id' => $detail['medicine_id'],
                'stock_qty'   => $detail['stock_qty'],
                'unit_price'  => $detail['unit_price'],
                'expire_date' => $detail['expire_date'],
                'qty'         => $detail['stock_now'] ?? 0, // stok sekarang
                'unit'        => $detail['unit_name'] ?? '', // nama unit
            ];
        }

        // Ambil semua daftar obat untuk pilihan <select>
        // $MedicineList = new MedicineList();
        $medicines = $db->table('medicine_list')
            ->select('medicine_list.id, medicine_list.name, n.name as unit')
            ->join('medicine_unit n', 'n.id = medicine_list.md_unit')
            ->where('medicine_list.status', 1) // aktif saja
            ->get()
            ->getResultArray();

        $data = [
            'title'     => 'Form Edit Obat Masuk',
            'stockin'   => $stockin,
            'details'   => $details,
            'medicines' => $medicines,
            'medicineList' => $medicines, // untuk JS medicineListData
        ];

        return view('admin/stockin/edit_stockin', $data);
    }

    public function stockout()
    {
        $data['title'] = 'List Data Obat Keluar';

        $db = \Config\Database::connect();
        $builder = $db->table('medicine_stockout');
        $builder->select('medicine_stockout.*, trx_type.name as type, users.fullname');
        $builder->join('transaction_type as trx_type', 'trx_type.id = medicine_stockout.trx_type');
        $builder->join('users', 'users.id = medicine_stockout.preparation_by');
        $builder->orderBy('medicine_stockout.id', 'desc');
        $query = $builder->get();

        $data['medicineStockOut'] = $query->getResult();
        return view('admin/stockout/stockout', $data);
    }

    public function add_stockout()
    {
        $data['title'] = 'Form Data Obat Keluar';
        $TrxType = new TrxType();

        $data['trx_type'] = $TrxType->findAll();

        $db = \Config\Database::connect();

        // data users
        $build = $db->table('users');
        $build->select('users.*');
        $build->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $build->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        $build->where('auth_groups.id = 1');
        $query = $build->get();
        $data['users'] = $query->getResultArray();

        // medicine list
        $builder = $db->table('medicine_list');
        $builder->select('medicine_list.*, medicine_unit.name as unit');
        $builder->join('medicine_unit', 'medicine_unit.id = medicine_list.md_unit');
        $builder->orderBy('medicine_list.id', 'desc');
        $query = $builder->get();
        $data['medicineList'] = $query->getResult();

        return view('admin/stockout/add_stockout', $data);
    }

    public function save_stockout($id = null)
    {
        // Validasi data
        if (!$this->validate([
            'title'          => 'required',
            'trx_type'       => 'required',
            'preparation_by' => 'required',
            'date_of_public' => 'required',
            'items'          => 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Data belum lengkap!');
        }

        $medicineStockOutModel = new MedicineStockOut();
        $detailMdStockOutModel = new DetailStockOut();
        $totalMdStockModel     = new TotalMdStock();

        // Handle upload file bukti
        $envidenceFile = $this->request->getFile('envidence');
        $envidenceName = null;
        if ($envidenceFile && $envidenceFile->isValid()) {
            $envidenceName = $envidenceFile->getRandomName();
            $envidenceFile->move('uploads/envidence', $envidenceName);
        }

        // Ambil items detail obat keluar
        $items = $this->request->getPost('items');

        // Mulai transaksi
        $db = \Config\Database::connect(); // Mengakses database
        $db->transStart(); // Memulai transaksi

        try {
            // Kalau ID ada, maka proses update
            if ($id) {
                // Ambil items detail obat keluar
                $items = $this->request->getPost('items');

                // Validasi stok
                foreach ($items as $item) {
                    $medicineId = $item['md_id'];
                    $qtyOut = (int) $item['stock_qty'];

                    // Ambil stok sekarang
                    $currentStock = $totalMdStockModel->where('medicine_id', $medicineId)->first();

                    if (!$currentStock) {
                        throw new \Exception('Stok obat dengan ID ' . $medicineId . ' tidak ditemukan!');
                    }

                    // Cek apakah stok cukup
                    if ($currentStock['qty'] < $qtyOut) {
                        throw new \Exception('Stok obat tidak cukup! Stok tersedia: ' . $currentStock['qty'] . ', yang diminta: ' . $qtyOut);
                    }
                }

                // Rollback stok lama jika ada
                $oldDetails = $detailMdStockOutModel->where('stockout_id', $id)->findAll();
                foreach ($oldDetails as $old) {
                    $currentStock = $totalMdStockModel->where('medicine_id', $old['medicine_id'])->first();
                    if ($currentStock) {
                        $newQty = $currentStock['qty'] + $old['stock_qty']; // Balikin stok dulu
                        $totalMdStockModel->update($currentStock['id'], ['qty' => $newQty]);
                    }
                }

                // Hapus detail lama
                $detailMdStockOutModel->where('stockout_id', $id)->delete();

                // Update data header stockout
                $dataUpdate = [
                    'title'          => $this->request->getPost('title'),
                    'trx_type'       => $this->request->getPost('trx_type'),
                    'preparation_by' => $this->request->getPost('preparation_by'),
                    'trx_purpose'    => $this->request->getPost('trx_purpose'),
                    'date_of_public' => $this->request->getPost('date_of_public'),
                    'desc'           => $this->request->getPost('desc'),
                    'updated_by'     => user()->id,
                ];

                if ($envidenceName) {
                    $dataUpdate['envidence'] = $envidenceName;
                }

                $medicineStockOutModel->update($id, $dataUpdate);
                $stockoutId = $id;
            } else {
                // Jika INSERT baru
                $exitNumber = $this->generateUniqueIdOut($medicineStockOutModel);
                $medicineStockOutModel->insert([
                    'exit_number'   => $exitNumber,
                    'title'         => $this->request->getPost('title'),
                    'trx_type'      => $this->request->getPost('trx_type'),
                    'preparation_by' => $this->request->getPost('preparation_by'),
                    'trx_purpose'   => $this->request->getPost('trx_purpose'),
                    'date_of_public' => $this->request->getPost('date_of_public'),
                    'desc'          => $this->request->getPost('desc'),
                    'envidence'     => $envidenceName,
                    'updated_by'    => user()->id,
                ]);
                $stockoutId = $medicineStockOutModel->getInsertID();
            }

            // Simpan detail baru
            foreach ($items as $item) {
                // Validasi stok untuk setiap detail sebelum menyimpan
                $medicineId = $item['md_id'];
                $qtyOut = (int) $item['stock_qty'];

                // Ambil stok sekarang
                $currentStock = $totalMdStockModel->where('medicine_id', $medicineId)->first();

                if ($currentStock && $currentStock['qty'] >= $qtyOut) {
                    // Simpan ke detail_md_stockout
                    $detailMdStockOutModel->insert([
                        'stockout_id' => $stockoutId,
                        'medicine_id' => $medicineId,
                        'stock_qty'   => $qtyOut,
                    ]);

                    // Update total stok (kurangi stok baru)
                    $newQty = $currentStock['qty'] - $qtyOut;
                    $totalMdStockModel->update($currentStock['id'], ['qty' => $newQty]);
                } else {
                    // Jika stok tidak cukup, beri pesan error
                    throw new \Exception('Stok tidak cukup untuk obat dengan ID ' . $medicineId . '. Stok tersedia: ' . ($currentStock['qty'] ?? 0));
                }
            }

            // Commit transaksi jika semua berhasil
            $db->transComplete(); // Menyelesaikan transaksi

            if ($db->transStatus() === false) {
                // Jika transaksi gagal, rollback otomatis sudah terjadi
                throw new \Exception('Transaksi gagal!');
            }

            return redirect()->to('/medicinestockout')->with('success', $id ? 'Transaksi berhasil diupdate!' : 'Transaksi obat keluar berhasil disimpan!');
        } catch (\Exception $e) {
            // Jika ada error, rollback transaksi otomatis
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit_stockout($id)
    {
        $db = \Config\Database::connect();

        // Ambil data header dari table medicine_stockout
        $stockout = $db->table('medicine_stockout')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$stockout) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak ditemukan.');
        }

        // Ambil detail barang keluar
        $details = $db->table('detail_md_stockout d')
            ->select('d.id, d.stockout_id, d.medicine_id, d.stock_qty, m.name as medicine_name, n.name as unit, t.qty as stock_now')
            ->join('medicine_list m', 'd.medicine_id = m.id')
            ->join('medicine_unit n', 'n.id = m.md_unit')
            ->join('total_md_stock t', 't.medicine_id = m.id', 'left')
            ->where('d.stockout_id', $id)
            ->get()
            ->getResultArray();

        // Ambil semua pilihan obat (untuk dropdown)
        $medicines = $db->table('medicine_list')
            ->select('medicine_list.id, medicine_list.name, n.name as unit')
            ->join('medicine_unit n', 'n.id = medicine_list.md_unit')
            ->where('medicine_list.status', 1) // aktif saja
            ->get()
            ->getResultArray();

        // Ambil semua jenis transaksi (trx_type)
        $trx_type = $db->table('transaction_type')
            ->where('status', 1)
            ->get()
            ->getResultArray();

        // Ambil semua user (untuk dropdown disiapkan oleh)
        $users = $db->table('users')
            ->select('id, fullname')
            ->get()
            ->getResult();


        return view('admin/stockout/edit_stockout', [
            'stockout' => $stockout,
            'details' => $details,
            'medicines' => $medicines,
            'trx_type' => $trx_type,
            'medicineList' => $medicines, // untuk JS medicineListData
            'users' => $users,
            'title' => 'Form Data Obat Keluar'
        ]);
    }

    public function all_stock()
    {
        $data['title'] = 'List Total Stock';

        $db = \Config\Database::connect();
        $builder = $db->table('total_md_stock');
        $builder->select('total_md_stock.*, ml.name, ml.code');
        $builder->join('medicine_list as ml', 'ml.id = total_md_stock.medicine_id');
        // $builder->join('medicine_stockin as ms', 'ms.id = medicine_stockout.preparation_by');
        $builder->orderBy('total_md_stock.id', 'desc');
        $query = $builder->get();

        $data['total_stock'] = $query->getResult();
        return view('admin/total_stock/all_stock', $data);
    }

    public function getLogs($medicineId)
    {
        $db = \Config\Database::connect();

        // 1. Log Masuk (Stock In)
        $builderIn = $db->table('detail_md_stockin as dsi');
        $builderIn->select('dsi.updated_at, dsi.stock_qty, dsi.stockin_id, null as stockout_id, null as checkout_id, null as refund_id, users.fullname');
        $builderIn->join('medicine_stockin as msi', 'msi.id = dsi.stockin_id');
        $builderIn->join('users', 'users.id = msi.updated_by');
        $builderIn->where('dsi.medicine_id', $medicineId);

        // 2. Log Keluar (Stock Out Manual)
        $builderOut = $db->table('detail_md_stockout as dso');
        $builderOut->select('dso.updated_at, dso.stock_qty, null as stockin_id, dso.stockout_id, null as checkout_id, null as refund_id, users.fullname');
        $builderOut->join('medicine_stockout as mso', 'mso.id = dso.stockout_id');
        $builderOut->join('users', 'users.id = mso.updated_by');
        $builderOut->where('dso.medicine_id', $medicineId);

        // 3. Log Checkout (Transaksi Pembelian)
        $builderTrans = $db->table('trans_d_order as tdo');
        $builderTrans->select('tdo.created_at as updated_at, tdo.qty as stock_qty, null as stockin_id, null as stockout_id, tdo.trans_h_orderid as checkout_id, null as refund_id, users.fullname');
        $builderTrans->join('trans_h_order as tho', 'tho.id = tdo.trans_h_orderid');
        $builderTrans->join('users', 'users.id = tho.userid');
        $builderTrans->where('tdo.medicine_id', $medicineId);

        // 4. Log Refund (Barang Kembali)
        $builderRefund = $db->table('trans_d_order as tdo');
        $builderRefund->select('r.created_at as updated_at, tdo.qty as stock_qty, null as stockin_id, null as stockout_id, null as checkout_id, r.id as refund_id, users.fullname');
        $builderRefund->join('trans_h_refund as r', 'r.trans_h_orderid = tdo.trans_h_orderid');
        $builderRefund->join('trans_h_order as o', 'o.id = r.trans_h_orderid');
        $builderRefund->join('users', 'users.id = r.updated_by');
        $builderRefund->where('tdo.medicine_id', $medicineId);

        // Gabungkan dengan UNION
        $query = $builderIn->getCompiledSelect()
            . ' UNION ALL ' .
            $builderOut->getCompiledSelect()
            . ' UNION ALL ' .
            $builderTrans->getCompiledSelect()
            . ' UNION ALL ' .
            $builderRefund->getCompiledSelect();

        $logs = $db->query($query)->getResult();

        return $this->response->setJSON($logs);
    }

    public function order()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('trans_h_order');
        $builder->select('
        trans_h_order.id,
        trans_h_order.no_trx,
        trans_h_order.status,
        trans_h_order.username,
        trans_h_order.total_price,
        trans_h_order.created_at,
        status_trx.id AS status_id,
        status_trx.name AS status_name
    ');
        $builder->join('status_trx', 'status_trx.id = trans_h_order.status', 'left');
        $builder->orderBy('trans_h_order.created_at', 'DESC');

        $orders = $builder->get()->getResultArray();

        $data = [
            'title' => 'List Pesanan',
            'orders' => $orders
        ];
        return view('admin/order/index', $data);
    }

    public function detail_order($id)
    {
        $db = \Config\Database::connect();
        $statusModel = new StatusTrx();
        $statusList = $statusModel->findAll();

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
            'statusList' => $statusList,
            'statusLogs'  => $statusLogs
        ];
        return view('admin/order/detail', $data);
    }

    public function updateStatus($id)
    {
        $model         = new TransHOrder();
        $logModel      = new LogStatusTrx();
        $refundModel   = new TransHRefund();
        $orderDetail   = new TransDOrder();     // pastikan model ini ada
        $stockModel    = new TotalMdStock();    // pastikan model ini juga ada

        $userId   = user()->id;
        $statusId = $this->request->getPost('status_id');

        if (!$statusId) {
            return redirect()->back()->with('error', 'Status tidak boleh kosong.');
        }

        // Ambil data transaksi
        $transaction = $model->find($id);
        if (!$transaction) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan.');
        }

        // Simpan update status di transaksi
        $updated = $model->update($id, [
            'status'     => $statusId,
            'updated_by' => $userId
        ]);

        if ($updated) {
            // Simpan juga ke tabel log_status_trx
            $logModel->insert([
                'trans_h_orderid' => $id,
                'status_id'       => $statusId,
                'updated_by'      => $userId
            ]);

            // Jika status adalah 4 (Refund), simpan ke trans_h_refund & kembalikan stok
            if ($statusId == 4) {
                // Insert ke refund
                $refundModel->insert([
                    'trans_h_orderid' => $id,
                    'no_trx'          => $transaction['no_trx'],
                    'updated_by'          => $userId
                ]);

                // Ambil semua item detail order
                $items = $orderDetail->where('trans_h_orderid', $id)->findAll();

                foreach ($items as $item) {
                    $stock = $stockModel->where('medicine_id', $item['medicine_id'])->first();

                    if ($stock) {
                        $newQty = $stock['qty'] + $item['qty'];
                        $stockModel->update($stock['id'], ['qty' => $newQty]);
                    }
                }
            }

            return redirect()->to('order')->with('success', 'Status berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui status.');
        }
    }

    public function refund_list()
    {
        $db = \Config\Database::connect();

        // Subquery: Ambil data created_at terbaru untuk setiap no_trx
        $subquery = $db->table('trans_h_refund thr')
            ->select('thr.no_trx, MAX(tdr.created_at) AS latest_created')
            ->join('trans_d_refund tdr', 'tdr.trans_h_refund = thr.id', 'left')
            ->groupBy('thr.no_trx');

        $builder = $db->table('trans_h_refund thr');
        $builder->select('thr.id as refund_id, thr.no_trx, thr.status, tdr.name, tdr.created_at, thr.status as status_id, sr.name as status_name, tho.total_price');
        $builder->join('trans_h_order tho', 'tho.id = thr.trans_h_orderid', 'left');
        $builder->join('trans_d_refund tdr', 'tdr.trans_h_refund = thr.id', 'left');
        $builder->join('status_refund sr', 'sr.id = thr.status', 'left');

        // Join ke subquery untuk ambil data terbaru tiap no_trx
        $builder->join('(' . $subquery->getCompiledSelect() . ') latest', 'latest.no_trx = thr.no_trx AND latest.latest_created = tdr.created_at', 'inner');

        $builder->where('thr.status IS NOT NULL');
        $builder->orderBy('tdr.created_at', 'DESC');

        $refunds = $builder->get()->getResultArray();

        $data = [
            'title' => 'List Refund',
            'refunds' => $refunds
        ];

        return view('admin/refund/index', $data);
    }

    public function edit_refund($id)
    {
        $userId = user()->id;

        $refundHModel = new TransHRefund();
        $refundDModel = new TransDRefund();
        $bankModel = new Bank();
        $orderModel = new TransHOrder();
        $logModel = new LogStatusRefund();
        $statusModel = new StatusRefund();
        $approvalModel = new TransDApprovalRefund();
        $statusList = $statusModel->findAll();
        $refundApproval = $approvalModel->where('trans_h_refund', $id)->first();


        // Ambil data header refund
        $refundHeader = $refundHModel
            ->select('trans_h_refund.*, status_refund.name as status_label')
            ->join('status_refund', 'status_refund.id = trans_h_refund.status', 'left')
            ->where('trans_h_refund.id', $id)
            ->first();
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

        $data = [
            'title'        => 'Detail Refund',
            'refundHeader' => $refundHeader,
            'refundDetail' => $refundDetail,
            'banks'        => $banks,
            'transactions' => $transactions,
            'logStatus'    => $logStatus,
            'statusList'    => $statusList,
            'refundApproval'  => $refundApproval
        ];

        return view('admin/refund/edit', $data);
    }

    public function update_refund($id)
    {
        // dd($id);
        $approvalModel = new \App\Models\TransDApprovalRefund();
        $refundHModel  = new \App\Models\TransHRefund();
        $logModel      = new \App\Models\LogStatusRefund();

        $status = $this->request->getPost('status_refund');
        $rejectReason = $this->request->getPost('reject_reason');
        $file = $this->request->getFile('evidence_refund');
        $userId = user()->id;

        // Validasi dasar
        if (!$status) {
            return redirect()->back()->withInput()->with('error', 'Status refund harus dipilih.');
        }

        $dataApproval = [
            'trans_h_refund' => $id,
            'updated_by'     => $userId,
        ];

        // Jika status adalah Disetujui (id = 2), proses upload bukti
        if ($status == 2) {
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move('uploads/refund_evidence/', $newName);
                $dataApproval['evidence_refund'] = $newName;
            } else {
                return redirect()->back()->withInput()->with('error', 'File bukti transfer tidak valid.');
            }
        }

        // Jika status adalah Ditolak (id = 3), simpan alasan penolakan
        if ($status == 3) {
            if (!$rejectReason) {
                return redirect()->back()->withInput()->with('error', 'Alasan penolakan wajib diisi.');
            }
            $dataApproval['reject_reason'] = $rejectReason;
        }

        // Simpan ke tabel trans_d_approval_refund
        $approvalModel->insert($dataApproval);

        // Update status pada trans_h_refund
        $refundHModel->update($id, [
            'status' => $status,
        ]);

        // Insert ke log_status_refund
        $logModel->insert([
            'trans_h_refund' => $id,
            'status_refund'  => $status,
            'updated_by'     => $userId,
        ]);

        return redirect()->to('/refund')->with('success', 'Status refund berhasil diperbarui.');
    }
}
