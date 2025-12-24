<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // CO01からCO05: 一般ユーザ1（user_id = 1）が出品
        $params = [
            [
                'name' => '腕時計', // CO01
                'price' => 15000,
                'brand'=> 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'public/img/mens_clock.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$UNUSED,
            ],
            [
                'name' => 'HDD', // CO02
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'public/img/hard_disk.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$HARMLESS,
            ],
            [
                'name' => '玉ねぎ3束', // CO03
                'price' => 300,
                'brand' => '',
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'public/img/onion.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$HARMED,
            ],
            [
                'name' => '革靴', // CO04
                'price' => 4000,
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'public/img/leather_shoes.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$BAD_CONDITION,
            ],
            [
                'name' => 'ノートPC', // CO05
                'price' => 45000,
                'brand' => '',
                'description' => '高性能なノートパソコン',
                'img_url' => 'public/img/laptop_PC.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$UNUSED,
            ],
            // CO06からCO10: 一般ユーザ2（user_id = 2）が出品
            [
                'name' => 'マイク', // CO06
                'price' => 8000,
                'brand' => '',
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'public/img/mic.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$HARMLESS,
            ],
            [
                'name' => 'ショルダーバッグ', // CO07
                'price' => 3500,
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'public/img/shoulder_bag.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$HARMED,
            ],
            [
                'name' => 'タンブラー', // CO08
                'price' => 500,
                'brand' => '',
                'description' => '使いやすいタンブラー',
                'img_url' => 'public/img/tumbler.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$BAD_CONDITION,
            ],
            [
                'name' => 'コーヒーミル', // CO09
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'img_url' => 'public/img/coffer_mill.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$UNUSED,
            ],
            [
                'name' => 'メイクセット', // CO10
                'price' => 2500,
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'img_url' => 'public/img/make_set.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$HARMLESS,
            ],
        ];

        $range = count($params);
        for ($i = 0; $i < $range; $i++){
            Item::create($params[$i]);
        }

        // 既存のLikeデータはそのまま維持
        Like::create([
            'user_id' => 1,
            'item_id' => 1,
        ]);
        Like::create([
            'user_id' => 2,
            'item_id' => 7,
        ]);
    }
}
