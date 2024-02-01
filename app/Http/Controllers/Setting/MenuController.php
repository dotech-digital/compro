<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->menu = "SETTING.MENU";
        $this->url = url('setting/menu');
        $this->title = "Menu";
        $this->view = "setting.menu.";
    }

    public function index()
    {
        $this->authAction('read');
        $this->authCheckDetailAccess();

        $breadcrumbs = [
            ['url' => url('/dashboard'), 'title' => 'Home'],
            ["url" => "#", "title" => "Setting"],
            ['url' => $this->url, 'title' => $this->title],
        ];

        return view($this->view . 'index')->with('title', $this->title)
            ->with('url', $this->url)
            ->with('breadcrumbs', $breadcrumbs)
            ->with('allowAccess', $this->authAccess());
    }

    public function list()
    {
        $this->authAction('read');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $data = Menu::with('parent')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        $this->authAction('create');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $menu = Menu::where('level', 1)->get();

        $r = Route::getRoutes();

        $routes = [];
        foreach ($r as $route) {
            $name = $route->getName();
            $uri = $route->uri;
            $std = new \stdClass();
            $std->name = $name;
            $std->uri = $uri;
            $routes[] = $std;
        }

        return view($this->view . 'action')
            ->with('title', 'Add ' . $this->title)
            ->with('url', $this->url)
            ->with('menu', $menu)
            ->with('routes', $routes);
    }

    public function store(Request $request)
    {
        $this->authAction('create');
        if ($this->authCheckDetailAccess() !== true) return $this->authCheckDetailAccess();

        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:menus,code',
            'name' => 'required',
            'url' => 'required',
            'order' => 'required',
            'tag' => 'required',
            'icon' => 'required',
            'is_active' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            return $this->setResponse(false, "Validation Error", $errors);
        }

        $data = $request->all();
        $data['level'] = $request->parent_id == null ? 1 : 2;

        $news = Menu::create($data);

        if ($news) {
            return $this->setResponse(true, $this->saveSuccessMessage);
        } else {
            return $this->setResponse(false, $this->saveFailedMessage);
        }
    }
}
