<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SizeAndQuantity;
use App\Sneaker;
use App\Http\Requests;

class ClientController extends Controller
{
    public function cart() {
    	return view('cart');
    }

    public function checkout(Request $req) {
        $index = strrpos($req, '[{');
        $json = json_decode(substr($req, $index), true);
    	$sizes = array();
    	for ($i=0;$i<count($json);$i++) {
    		$size = SizeAndQuantity::where([['sneaker_id', '=', $req[$i]['sneaker_id']], ['size', '=', $req[$i]['size']]])->first();
            //$size = SizeAndQuantity::where([['sneaker_id', '=', 69], ['size', '=', 40]])->first();
    		$size->quantity -= $req[$i]['quantity'];
    		$size->save();
    		array_push($sizes, $size);
    	}
    	return $sizes;

    	// $sneaker = new Sneaker();
    	// $index = strrpos($req, '[{');
     //    $json = json_decode(substr($req, $index), true);
    	// $sneaker->name = $json[0]['name'];
    	// $sneaker->brand = "default";
    	// $sneaker->price = count($json);
    	// $sneaker->cover_image = "default";
    	// $sneaker->save();
    }
}
