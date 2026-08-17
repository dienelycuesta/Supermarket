<?php
class Paginator {
    private $total;
    private $perPage;
    private $currentPage;
    private $totalPages;

    public function __construct($total, $perPage, $currentPage) {
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = max(1, ceil($total / $perPage));
    }

    public function getOffset() { return ($this->currentPage - 1) * $this->perPage; }
    public function getLimit() { return $this->perPage; }
    public function getTotalPages() { return $this->totalPages; }
    public function hasNext() { return $this->currentPage < $this->totalPages; }
    public function hasPrev() { return $this->currentPage > 1; }
    public function getCurrentPage() { return $this->currentPage; }
    public function getTotal() { return $this->total; }

    public function render() {
        if ($this->totalPages <= 1) return '';
        $params = $_GET;
        $html = '<nav><ul class="pagination justify-content-center">';
        // Previous
        $html .= '<li class="page-item' . (!$this->hasPrev() ? ' disabled' : '') . '">';
        $params['page'] = $this->currentPage - 1;
        $html .= '<a class="page-link" href="?' . http_build_query($params) . '">&laquo;</a></li>';
        // Pages
        $start = max(1, $this->currentPage - 2);
        $end = min($this->totalPages, $this->currentPage + 2);
        if ($start > 1) {
            $params['page'] = 1;
            $html .= '<li class="page-item"><a class="page-link" href="?' . http_build_query($params) . '">1</a></li>';
            if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        for ($i = $start; $i <= $end; $i++) {
            $params['page'] = $i;
            $active = $i === $this->currentPage ? ' active' : '';
            $html .= '<li class="page-item' . $active . '"><a class="page-link" href="?' . http_build_query($params) . '">' . $i . '</a></li>';
        }
        if ($end < $this->totalPages) {
            if ($end < $this->totalPages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            $params['page'] = $this->totalPages;
            $html .= '<li class="page-item"><a class="page-link" href="?' . http_build_query($params) . '">' . $this->totalPages . '</a></li>';
        }
        // Next
        $html .= '<li class="page-item' . (!$this->hasNext() ? ' disabled' : '') . '">';
        $params['page'] = $this->currentPage + 1;
        $html .= '<a class="page-link" href="?' . http_build_query($params) . '">&raquo;</a></li>';
        $html .= '</ul></nav>';
        return $html;
    }
}
