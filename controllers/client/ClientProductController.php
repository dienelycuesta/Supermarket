<?php
class ClientProductController extends Controller {
    public function show($slug) {
        $product = Product::findBySlug($slug);
        if (!$product) $product = Product::find((int)$slug);
        if (!$product || !$product['is_active']) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'No Encontrado']);
            return;
        }
        $category = $product['category_id'] ? Category::find($product['category_id']) : null;
        $related = $product['category_id'] ? Product::getByCategory($product['category_id'], 1, 4) : ['data' => []];
        $this->render('client/product/show', [
            'pageTitle' => $product['name'],
            'product' => $product, 'category' => $category,
            'relatedProducts' => $related['data'],
        ]);
    }
}
