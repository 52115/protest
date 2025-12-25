<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CategoryItem;

class ItemController extends Controller
{
    public function index(Request $request){
        $tab = $request->query('tab', 'recommend');
        $search = $request->query('search');
        $query = Item::query();
        $query->where('user_id', '<>', Auth::id());

        if ($tab === 'mylist'){
            $query->whereIn('id', function ($query) {
                $query->select('item_id')
                    ->from('likes')
                    ->where('user_id', auth()->id());
            });
        }

        if($search){
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->get();

        return view('index',compact('items','tab', 'search'));
    }
    
    public function detail(Item $item){
        return view('detail', compact('item'));
    }
    
    public function search(Request $request){
        $searchWord = $request->search_item;
        $query = Item::query();
        $query = Item::scopeItem($query, $searchWord);
        
        $items = $query->get();
        return view('index', compact('items'));
    }
    
    public function sellView(){
        $categories = Category::all();
        $conditions = Condition::all();
        return view('sell',compact('categories', 'conditions'));
    }
    
    public function sellCreate(ItemRequest $request){
        
        $img = $request->file('img_url');
        
        try {
            $img_url = Storage::disk('local')->put('public/img', $img);
        } catch (\Throwable $th) {
            throw $th;
        }
        
        $item = Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'brand' => $request->brand,
            'description' => $request->description,
            'img_url' => $img_url,
            'condition_id' => $request->condition_id,
            'user_id' => Auth::id(),
        ]);
        
        foreach ($request->categories as $category_id){
            CategoryItem::create([
                'item_id' => $item->id,
                'category_id' => $category_id
            ]);
        }
        
        return redirect()->route('item.detail',['item' => $item->id]);
    }
}
