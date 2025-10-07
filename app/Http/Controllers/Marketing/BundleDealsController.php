<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Marketing\BundleDealRepositoryInterface;
use Illuminate\Http\Request;

class BundleDealsController extends Controller
{
    protected $bundleRepo;

    public function __construct(BundleDealRepositoryInterface $bundleRepository)
    {
        $this->bundleRepo = $bundleRepository;
    }

    public function index(Request $request)
    {
        return view('pages.marketing.bundle-deals.index');
    }
}
