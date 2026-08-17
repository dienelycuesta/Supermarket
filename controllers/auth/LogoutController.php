<?php
class LogoutController extends Controller {
    public function logout() {
        Auth::logout();
        session_start();
        Session::flash('success', 'Sesión cerrada exitosamente.');
        $this->redirect(url('login'));
    }
}
