<?php
class AccountController extends Controller {
    public function index() {
        $this->requireAuth();
        $user = Auth::user();
        $addresses = CustomerAddress::getByUser($user['id']);
        $recentOrders = Order::getByUser($user['id'], 1);
        $this->render('client/account/index', [
            'pageTitle' => 'Mi Cuenta', 'user' => $user,
            'addresses' => $addresses, 'recentOrders' => $recentOrders['data'],
        ]);
    }

    public function updateProfile() {
        $this->requireAuth();
        $this->validateCSRF();
        $data = [
            'first_name' => sanitize($_POST['first_name'] ?? ''),
            'last_name' => sanitize($_POST['last_name'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
        ];
        User::updateProfile(Auth::user()['id'], $data);
        $_SESSION['user'] = array_merge(Auth::user(), $data);
        Session::flash('success', 'Perfil actualizado exitosamente.');
        $this->redirect(url('account'));
    }

    public function changePassword() {
        $this->requireAuth();
        $this->validateCSRF();
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['new_password_confirmation'] ?? '';
        $user = User::find(Auth::user()['id']);
        if (!password_verify($current, $user['password'])) {
            Session::flash('error', 'La contraseña actual es incorrecta.');
            $this->redirect(url('account'));
        }
        if (strlen($new) < 8 || $new !== $confirm) {
            Session::flash('error', 'La nueva contraseña debe tener al menos 8 caracteres y coincidir con la confirmación.');
            $this->redirect(url('account'));
        }
        User::changePassword(Auth::user()['id'], $new);
        Session::flash('success', 'Contraseña cambiada exitosamente.');
        $this->redirect(url('account'));
    }

    public function addAddress() {
        $this->requireAuth();
        $this->validateCSRF();
        $data = [
            'user_id' => Auth::user()['id'],
            'label' => sanitize($_POST['label'] ?? 'Home'),
            'address_line1' => sanitize($_POST['address_line1'] ?? ''),
            'address_line2' => sanitize($_POST['address_line2'] ?? ''),
            'city' => sanitize($_POST['city'] ?? ''),
            'state' => sanitize($_POST['state'] ?? ''),
            'postal_code' => sanitize($_POST['postal_code'] ?? ''),
            'is_default' => isset($_POST['is_default']) ? 1 : 0,
        ];
        if (!empty($_POST['latitude'])) $data['latitude'] = (float)$_POST['latitude'];
        if (!empty($_POST['longitude'])) $data['longitude'] = (float)$_POST['longitude'];
        CustomerAddress::create($data);
        Session::flash('success', 'Dirección agregada exitosamente.');
        $this->redirect(url('account'));
    }

    public function deleteAddress($id) {
        $this->requireAuth();
        $this->validateCSRF();
        $addr = CustomerAddress::find($id);
        if ($addr && $addr['user_id'] == Auth::user()['id']) {
            CustomerAddress::delete($id);
            Session::flash('success', 'Dirección eliminada.');
        }
        $this->redirect(url('account'));
    }
}
