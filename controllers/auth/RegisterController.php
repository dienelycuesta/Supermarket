<?php
class RegisterController extends Controller {
    public function showRegister() {
        if (Auth::check()) $this->redirect(url(''));
        $this->render('auth/register', ['pageTitle' => 'Registro']);
    }

    public function register() {
        $this->validateCSRF();
        $data = [
            'first_name' => sanitize($_POST['first_name'] ?? ''),
            'last_name' => sanitize($_POST['last_name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ];

        $validation = Validator::validate($data, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!$validation['valid']) {
            $_SESSION['old_input'] = $data;
            Session::flash('errors', $validation['errors']);
            $this->redirect(url('register'));
        }

        $data['role'] = 'customer';
        User::createUser($data);
        Auth::login($data['email'], $data['password']);
        Session::flash('success', 'Cuenta creada exitosamente!');
        $this->redirect(url(''));
    }
}
