<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sneaker;
use App\SizeAndQuantity;
use File;
use App\Http\Requests;
use App\Http\Resources\Sneaker as SneakerResource;

class ManageController extends Controller
{
    public function getAll() {
    	$sneakers = Sneaker::orderBy('created_at', 'desc')->paginate(5);
    	return SneakerResource::collection($sneakers);
    }

    public function getOne($id) {
    	$sneaker = Sneaker::findOrFail($id);
    	return new SneakerResource($sneaker);
    }

    public function create(Request $req) {
    	if ($req->hasFile('file')) {
            $filenameWithExt = $req->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $req->file('file')->getClientOriginalExtension();
            $filenameToStore = $filename.'_'.time().'_'.$extension;
            $path = $req->file('file')->move('cover_images', $filenameToStore);
            //$filenameToStore = "OK";
        }
        else {
            $filenameToStore = 'noimage.jpg';
        }

    	$sneaker = new Sneaker();
        $sneaker_json = json_decode($req->sneaker);
        $sneaker->name = $sneaker_json->{'name'};
		$sneaker->brand = $sneaker_json->{'brand'};
    	$sneaker->price = $sneaker_json->{'price'};
    	$sneaker->cover_image = $filenameToStore;
        if ($sneaker->save()) return new SneakerResource($sneaker);
    }

    public function edit(Request $req, $id) {
    	$sneaker = Sneaker::findOrFail($id);
    	$sneaker->name = $req->name;
    	$sneaker->brand = $req->brand;
    	$sneaker->price = $req->price;
    	$sneaker->cover_image = $req->cover_image;
    	if ($sneaker->save()) return new SneakerResource($sneaker);
    }

    public function delete($id) {
    	$sneaker = Sneaker::findOrFail($id);

        if ($sneaker->cover_image != 'noimage.jpg') {
            File::Delete('cover_images/'.$sneaker->cover_image);
        }

        $sizes = SizeAndQuantity::where('sneaker_id', '=', $id)->get();
        foreach ($sizes as $size) {
            $size->delete();
        }

    	if ($sneaker->delete()) return new SneakerResource($sneaker);
    }

    public function addSize(Request $req) {
        $s = SizeAndQuantity::where([['size', '=', $req->size], ['sneaker_id', '=', $req->sneaker_id]])->get();
        if (count($s)>0) {
            $s[0]->quantity += $req->quantity;
            if ($s[0]->save()) return $s[0];
        }
        else {
            $size = new SizeAndQuantity();
            $size->sneaker_id = $req->sneaker_id;
            $size->size = $req->size;
            $size->quantity = $req->quantity;
        }
        if ($size->save()) return $size;
    }

    public function getAllSizes() {
        $sizes = SizeAndQuantity::all();
        return $sizes;
    }
}
