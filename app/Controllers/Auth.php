<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        $session = session();
        $session->set([
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'isLoggedIn' => true,
            'role'       => $user['role'],
        ]);

        return redirect()->to('/')->with('message', 'Login berhasil!');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function attemptRegister()
    {
        $rules = [
            'username'      => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[8]',
            'pass_confirm'  => 'required_with[password]|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $userModel = new UserModel();

        $userModel->save([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role'     => 'user',
        ]);

        return redirect()->to('login')->with('message', 'Registrasi berhasil! Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('/'))->with('message', 'Anda telah logout.');
    }

    public function profile()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Anda harus login untuk mengakses profil.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($session->get('user_id'));

        if (! $user) {
            return redirect()->to('logout')->with('error', 'Data pengguna tidak ditemukan. Silakan login kembali.');
        }

        $data = [
            'user' => $user,
            'validation' => \Config\Services::validation(),
        ];

        return view('auth/profile', $data);
    }

    public function updateProfile()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Anda harus login untuk memperbarui profil.');
        }

        $userId = $session->get('user_id');
        $userModel = new UserModel();
        $currentUser = $userModel->find($userId);

        if (! $currentUser) {
            return redirect()->to('logout')->with('error', 'Data pengguna tidak ditemukan. Silakan login kembali.');
        }

        $rules = [
            'username' => 'required|alpha_numeric_space|min_length[3]',
            'email'    => 'required|valid_email',
        ];

        $password = $this->request->getPost('password');
        $passConfirm = $this->request->getPost('pass_confirm');

        if (!empty($password)) {
            $rules['password'] = 'required|min_length[8]';
            $rules['pass_confirm'] = 'required_with[password]|matches[password]';
        }

        if ($this->request->getPost('username') !== $currentUser['username']) {
            $rules['username'] .= '|is_unique[users.username,id,' . $userId . ']';
        }
        if ($this->request->getPost('email') !== $currentUser['email']) {
            $rules['email'] .= '|is_unique[users.email,id,' . $userId . ']';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToUpdate = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ];

        if (!empty($password)) {
            $dataToUpdate['password'] = $password;
        }

        $userModel->update($userId, $dataToUpdate);

        $session->set('username', $dataToUpdate['username']);
        $session->set('email', $dataToUpdate['email']);

        return redirect()->to('profile')->with('message', 'Profil berhasil diperbarui.');
    }
}
