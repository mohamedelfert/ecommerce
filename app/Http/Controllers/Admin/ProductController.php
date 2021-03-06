<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ProductDatatable;
use App\Http\Controllers\Controller;
use App\Models\MallProduct;
use App\Models\Product;
use App\Models\ProductOtherData;
use App\Models\ProductRelated;
use App\Models\Size;
use App\Models\Weight;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(ProductDatatable $product)
    {
        return $product->render('admin.products.index', ['title' => trans('admin.products')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $product = Product::create([
            'title' => '',
            'description' => '',
            'photo' => '',
            'size' => '',
            'weight' => '',
        ]);
        if (!empty($product)) {
            return redirect(adminUrl('products/' . $product->id . '/edit'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.product',
            ['title' => trans('admin.create_or_edit_product', ['title' => $product->title]),
                'product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'department_id' => 'required|numeric',
            'trade_mark_id' => 'required|numeric',
            'manufacture_id' => 'required|numeric',
            'color_id' => 'sometimes|nullable|numeric',
            'size' => 'required',
            'size_id' => 'sometimes|nullable|numeric',
            'weight' => 'required',
            'weight_id' => 'sometimes|nullable|numeric',
            'currency_id' => 'sometimes|nullable|numeric',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
            'offer_price' => 'sometimes|nullable|numeric',
            'offer_start_at' => 'sometimes|nullable|date',
            'offer_end_at' => 'sometimes|nullable|date',
            'reason' => 'sometimes|nullable',
            'status' => 'sometimes|nullable|in:pending,active,refused',
        ];
        $validate_msg_ar = [
            'title' => trans('admin.title'),
            'description' => trans('admin.description'),
            'department_id' => trans('admin.department_id'),
            'trade_mark_id' => trans('admin.trade_mark_id'),
            'manufacture_id' => trans('admin.manufacture_id'),
            'color_id' => trans('admin.color_id'),
            'size' => trans('admin.size'),
            'size_id' => trans('admin.size_id'),
            'weight' => trans('admin.weight'),
            'weight_id' => trans('admin.weight_id'),
            'currency_id' => trans('admin.currency_id'),
            'price' => trans('admin.price'),
            'stock' => trans('admin.stock'),
            'start_at' => trans('admin.start_at'),
            'end_at' => trans('admin.end_at'),
            'offer_price' => trans('admin.offer_price'),
            'offer_start_at' => trans('admin.offer_start_at'),
            'offer_end_at' => trans('admin.offer_end_at'),
            'reason' => trans('admin.reason'),
            'status' => trans('admin.status'),
        ];
        $data = $this->validate($request, $rules, $validate_msg_ar);

        $data['title'] = $request->title;
        $data['description'] = $request->description;
        $data['department_id'] = $request->department_id;
        $data['price'] = $request->price;
        $data['stock'] = $request->stock;
        $data['start_at'] = $request->start_at;
        $data['end_at'] = $request->end_at;
        $data['offer_price'] = $request->offer_price;
        $data['offer_start_at'] = $request->offer_start_at;
        $data['offer_end_at'] = $request->offer_end_at;

        if ($request->status === 'active' || $request->status === 'pending') {
            $data['status'] = $request->status;
            $data['reason'] = '';
        } else {
            $data['status'] = $request->status;
            $data['reason'] = $request->reason;
        }

        $data['color_id'] = $request->color_id;
        $data['trade_mark_id'] = $request->trade_mark_id;
        $data['manufacture_id'] = $request->manufacture_id;
        $data['size_id'] = $request->size_id;
        $data['size'] = $request->size;
        $data['weight_id'] = $request->weight_id;
        $data['weight'] = $request->weight;

        // for malls
        if ($request->has('malls')) {
            MallProduct::where('product_id', $id)->delete();
            foreach ($request->malls as $mall) {
                MallProduct::create([
                    'product_id' => $id,
                    'mall_id' => $mall,
                ]);
            }
        }

        // for other_data
        if ($request->has('input_key') and $request->has('input_value')) {
            $i = 0;
//            $other_data = '';
            ProductOtherData::where('product_id', $id)->delete();
            foreach ($request->input_key as $key) {
                ProductOtherData::create([
                    'data_key' => $key,
                    'data_value' => $request->input_value[$i],
                    'product_id' => $id,
                ]);
//                $other_data .= $key . ',' . $request->input_value[$i] . '|';
                $i++;
            }
//            $data['other_data'] = rtrim($other_data, '|');
        }

        // for related product
        if ($request->has('related')) {
            ProductRelated::where('product_id', $id)->delete();
            foreach ($request->related as $related) {
                ProductRelated::create([
                    'product_id' => $id,
                    'related_product' => $related,
                ]);
            }
        }

        Product::where('id', $id)->update($data);
        return response(['status' => true, 'message' => trans('admin_validation.update')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->deleteProduct($id);
        toastr()->error(trans('admin_validation.delete'));
        return redirect(adminUrl('products'));
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        Storage::delete($product->photo);
        upload_file()->deleteFiles($id);
        $product->delete();
    }

    public function delete_all(Request $request)
    {
        if (is_array($request->box)) {
            foreach ($request->box as $id) {
                $this->deleteProduct($id);
            }
        } else {
            $this->deleteProduct($request->box);
        }
        toastr()->error(trans('admin_validation.delete'));
        return redirect()->back();
    }

    /********************** this for photos upload & delete **********************/
    public function upload_main_photo($id)
    {
        $product = Product::find($id);
        if (request()->hasFile('file')) {
            $product->update([
                'photo' => upload_file()->upload([
                    'file' => 'file',
                    'path' => 'products/' . $id,
                    'upload_type' => 'single',
                    'delete_file' => Product::find($id)->photo,
                ])
            ]);
            return response(['status' => true, 'photo' => $product->photo], 200);
        }
    }

    public function delete_main_photo($id)
    {
        $product = Product::findOrFail($id);
        Storage::delete($product->photo);
        $product->update(['photo' => '']);
    }

    public function upload_file($id)
    {
        if (request()->hasFile('file')) {
            $file_id = upload_file()->upload([
                'file' => 'file',
                'path' => 'products/' . $id,
                'upload_type' => 'files',
                'file_type' => 'product',
                'relation_id' => $id,
            ]);
            return response(['status' => true, 'id' => $file_id], 200);
        }
    }

    public function delete_file(Request $request)
    {
        if (request()->has('id')) {
            upload_file()->delete($request->id);
        }
    }

    /********************** this for photos upload & delete **********************/

    public function prepare_size_weight()
    {
        if (request()->ajax() and request()->has('dep_id')) {
            $sizes = Size::whereIn('department_id', explode(',', get_parent_department(request('dep_id'))))
                ->pluck('name_' . session('lang'), 'id');

//            $department_list = array_diff(explode(',', get_parent_department(request('dep_id'))), [request('dep_id')]);
//            $size_1 = Size::where('is_public', 'yes')
//                ->whereIn('department_id', $department_list)
//                ->pluck('name_' . session('lang'), 'id');
//            $size_2 = Size::where('department_id', request('dep_id'))
//                ->pluck('name_' . session('lang'), 'id');
//            $sizes = array_merge(json_decode($size_1, true), json_decode($size_2, true));

            $weights = Weight::pluck('name_' . session('lang'), 'id');
            $product = Product::find(request('product_id'));
            return view('admin.products.ajax.size_weight',
                compact('sizes', 'weights', 'product'))
                ->render();
        }
    }

    /********************** this for copy product **********************/
    public function copy_product(Request $request, $id)
    {
        if ($request->ajax()) {
            $copy = Product::find($id)->toArray();
            unset($copy['id']);
            $product = Product::create($copy); // or

//            $product = Product::create([
//                'title' => $copy->title,
//                'description' => $copy->description,
//                'photo' => $copy->photo,
//                'department_id' => $copy->department_id,
//                'trade_mark_id' => $copy->trade_mark_id,
//                'manufacture_id' => $copy->manufacture_id,
//                'color_id' => $copy->color_id,
//                'size' => $copy->size,
//                'size_id' => $copy->size_id,
//                'weight' => $copy->weight,
//                'weight_id' => $copy->weight_id,
//                'currency_id' => $copy->currency_id,
//                'price' => $copy->price,
//                'stock' => $copy->stock,
//                'start_at' => $copy->start_at,
//                'end_at' => $copy->end_at,
//                'offer_price' => $copy->offer_price,
//                'offer_start_at' => $copy->offer_start_at,
//                'offer_end_at' => $copy->offer_end_at,
//                'reason' => $copy->reason,
//                'status' => $copy->status,
//            ]);

            // copy malls
            $malls = MallProduct::where('product_id', $id)->get();
            foreach ($malls as $mall) {
                MallProduct::create([
                    'product_id' => $product->id,
                    'mall_id' => $mall->mall_id,
                ]);
            }

            // copy other data
            $other_data = ProductOtherData::where('product_id', $id)->get();
            foreach ($other_data as $key) {
                ProductOtherData::create([
                    'data_key' => $key->data_key,
                    'data_value' => $key->data_value,
                    'product_id' => $product->id,
                ]);
            }

            // copy main photo
            if (!empty($copy['photo'])) {
                $exe = File::extension($copy['photo']);
                $new_path = 'products/' . $product->id . '/' . Str::random(40) . '.' . $exe;
                Storage::copy($copy['photo'], $new_path);
                $product->photo = $new_path;
                $product->save();
            }

            // copy files
            $files = \App\Models\File::where('file_type', 'product')->where('relation_id', $id)->get();
            if (count($files) > 0) {
                foreach ($files as $file) {
                    $hashName = Str::random(40);
                    $exe = File::extension($file->full_path);
                    $new_path = 'products/' . $product->id . '/' . $hashName . '.' . $exe;
                    Storage::copy($file->full_path, $new_path);
                    $add = \App\Models\File::create([
                        'name' => $file->name,
                        'size' => $file->size,
                        'file' => $hashName,
                        'path' => 'products/' . $product->id,
                        'full_path' => $new_path,
                        'mime_type' => $file->mime_type,
                        'file_type' => 'product',
                        'relation_id' => $product->id,
                    ]);
                }
            }
            return response([
                'status' => true,
                'message' => trans('admin_validation.success'),
                'id' => $product->id
            ], 200);
        }
    }

    /********************** this for copy product **********************/

    public function product_search(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->search) && $request->has('search')) {
                $related_products = ProductRelated::where('product_id', $request->product_id)->get(['related_product']);
                $products = Product::where('title', 'LIKE', '%' . $request->search . '%')
                    ->where('id', '!=', $request->product_id)
                    ->whereNotIn('id', $related_products)
                    ->limit(10)
                    ->orderBy('id', 'DESC')
                    ->get(); // or
//                $products = Product::where('title', 'LIKE', '%' . $request->search . '%')->limit(10)->get();
                return response([
                    'status' => true,
                    'results' => count($products) > 0 ? $products : '',
                    'total' => count($products),
                ], 200);
            }
        }
    }

}
