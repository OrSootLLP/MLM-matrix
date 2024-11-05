<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function product()
    {
        $pageTitle = 'Products';
        $products     = Product::orderBy('name', 'asc')->paginate(getPaginate());
        return view('admin.product.index', compact('pageTitle', 'products'));
    }

    public function productSave(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'image'         => [($request->id) ? 'nullable' : 'required', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'description'   => 'required',
            'price'         => 'required|numeric|min:0',
            'bv'            => 'required|min:0|integer',
            'pv'            => 'required|min:0|integer',
        ]);

        $product = new Product();
        if ($request->id) {
            $product = Product::findOrFail($request->id);
        }

        if ($request->hasFile('image')) {
            try {
                $path = getFilePath('product');
                $image = fileUploader($request->image, $path, getFileSize('product'), @$product->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }

        $product->name          = $request->name;
        if (isset($image)) {
            $product->image         = $image;
        }
        $product->description   = $request->description;
        $product->price         = $request->price;
        $product->bv            = $request->bv;
        $product->pv            = $request->pv;
        $product->save();

        $notify[] = ['success', 'Product saved successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Product::changeStatus($id);
    }

    public function remove($id)
    {
        $product = Product::find($id);
        fileManager()->removeFile(getFilePath('gateway') . '/' . $product->image);
        $product->delete();
        $notify[] = ['success', 'Product removed successfully'];
        return back()->withNotify($notify);
    }
}
