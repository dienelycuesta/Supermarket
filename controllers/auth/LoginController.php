<?php
class LoginController extends Controller {
    public function showLogin() {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? url('admin/dashboard') : url(''));
        }
        $this->render('auth/login', ['pageTitle' => 'Iniciar Sesión']);
    }

    public function login() {
        $this->validateCSRF();
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            Session::flash('error', 'Correo y contraseña son requeridos.');
            $this->redirect(url('login'));
        }

        if (Auth::login($email, $password)) {
            $sid = session_id();
            $user = Auth::user();
            Cart::mergeGuestToUser($sid, $user['id']);
            if (in_array($user['role'], ['admin', 'manager', 'cashier'])) {
                $this->redirect(url('admin/dashboard'));
            }
            $this->redirect(url(''));
        }

        Session::flash('error', 'Credenciales inválidas o cuenta bloqueada.');
        $this->redirect(url('login'));
    }
}
