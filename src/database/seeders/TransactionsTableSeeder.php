<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\SoldItem;
use App\Models\TransactionMessage;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ===== User 1がUser 2が出品した商品を購入（取引中） =====
        
        // CO06: マイク（item_id = 6）を購入 → 取引中
        $transaction1 = Transaction::create([
            'item_id' => 6, // マイク
            'buyer_id' => 1, // User 1が購入
            'seller_id' => 2, // User 2が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction1->id,
            'user_id' => 1,
            'message' => 'こんにちは。商品の状態についてお聞きしたいです。',
            'img_url' => null,
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction1->id,
            'user_id' => 2,
            'message' => 'はい、状態良好です。お気軽にお問い合わせください。',
            'img_url' => null,
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction1->id,
            'user_id' => 1,
            'message' => 'ありがとうございます。発送方法についても教えていただけますか？',
            'img_url' => null,
        ]);

        // CO07: ショルダーバッグ（item_id = 7）を購入 → 取引中
        $transaction2 = Transaction::create([
            'item_id' => 7, // ショルダーバッグ
            'buyer_id' => 1, // User 1が購入
            'seller_id' => 2, // User 2が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction2->id,
            'user_id' => 1,
            'message' => '商品のサイズについて質問があります。',
            'img_url' => null,
        ]);

        // CO08: タンブラー（item_id = 8）を購入 → 取引中
        $transaction3 = Transaction::create([
            'item_id' => 8, // タンブラー
            'buyer_id' => 1, // User 1が購入
            'seller_id' => 2, // User 2が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction3->id,
            'user_id' => 2,
            'message' => 'ご購入ありがとうございます。発送準備中です。',
            'img_url' => null,
        ]);

        // CO09: コーヒーミル（item_id = 9）を購入 → 取引中
        $transaction4 = Transaction::create([
            'item_id' => 9, // コーヒーミル
            'buyer_id' => 1, // User 1が購入
            'seller_id' => 2, // User 2が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction4->id,
            'user_id' => 1,
            'message' => '使用感はどうですか？',
            'img_url' => null,
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction4->id,
            'user_id' => 2,
            'message' => 'ほぼ未使用で、状態は非常に良好です。',
            'img_url' => null,
        ]);

        // ===== User 2がUser 1が出品した商品を購入（取引中） =====

        // CO01: 腕時計（item_id = 1）を購入 → 取引中
        $transaction5 = Transaction::create([
            'item_id' => 1, // 腕時計
            'buyer_id' => 2, // User 2が購入
            'seller_id' => 1, // User 1が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction5->id,
            'user_id' => 2,
            'message' => '発送お願いします。',
            'img_url' => null,
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction5->id,
            'user_id' => 1,
            'message' => '承知いたしました。明日発送予定です。',
            'img_url' => null,
        ]);

        // CO03: 玉ねぎ3束（item_id = 3）を購入 → 取引中
        $transaction6 = Transaction::create([
            'item_id' => 3, // 玉ねぎ3束
            'buyer_id' => 2, // User 2が購入
            'seller_id' => 1, // User 1が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction6->id,
            'user_id' => 2,
            'message' => '新鮮さについて確認したいです。',
            'img_url' => null,
        ]);

        // CO04: 革靴（item_id = 4）を購入 → 取引中
        $transaction7 = Transaction::create([
            'item_id' => 4, // 革靴
            'buyer_id' => 2, // User 2が購入
            'seller_id' => 1, // User 1が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction7->id,
            'user_id' => 2,
            'message' => 'サイズについて教えてください。',
            'img_url' => null,
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction7->id,
            'user_id' => 1,
            'message' => '26cmサイズです。',
            'img_url' => null,
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction7->id,
            'user_id' => 2,
            'message' => 'ありがとうございます。ちょうど良いサイズです。',
            'img_url' => null,
        ]);

        // ===== User 3がUser 1が出品した商品を購入（取引中） =====

        // CO05: ノートPC（item_id = 5）を購入 → 取引中
        $transaction8 = Transaction::create([
            'item_id' => 5, // ノートPC
            'buyer_id' => 3, // User 3が購入
            'seller_id' => 1, // User 1が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction8->id,
            'user_id' => 3,
            'message' => '動作確認はされていますか？',
            'img_url' => null,
        ]);

        // ===== User 3がUser 2が出品した商品を購入（取引中） =====

        // CO10: メイクセット（item_id = 10）を購入 → 取引中
        $transaction9 = Transaction::create([
            'item_id' => 10, // メイクセット
            'buyer_id' => 3, // User 3が購入
            'seller_id' => 2, // User 2が出品
            'completed_at' => null, // 取引中
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction9->id,
            'user_id' => 3,
            'message' => 'こんにちは。商品について質問があります。',
            'img_url' => null,
        ]);
        TransactionMessage::create([
            'transaction_id' => $transaction9->id,
            'user_id' => 2,
            'message' => '何でもお聞きください。',
            'img_url' => null,
        ]);

        // ===== 取引完了データ =====

        // CO02: HDD（item_id = 2）を購入完了
        $transaction10 = Transaction::create([
            'item_id' => 2, // HDD
            'buyer_id' => 2, // User 2が購入
            'seller_id' => 1, // User 1が出品
            'completed_at' => now(), // 購入完了
        ]);

        // 購入完了した商品はSoldItemにも記録
        SoldItem::create([
            'user_id' => 2, // User 2が購入
            'item_id' => 2, // HDD
            'sending_postcode' => '1080014',
            'sending_address' => '東京都港区芝5丁目29-20610',
            'sending_building' => 'クロスオフィス三田',
        ]);
    }
}

