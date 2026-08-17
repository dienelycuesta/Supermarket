<?php
class AdminProductController extends Controller {
    public function index() {
        $this->requireAdmin();
        $search = sanitize($_GET['search'] ?? '');
        $categoryId = (int)($_GET['category_id'] ?? 0);
        $page = $this->getPage();
        $products = Product::adminSearch($search, $categoryId, $page);
        $categories = Category::getActive();
        $this->render('admin/products/index', array_merge($products, [
            'pageTitle' => 'Productos', 'categories' => $categories,
            'search' => $search, 'categoryId' => $categoryId,
        ]));
    }

    public function create() {
        $this->requireAdmin();
        $this->render('admin/products/create', [
            'pageTitle' => 'Agregar Producto',
            'categories' => Category::getActive(),
            'suppliers' => Supplier::getActive(),
        ]);
    }

    public function store() {
        $this->requireAdmin();
        $this->validateCSRF();
        $data = [
            'name' => sanitize($_POST['name'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
            'supplier_id' => (int)($_POST['supplier_id'] ?? 0) ?: null,
            'cost_price' => (float)($_POST['cost_price'] ?? 0),
            'sale_price' => (float)($_POST['sale_price'] ?? 0),
            'compare_price' => (float)($_POST['compare_price'] ?? 0) ?: null,
            'stock' => (int)($_POST['stock'] ?? 0),
            'min_stock' => (int)($_POST['min_stock'] ?? 5),
            'max_stock' => (int)($_POST['max_stock'] ?? 1000),
            'unit' => sanitize($_POST['unit'] ?? 'unit'),
            'tax_rate' => (float)($_POST['tax_rate'] ?? 18),
            'weight' => (float)($_POST['weight'] ?? 0) ?: null,
            'brand' => sanitize($_POST['brand'] ?? ''),
            'barcode' => sanitize($_POST['barcode'] ?? '') ?: null,
            'sku' => sanitize($_POST['sku'] ?? '') ?: generateSKU(),
            'slug' => generateSlug($_POST['name'] ?? ''),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        $validation = Validator::validate($data, [
            'name' => 'required|max:255',
            'sale_price' => 'required|numeric',
        ]);
        if ($_POST['barcode'] ?? '') {
            $bv = Validator::validate(['barcode' => $_POST['barcode']], ['barcode' => 'ean13']);
            if (!$bv['valid']) $validation['errors'] = array_merge($validation['errors'], $bv['errors']);
            $validation['valid'] = $validation['valid'] && $bv['valid'];
        }

        if (!$validation['valid']) {
            $_SESSION['old_input'] = $_POST;
            Session::flash('errors', $validation['errors']);
            $this->redirect(url('admin/products/create'));
        }

        // Ensure unique slug
        $baseSlug = $data['slug'];
        $i = 1;
        while (Product::findBySlug($data['slug'])) {
            $data['slug'] = $baseSlug . '-' . $i++;
        }

        if (!empty($_FILES['image']['name'])) {
            $upload = ImageUpload::upload($_FILES['image']);
            if ($upload['success']) $data['image'] = $upload['filename'];
        }

        $id = Product::create($data);
        if ($data['stock'] > 0) {
            InventoryMovement::record($id, 'entry', $data['stock'], 'manual', null, 'Stock inicial', Auth::id());
        }
        Session::flash('success', 'Producto creado exitosamente.');
        $this->redirect(url('admin/products'));
    }

    public function show($id) {
        $this->requireAdmin();
        $product = Product::getWithCategory($id);
        if (!$product) { $this->redirect(url('admin/products')); }
        $movements = InventoryMovement::getByProduct($id);
        $this->render('admin/products/show', [
            'pageTitle' => $product['name'], 'product' => $product,
            'movements' => $movements['data'], 'movPaginator' => $movements['paginator'],
        ]);
    }

    public function edit($id) {
        $this->requireAdmin();
        $product = Product::find($id);
        if (!$product) { $this->redirect(url('admin/products')); }
        $this->render('admin/products/edit', [
            'pageTitle' => 'Editar Producto', 'product' => $product,
            'categories' => Category::getActive(),
            'suppliers' => Supplier::getActive(),
        ]);
    }

    public function update($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        $product = Product::find($id);
        if (!$product) { $this->redirect(url('admin/products')); }

        $data = [
            'name' => sanitize($_POST['name'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
            'supplier_id' => (int)($_POST['supplier_id'] ?? 0) ?: null,
            'cost_price' => (float)($_POST['cost_price'] ?? 0),
            'sale_price' => (float)($_POST['sale_price'] ?? 0),
            'compare_price' => (float)($_POST['compare_price'] ?? 0) ?: null,
            'min_stock' => (int)($_POST['min_stock'] ?? 5),
            'max_stock' => (int)($_POST['max_stock'] ?? 1000),
            'unit' => sanitize($_POST['unit'] ?? 'unit'),
            'tax_rate' => (float)($_POST['tax_rate'] ?? 18),
            'weight' => (float)($_POST['weight'] ?? 0) ?: null,
            'brand' => sanitize($_POST['brand'] ?? ''),
            'barcode' => sanitize($_POST['barcode'] ?? '') ?: null,
            'sku' => sanitize($_POST['sku'] ?? $product['sku']),
            'slug' => generateSlug($_POST['name'] ?? ''),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($data['slug'] !== $product['slug']) {
            $baseSlug = $data['slug'];
            $i = 1;
            while (($found = Product::findBySlug($data['slug'])) && $found['id'] != $id) {
                $data['slug'] = $baseSlug . '-' . $i++;
            }
        }

        if (!empty($_FILES['image']['name'])) {
            $upload = ImageUpload::upload($_FILES['image']);
            if ($upload['success']) {
                if ($product['image']) ImageUpload::delete($product['image']);
                $data['image'] = $upload['filename'];
            }
        }

        Product::update($id, $data);
        Session::flash('success', 'Producto actualizado exitosamente.');
        $this->redirect(url('admin/products'));
    }

    public function delete($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        $product = Product::find($id);
        if ($product) {
            if ($product['image']) ImageUpload::delete($product['image']);
            Product::delete($id);
            Session::flash('success', 'Producto eliminado exitosamente.');
        }
        $this->redirect(url('admin/products'));
    }
}
