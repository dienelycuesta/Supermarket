<?php
class CatalogController extends Controller {
    public function index() {
        $search = sanitize($_GET['search'] ?? '');
        $categoryId = (int)($_GET['category'] ?? 0);
        $sort = sanitize($_GET['sort'] ?? 'name_asc');
        $page = $this->getPage();
        $products = Product::search($search, $categoryId, $sort, $page, ITEMS_PER_PAGE);
        $selectedCategory = $categoryId ? Category::find($categoryId) : null;
        $this->render('client/catalog/index', [
            'pageTitle' => $selectedCategory ? $selectedCategory['name'] : 'Catálogo',
            'products' => $products['data'],
            'paginator' => $products['paginator'],
            'categories' => Category::getWithProductCount(),
            'search' => $search,
            'categoryId' => $categoryId,
            'sort' => $sort,
            'selectedCategory' => $selectedCategory,
            'totalProducts' => $products['total'],
        ]);
    }
}
