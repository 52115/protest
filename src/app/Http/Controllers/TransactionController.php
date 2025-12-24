<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Models\Item;
use App\Models\User;
use App\Http\Requests\TransactionMessageRequest;
use App\Mail\TransactionCompletedMail;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function show($transaction_id)
    {
        $transaction = Transaction::with(['item', 'buyer', 'seller', 'messages.user'])->findOrFail($transaction_id);
        $user = User::find(Auth::id());
        
        // 取引に関わっているユーザーかチェック
        if ($transaction->buyer_id != Auth::id() && $transaction->seller_id != Auth::id()) {
            abort(403);
        }

        // 相手のユーザー情報を取得
        $partner = $transaction->buyer_id == Auth::id() ? $transaction->seller : $transaction->buyer;
        
        // 他の取引一覧を取得（新規メッセージ順）
        $otherTransactions = $this->getUserTransactions(Auth::id())
            ->filter(function ($t) use ($transaction_id) {
                return $t->id != $transaction_id;
            })
            ->map(function ($t) use ($user) {
                $partner = $t->buyer_id == $user->id ? $t->seller : $t->buyer;
                $lastReadTime = session('last_read_transaction_' . $t->id, '1970-01-01 00:00:00');
                $unreadCount = $t->messages
                    ->where('user_id', '!=', $user->id)
                    ->where('created_at', '>', $lastReadTime)
                    ->count();
                return [
                    'transaction' => $t,
                    'partner' => $partner,
                    'item' => $t->item,
                    'unread_count' => $unreadCount,
                ];
            })
            ->values();

        // メッセージを時系列順に取得
        $messages = $transaction->messages()->with('user')->orderBy('created_at', 'asc')->get();

        // 現在の取引の既読時間を更新
        session()->put('last_read_transaction_' . $transaction_id, now()->toDateTimeString());

        // 評価モーダル表示フラグをチェック
        $showRatingModal = false;
        if ($transaction->isCompleted()) {
            // 既に評価済みかチェック
            $alreadyRated = \App\Models\Rating::where('rater_id', Auth::id())
                ->where('item_id', $transaction->item_id)
                ->exists();
            
            if (!$alreadyRated) {
                // 出品者の場合：購入者が取引完了した後に初めてチャット画面を開いた時
                if ($transaction->seller_id == Auth::id()) {
                    // 出品者がまだ評価していない場合、評価モーダルを表示
                    // 購入者が取引完了した後（completed_atが設定されている）に出品者がチャット画面を開いた時
                    if (!session()->has('seller_viewed_completed_transaction_' . $transaction_id)) {
                        // 出品者が取引完了後のチャット画面を初めて開いた時にフラグを設定
                        session()->put('show_rating_modal_seller_' . $transaction_id, true);
                        session()->put('seller_viewed_completed_transaction_' . $transaction_id, true);
                        $showRatingModal = true;
                    } else {
                        // 既に開いたことがある場合は、セッションのフラグを確認
                    $showRatingModal = session('show_rating_modal_seller_' . $transaction_id, false);
                    }
                } else {
                    // 購入者の場合：取引完了ボタンを押した後
                    $showRatingModal = session('show_rating_modal_' . $transaction_id, false);
                }
            }
        }

        return view('transaction', compact('transaction', 'user', 'partner', 'messages', 'otherTransactions', 'showRatingModal'));
    }

    public function store($transaction_id, TransactionMessageRequest $request)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        
        // 取引に関わっているユーザーかチェック
        if ($transaction->buyer_id != Auth::id() && $transaction->seller_id != Auth::id()) {
            abort(403);
        }

        // バリデーションエラー時は自動的にリダイレクトされる
        // 入力値をセッションに保存（他の画面遷移時の保持用）
        // prepareForValidation()で保存されるが、バリデーション成功時はここでも保存
        if ($request->has('message') && $request->filled('message')) {
            session()->put('transaction_message_' . $transaction_id, $request->input('message'));
        }

        $img_url = null;
        if ($request->hasFile('img_url')) {
            $img_url = Storage::disk('local')->put('public/img', $request->file('img_url'));
        }

        TransactionMessage::create([
            'transaction_id' => $transaction_id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'img_url' => $img_url,
        ]);

        // メッセージ送信成功後、セッションをクリア
        session()->forget('transaction_message_' . $transaction_id);

        return redirect('/transaction/' . $transaction_id)->with('flashSuccess', 'メッセージを送信しました！');
    }

    public function saveDraft($transaction_id, Request $request)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        
        // 取引に関わっているユーザーかチェック
        if ($transaction->buyer_id != Auth::id() && $transaction->seller_id != Auth::id()) {
            abort(403);
        }

        // 入力値をセッションに保存（他の画面遷移時の保持用）
        if ($request->has('message')) {
            session()->put('transaction_message_' . $transaction_id, $request->input('message'));
        }

        return response()->json(['success' => true]);
    }

    public function edit($transaction_id, $message_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        $message = TransactionMessage::findOrFail($message_id);
        
        // 自分のメッセージかチェック
        if ($message->user_id != Auth::id()) {
            abort(403);
        }

        // 取引に関わっているユーザーかチェック
        if ($transaction->buyer_id != Auth::id() && $transaction->seller_id != Auth::id()) {
            abort(403);
        }

        return view('transaction_message_edit', compact('transaction', 'message'));
    }

    public function update($transaction_id, $message_id, TransactionMessageRequest $request)
    {
        $message = TransactionMessage::findOrFail($message_id);
        
        // 自分のメッセージかチェック
        if ($message->user_id != Auth::id()) {
            abort(403);
        }

        $img_url = $message->img_url;
        if ($request->hasFile('img_url')) {
            // 古い画像を削除
            if ($img_url) {
                Storage::disk('local')->delete($img_url);
            }
            $img_url = Storage::disk('local')->put('public/img', $request->file('img_url'));
        }

        $message->update([
            'message' => $request->message,
            'img_url' => $img_url,
        ]);

        return redirect('/transaction/' . $transaction_id)->with('flashSuccess', 'メッセージを編集しました！');
    }

    public function destroy($transaction_id, $message_id)
    {
        $message = TransactionMessage::findOrFail($message_id);
        
        // 自分のメッセージかチェック
        if ($message->user_id != Auth::id()) {
            abort(403);
        }

        // 画像を削除
        if ($message->img_url) {
            Storage::disk('local')->delete($message->img_url);
        }

        $message->delete();

        return back()->with('flashSuccess', 'メッセージを削除しました！');
    }

    public function complete($transaction_id, Request $request)
    {
        $transaction = Transaction::with(['item', 'seller'])->findOrFail($transaction_id);
        
        // 購入者のみが取引完了できる
        if ($transaction->buyer_id != Auth::id()) {
            abort(403);
        }

        $transaction->update([
            'completed_at' => now(),
        ]);

        // 出品者にメール通知を送信
        Mail::to($transaction->seller->email)->send(new TransactionCompletedMail($transaction));

        // セッションに評価モーダルを表示するフラグを設定
        session()->put('show_rating_modal_' . $transaction_id, true); // 購入者用
        session()->put('show_rating_modal_seller_' . $transaction_id, true); // 出品者用

        return back()->with('showRatingModal', true);
    }

    public function completeRating($transaction_id, Request $request)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        $user = User::find(Auth::id());
        
        // 取引に関わっているユーザーかチェック
        if ($transaction->buyer_id != Auth::id() && $transaction->seller_id != Auth::id()) {
            abort(403);
        }

        // 評価を保存
        $ratedUserId = $transaction->buyer_id == Auth::id() ? $transaction->seller_id : $transaction->buyer_id;
        
        \App\Models\Rating::create([
            'rater_id' => Auth::id(),
            'rated_user_id' => $ratedUserId,
            'item_id' => $transaction->item_id,
            'rating' => $request->rating,
        ]);

        // 評価モーダルのフラグを削除
        session()->forget('show_rating_modal_' . $transaction_id);
        session()->forget('show_rating_modal_seller_' . $transaction_id);

        return redirect('/')->with('flashSuccess', '評価を送信しました！');
    }

    private function getUserTransactions($userId)
    {
        return Transaction::where(function($query) use ($userId) {
                $query->where('buyer_id', $userId)
                      ->orWhere('seller_id', $userId);
            })
            ->with(['item', 'buyer', 'seller', 'messages'])
            ->whereNull('completed_at')
            ->get()
            ->sortByDesc(function ($transaction) {
                $latestMessage = $transaction->messages->sortByDesc('created_at')->first();
                return $latestMessage ? $latestMessage->created_at : $transaction->created_at;
            });
    }
}
