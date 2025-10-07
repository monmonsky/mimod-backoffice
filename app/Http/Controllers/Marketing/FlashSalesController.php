<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Marketing\FlashSaleRepositoryInterface;
use Illuminate\Http\Request;

class FlashSalesController extends Controller
{
    protected $flashSaleRepo;

    public function __construct(FlashSaleRepositoryInterface $flashSaleRepository)
    {
        $this->flashSaleRepo = $flashSaleRepository;
    }

    public function index(Request $request)
    {
        return view('pages.marketing.flash-sales.index');
    }
}
