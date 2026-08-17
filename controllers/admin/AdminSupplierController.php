<?php
class AdminSupplierController extends Controller {
    public function index() {
        $this->requireAdmin();
        $search = sanitize($_GET['search'] ?? '');
        $page = $this->getPage();
        $result = Supplier::search($search, $page);
        $this->render('admin/suppliers/index', array_merge($result, ['pageTitle' => 'Proveedores', 'search' => $search]));
    }

    public function create() {
        $this->requireAdmin();
        $this->render('admin/suppliers/create', ['pageTitle' => 'Agregar Proveedor']);
    }

    public function store() {
        $this->requireAdmin();
        $this->validateCSRF();
        $data = [];
        foreach (['name','contact_person','email','phone','rnc','address','city','notes','status'] as $f) {
            $data[$f] = sanitize($_POST[$f] ?? '');
        }
        $v = Validator::validate($data, ['name' => 'required|max:200']);
        if (!$v['valid']) {
            $_SESSION['old_input'] = $_POST;
            Session::flash('errors', $v['errors']);
            $this->redirect(url('admin/suppliers/create'));
        }
        Supplier::create($data);
        Session::flash('success', 'Proveedor creado exitosamente.');
        $this->redirect(url('admin/suppliers'));
    }

    public function show($id) {
        $this->requireAdmin();
        $supplier = Supplier::find($id);
        if (!$supplier) $this->redirect(url('admin/suppliers'));
        $products = Product::where("supplier_id = ?", [$id], 'name ASC');
        $this->render('admin/suppliers/show', ['pageTitle' => $supplier['name'], 'supplier' => $supplier, 'products' => $products]);
    }

    public function edit($id) {
        $this->requireAdmin();
        $supplier = Supplier::find($id);
        if (!$supplier) $this->redirect(url('admin/suppliers'));
        $this->render('admin/suppliers/edit', ['pageTitle' => 'Editar Proveedor', 'supplier' => $supplier]);
    }

    public function update($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        $data = [];
        foreach (['name','contact_person','email','phone','rnc','address','city','notes','status'] as $f) {
            $data[$f] = sanitize($_POST[$f] ?? '');
        }
        Supplier::update($id, $data);
        Session::flash('success', 'Proveedor actualizado exitosamente.');
        $this->redirect(url('admin/suppliers'));
    }

    public function delete($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        if (Supplier::hasProducts($id)) {
            Session::flash('error', 'No se puede eliminar un proveedor con productos asociados.');
            $this->redirect(url('admin/suppliers'));
        }
        Supplier::delete($id);
        Session::flash('success', 'Proveedor eliminado exitosamente.');
        $this->redirect(url('admin/suppliers'));
    }
}
