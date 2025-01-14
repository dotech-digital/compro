<?php

namespace App\Http\Controllers;

use App\Models\Master\News;
use App\Models\Product\Product;
use App\Models\Setting\Menu;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->menu = "DASHBOARD";
        $this->url = url('/dashboard');
        $this->title = "Dashboard";
        $this->view = "dashboard.";
    }

    public function index()
    {
        $breadcrumbs = [
            ["url" => "#", 'title' => "Home"],
            ['url' => $this->url, 'title' => $this->title],
        ];

        return view($this->view . 'index')->with('title', $this->title)
            ->with('url', $this->url)
            ->with('breadcrumbs', $breadcrumbs);
    }

    public function landing()
    {
        $news = News::with('user')->get();
        $products = Product::all();
        return view('landing')
            ->with('news', $news)
            ->with('products', $products);
    }
}
