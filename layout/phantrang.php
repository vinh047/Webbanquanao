<?php
class Pagination {
    public $limit;
    public $page;
    public $totalItems;
    public $totalPages;

    public function __construct($totalItems, $limit = 10, $currentPage = 1) {
        $this->limit = $limit;
        $this->totalItems = $totalItems;
        $this->totalPages = ceil($totalItems / $limit);
        $this->page = max(1, min($currentPage, $this->totalPages));
    }

    public function offset() {
        return ($this->page - 1) * $this->limit;
    }

    public function getTotalPages() {
        return $this->totalPages;
    }

    public function render($baseQueryParams = []) {
        $prevPage = max(1, $this->page - 1);
        $nextPage = min($this->totalPages, $this->page + 1);

        // Build query string base
        $queryStr = http_build_query(array_merge($baseQueryParams, ['pageproduct' => '']));

        echo '
        <section class="phantrang py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-auto text-center d-flex align-items-center gap-3">
                        <a href="#" class="page-link-custom btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center"
                           style="width: 40px; height: 40px;" data-page="' . $prevPage . '">
                            <i class="fa fa-chevron-left text-dark"></i>
                        </a>

                        <span>
                            <input id="pageInput" type="number" min="1" max="' . $this->totalPages . '" value="' . $this->page . '"
                                class="form-control d-inline-block text-center" style="width: 60px; display: inline-block;"/>
                            / ' . $this->totalPages . '
                        </span>

                        <a href="#" class="page-link-custom btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center"
                           style="width: 40px; height: 40px;" data-page="' . $nextPage . '">
                            <i class="fa fa-chevron-right text-dark"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>';
    }
}
?>