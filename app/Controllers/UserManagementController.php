<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Entities\User;
use Myth\Auth\Password;

class UserManagementController extends BaseController
{
    public function index()
    {
        $data['title'] = 'List Admin';

        $db = \Config\Database::connect();
        $build = $db->table('users');
        $build->select('users.*');
        $build->join('auth_groups_users', 'auth_groups_users.user_id = users.id'); // ✅ diperbaiki
        $build->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        $build->where('auth_groups.id = 1');
        $query = $build->get();

        $data['users'] = $query->getResult();

        return view('admin/user_management/index', $data);
    }

    public function add()
    {
        $data['title'] = 'Form Tambah Admin';
        return view('admin/user_management/add', $data);
    }

    public function store()
    {
        $users = new UserModel();

        // Validasi form
        $rules = [
            'username' => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[5]',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Isi user data ke Entity
        $user = new User([
            'username' => $this->request->getPost('username'),
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'), // ✅ Ini akan auto-hash
            'active'   => 1,
        ]);

        // Save pakai entity
        if (!$users->save($user)) {
            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        // Ambil ID user baru
        $userId = $users->getInsertID();

        // Masukkan ke grup admin
        $groupModel = new \Myth\Auth\Models\GroupModel();
        $group = $groupModel->where('name', 'admin')->first();

        if ($group) {
            $groupModel->addUserToGroup($userId, (int) $group->id);
        }

        return redirect()->to('admin')->with('success', 'User admin berhasil dibuat.');
    }
}
