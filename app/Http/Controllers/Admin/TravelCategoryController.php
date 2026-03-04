<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TravelCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(in_array(optional($request->user())->role, ['admin']), 403);
            return $next($request);
        });
    }

    public function index(): View
    {
        $items = TravelCategory::orderBy('name')->paginate(15);
        return view('admin.travel_categories.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.travel_categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:travel_categories,code'],
            'name' => ['required','string','max:255'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = $request->boolean('active', true);
        TravelCategory::create($data);
        return redirect()->route('admin.travel-categories.index');
    }

    public function edit(TravelCategory $travel_category): View
    {
        return view('admin.travel_categories.edit', ['category' => $travel_category]);
    }

    public function update(Request $request, TravelCategory $travel_category): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:travel_categories,code,'.$travel_category->id],
            'name' => ['required','string','max:255'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = $request->boolean('active', true);
        $travel_category->update($data);
        return redirect()->route('admin.travel-categories.index');
    }

    public function destroy(TravelCategory $travel_category): RedirectResponse
    {
        $travel_category->delete();
        return redirect()->route('admin.travel-categories.index');
    }
}

