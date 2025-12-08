<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::all();
        return view('slider.index', compact('sliders'));
    }

    public function create()
    {
        return view('slider.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image',
            'link' => 'nullable|url',
        ]);

        // Logic to store the slider
        $slider = new Slider();
        $slider->title = $validated['title'];
        if ($request->hasFile('image')) {
            $slider->image_path = $request->file('image')->store('sliders', 'public');
        }
        $slider->link = $validated['link'] ?? null;
        $slider->save();

        return redirect()->route('products.index')->with('success', 'Slider created successfully.');
    }

    public function edit($id)
    {
        return view('slider.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image',
            'link' => 'nullable|url',
        ]);

        // Logic to update the slider
        $slider = Slider::findOrFail($id);
        $slider->title = $validated['title'];
        if ($request->hasFile('image')) {
            $slider->image_path = $request->file('image')->store('sliders', 'public');
        }
        $slider->link = $validated['link'] ?? null;
        $slider->save();

        return redirect()->route('products.index')->with('success', 'Slider updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete the slider
        $slider = Slider::findOrFail($id);
        $slider->delete();
        return redirect()->route('products.index')->with('success', 'Slider deleted successfully.');
    }

}
