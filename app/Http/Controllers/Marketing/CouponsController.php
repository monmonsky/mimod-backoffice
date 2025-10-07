<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Marketing\CouponRepositoryInterface;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    protected $couponRepo;

    public function __construct(CouponRepositoryInterface $couponRepository)
    {
        $this->couponRepo = $couponRepository;
    }

    public function index(Request $request)
    {
        return view('pages.marketing.coupons.index');
    }
}
