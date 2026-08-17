<?php
class AdminCategoryController extends Controller {
    public function index() {
        $this->requireAdmin();
        $categories = Category::getWithProductCount();
        $this->render('admin/categories/index', ['pageTitle' => 'Categorías', 'categories' => $categories]);
    }

    public function create() {
        $this->requireAdmin();
        $this->render('admin/categories/create', [
            'pageTitle' => 'Agregar Categoría',
            'parentCategories' => Category::getParentCategories(),
        ]);
    }

    public function store() {
        $this->requireAdmin();
        $this->validateCSRF();
        $data = [
            'name' => sanitize($_POST['name'] ?? ''),
            'slug' => generateSlug($_POST['name'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'parent_id' => (int)($_POST['parent_id'] ?? 0) ?: null,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
        ];
        $validation = Validator::validate($data, ['name' => 'required|max:100']);
        if (!$validation['valid']) {
            $_SESSION['old_input'] = $_POST;
            Session::flash('errors', $validation['errors']);
            $this->redirect(url('admin/categories/create'));
        }
        if (!empty($_FILES['image']['name'])) {
            $upload = ImageUpload::upload($_FILES['image']);
            if ($upload['success']) $data['image'] = $upload['filename'];
        }
        Category::create($data);
        Session::flash('success', 'Categoría creada exitosamente.');
        $this->redirect(url('admin/categories'));
    }

    public function edit($id) {
        $this->requireAdmin();
        $category = Category::find($id);
        if (!$category) $this->redirect(url('admin/categories'));
        $this->render('admin/categories/edit', [
            'pageTitle' => 'Editar Categoría', 'category' => $category,
            'parentCategories' => Category::getParentCategories(),
        ]);
    }

    public function update($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        $data = [
            'name' => sanitize($_POST['name'] ?? ''),
            'slug' => generateSlug($_POST['name'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'parent_id' => (int)($_POST['parent_id'] ?? 0) ?: null,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
        ];
        if (!empty($_FILES['image']['name'])) {
            $old = Category::find($id);
            $upload = ImageUpload::upload($_FILES['image']);
            if ($upload['success']) {
                if ($old && $old['image']) ImageUpload::delete($old['image']);
                $data['image'] = $upload['filename'];
            }
        }
        Category::update($id, $data);
        Session::flash('success', 'Categoría actualizada exitosamente.');
        $this->redirect(url('admin/categories'));
    }

    public function delete($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        if (Category::hasProducts($id)) {
            Session::flash('error', 'No se puede eliminar una categoría con productos asociados.');
            $this->redirect(url('admin/categories'));
        }
        Category::delete($id);
        Session::flash('success', 'Categoría eliminada exitosamente.');
        $this->redirect(url('admin/categories'));
    }
}
