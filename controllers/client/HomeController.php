<?php
class HomeController extends Controller {
    public function index() {
        $this->render('client/home/index', [
            'pageTitle' => 'Inicio',
            'featuredProducts' => Product::getFeatured(8),
            'onSaleProducts' => Product::getOnSale(10),
            'categories' => Category::getActive(),
        ]);
    }
}
