<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\Transaction;
use App\Models\Rating;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile(){

        $profile = Profile::where('user_id', Auth::id())->first();

        return view('profile',compact('profile'));
    }

    public function updateProfile(ProfileRequest $request){

        $img = $request->file('img_url');
        if (isset($img)){
            $img_url = Storage::disk('local')->put('public/img', $img);
        }else{
            $img_url = '';
        }
        
        $profile = Profile::where('user_id', Auth::id())->first();
        if ($profile){
            $profile->update([
                'user_id' => Auth::id(),
                'img_url' => $img_url,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        }else{
            Profile::create([
                'user_id' => Auth::id(),
                'img_url' => $img_url,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building
            ]);    
        }

        User::find(Auth::id())->update([
            'name' => $request->name
        ]);
        
        return redirect('/');
    }

    public function mypage(Request $request){
        $user = User::find(Auth::id());
        
        // 評価平均を取得
        $averageRating = $user->getAverageRating();
        
        if ($request->page == 'buy'){
            $items = SoldItem::where('user_id', $user->id)->get()->map(function ($sold_item) {
                return $sold_item->item;
            });         
        } elseif ($request->page == 'transaction') {
            // 取引中の商品を取得（新規メッセージ順にソート）
            // 完了していない取引、または完了したが評価がまだ完了していない取引を含める
            $transactions = Transaction::where(function($query) use ($user) {
                    $query->where('buyer_id', $user->id)
                          ->orWhere('seller_id', $user->id);
                })
                ->with(['item', 'buyer', 'seller', 'messages'])
                ->get()
                ->filter(function ($transaction) use ($user) {
                    // 完了していない取引は常に含める
                    if (!$transaction->isCompleted()) {
                        return true;
                    }
                    
                    // 完了した取引でも、評価がまだ完了していない場合は含める
                    $alreadyRated = Rating::where('rater_id', $user->id)
                        ->where('item_id', $transaction->item_id)
                        ->exists();
                    
                    return !$alreadyRated;
                })
                ->map(function ($transaction) use ($user) {
                    $partner = $transaction->buyer_id == $user->id ? $transaction->seller : $transaction->buyer;
                    // 未読メッセージ数をカウント（自分以外のユーザーからの最新メッセージ）
                    $lastReadTime = session('last_read_transaction_' . $transaction->id, '1970-01-01 00:00:00');
                    $unreadCount = $transaction->messages
                        ->where('user_id', '!=', $user->id)
                        ->filter(function($msg) use ($lastReadTime) {
                            return strtotime($msg->created_at) > strtotime($lastReadTime);
                        })
                        ->count();
                    
                    return [
                        'transaction' => $transaction,
                        'item' => $transaction->item,
                        'partner' => $partner,
                        'unread_count' => $unreadCount,
                    ];
                })
                ->sortByDesc(function ($item) {
                    // 最新メッセージの時刻でソート
                    $latestMessage = $item['transaction']->messages->sortByDesc('created_at')->first();
                    return $latestMessage ? $latestMessage->created_at : $item['transaction']->created_at;
                })
                ->values();
            
            return view('mypage', compact('user', 'transactions', 'averageRating'));
        } else {
            $items = Item::where('user_id', $user->id)->get();
        }
        return view('mypage', compact('user', 'items', 'averageRating'));
    }
}
