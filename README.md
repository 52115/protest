# 環境構築

## 前提条件

- Docker Desktopがインストールされていること
- Dockerが起動していること

## セットアップ手順

1. **プロジェクト直下で、以下のコマンドを実行する**

```bash
make init
```

※Makefileは実行するコマンドを省略することができる便利な設定ファイルです。コマンドの入力を効率的に行えるようになります。

`make init`コマンドは以下の処理を実行します：

1. Dockerコンテナのビルドと起動（`docker-compose up -d --build`）
2. Composerパッケージのインストール（`composer install`）
3. `.env`ファイルの作成（`.env.example`からコピー）
4. ストレージディレクトリの作成と画像ファイルの移動
5. アプリケーションキーの生成（`php artisan key:generate`）
6. ストレージリンクの作成（`php artisan storage:link`）
7. 権限の設定（`chmod -R 777 storage bootstrap/cache`）
8. データベースのマイグレーションとシーディング（`make fresh`）

## データベースマイグレーション

マイグレーションは`make init`実行時に自動的に実行されますが、個別に実行する場合は以下のコマンドを使用します：

```bash
# マイグレーションの実行（既存データを保持）
docker-compose exec php php artisan migrate

# マイグレーションのリフレッシュ（既存データを削除して再実行）
docker-compose exec php php artisan migrate:fresh

# マイグレーションとシーディングの同時実行
docker-compose exec php php artisan migrate:fresh --seed
```

または、Makefileを使用する場合：

```bash
# マイグレーションとシーディングの同時実行
make fresh
```

## その他の便利なコマンド

```bash
# Dockerコンテナの起動
make up

# Dockerコンテナの停止
make down

# Dockerコンテナの再起動
make restart

# キャッシュのクリア
make cache

# Dockerコンテナの停止（削除はしない）
make stop
```

## メール認証
mailtrapというツールを使用しています。<br>
以下のリンクから会員登録をしてください。　<br>
https://mailtrap.io/

メールボックスのIntegrationsから 「laravel 7.x and 8.x」を選択し、　<br>
.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。　<br>
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。　

## Stripeについて
コンビニ支払いとカード支払いのオプションがありますが、決済画面にてコンビニ支払いを選択しますと、レシートを印刷する画面に遷移します。そのため、カード支払いを成功させた場合に意図する画面遷移が行える想定です。<br>

また、StripeのAPIキーは以下のように設定をお願いいたします。
```
STRIPE_PUBLIC_KEY="パブリックキー"
STRIPE_SECRET_KEY="シークレットキー"
```

以下のリンクは公式ドキュメントです。<br>
https://docs.stripe.com/payments/checkout?locale=ja-JP
## テーブル仕様
### usersテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| name | varchar(255) |  |  | ◯ |  |
| email | varchar(255) |  | ◯ | ◯ |  |
| email_verified_at | timestamp |  |  |  |  |
| password | varchar(255) |  |  | ◯ |  |
| remember_token | varchar(100) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### profilesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  |  | ◯ | users(id) |
| img_url | varchar(255) |  |  |  |  |
| postcode | varchar(255) |  |  | ◯ |  |
| address | varchar(255) |  |  | ◯ |  |
| building | varchar(255) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### itemsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  |  | ◯ | users(id) |
| condition_id | bigint |  |  | ◯ | condtions(id) |
| name | varchar(255) |  |  | ◯ |  |
| price | int |  |  | ◯ |  |
| brand | varchar(255) |  |  |  |  |
| description | varchar(255) |  |  | ◯ |  |
| img_url | varchar(255) |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### commentsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  |  | ◯ | users(id) |
| item_id | bigint |  |  | ◯ | items(id) |
| comment | varchar(255) |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### likesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| user_id | bigint |  | ◯(item_idとの組み合わせ) | ◯ | users(id) |
| item_id | bigint |  | ◯(user_idとの組み合わせ) | ◯ | items(id) |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### sold_itemsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| user_id | bigint |  |  | ◯ | users(id) |
| item_id | bigint |  |  | ◯ | items(id) |
| sending_postcode | varchar(255) |  |  | ◯ |  |
| sending_address | varchar(255) |  |  | ◯ |  |
| sending_building | varchar(255) |  |  |  |  |
| created_at | created_at |  |  |  |  |
| updated_at | updated_at |  |  |  |  |

### category_itemsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| item_id | bigint |  | ◯(category_idとの組み合わせ) | ◯ | items(id) |
| category_id | bigint |  | ◯(item_idとの組み合わせ) | ◯ | categories(id) |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### categoriesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| category | varchar(255) |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### conditionsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| condition | varchar(255) |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

## ER図
![alt](ER.png)

## ダミーデータについて

`make init`または`make fresh`コマンドを実行すると、以下のダミーデータが自動的に作成されます。

### ユーザーデータ

以下の3つのユーザーアカウントが作成されます：

1. **一般ユーザ1**
   - メールアドレス: `general1@gmail.com`
   - パスワード: `password`
   - 出品商品: CO01〜CO05（腕時計、HDD、玉ねぎ3束、革靴、ノートPC）

2. **一般ユーザ2**
   - メールアドレス: `general2@gmail.com`
   - パスワード: `password`
   - 出品商品: CO06〜CO10（マイク、ショルダーバッグ、タンブラー、コーヒーミル、メイクセット）

3. **一般ユーザ3**
   - メールアドレス: `general3@gmail.com`
   - パスワード: `password`
   - 出品商品: なし

### 商品データ

合計10件の商品が作成されます：

| 商品ID | 商品名 | 出品者 | 価格 | 状態 |
|--------|--------|--------|------|------|
| CO01 | 腕時計 | 一般ユーザ1 | 15,000円 | 未使用に近い |
| CO02 | HDD | 一般ユーザ1 | 5,000円 | 目立った傷や汚れなし |
| CO03 | 玉ねぎ3束 | 一般ユーザ1 | 300円 | やや傷や汚れあり |
| CO04 | 革靴 | 一般ユーザ1 | 4,000円 | 状態が悪い |
| CO05 | ノートPC | 一般ユーザ1 | 45,000円 | 未使用に近い |
| CO06 | マイク | 一般ユーザ2 | 8,000円 | 目立った傷や汚れなし |
| CO07 | ショルダーバッグ | 一般ユーザ2 | 3,500円 | やや傷や汚れあり |
| CO08 | タンブラー | 一般ユーザ2 | 500円 | 状態が悪い |
| CO09 | コーヒーミル | 一般ユーザ2 | 4,000円 | 未使用に近い |
| CO10 | メイクセット | 一般ユーザ2 | 2,500円 | 目立った傷や汚れなし |

### 取引データ

#### 取引中の商品（9件）

1. **CO06: マイク**（一般ユーザ1が購入、一般ユーザ2が出品）
   - メッセージ数: 3件（購入者と出品者のやり取りあり）

2. **CO07: ショルダーバッグ**（一般ユーザ1が購入、一般ユーザ2が出品）
   - メッセージ数: 1件

3. **CO08: タンブラー**（一般ユーザ1が購入、一般ユーザ2が出品）
   - メッセージ数: 1件

4. **CO09: コーヒーミル**（一般ユーザ1が購入、一般ユーザ2が出品）
   - メッセージ数: 2件

5. **CO01: 腕時計**（一般ユーザ2が購入、一般ユーザ1が出品）
   - メッセージ数: 2件

6. **CO03: 玉ねぎ3束**（一般ユーザ2が購入、一般ユーザ1が出品）
   - メッセージ数: 1件

7. **CO04: 革靴**（一般ユーザ2が購入、一般ユーザ1が出品）
   - メッセージ数: 3件

8. **CO05: ノートPC**（一般ユーザ3が購入、一般ユーザ1が出品）
   - メッセージ数: 1件

9. **CO10: メイクセット**（一般ユーザ3が購入、一般ユーザ2が出品）
   - メッセージ数: 2件

#### 取引完了した商品（1件）

- **CO02: HDD**（一般ユーザ2が購入、一般ユーザ1が出品）
  - 購入完了済み（`SoldItem`テーブルにも記録あり）

### その他のデータ

- **カテゴリー**: 14種類のカテゴリー（ファッション、家電、インテリア、レディース、メンズ、コスメ、本、ゲーム、スポーツ、キッチン、ハンドメイド、アクセサリー、おもちゃ、ベビー・キッズ）
- **商品状態**: 4種類（良好、目立った傷や汚れなし、やや傷や汚れあり、状態が悪い）
- **いいね**: 2件（一般ユーザ1がCO01にいいね、一般ユーザ2がCO07にいいね）
- **プロフィール**: 3ユーザーすべてにプロフィール情報が登録済み

## テストアカウント

上記のダミーデータに含まれるユーザーアカウントを使用してログインできます：

- **一般ユーザ1**
  - メールアドレス: `general1@gmail.com`
  - パスワード: `password`

- **一般ユーザ2**
  - メールアドレス: `general2@gmail.com`
  - パスワード: `password`

- **一般ユーザ3**
  - メールアドレス: `general3@gmail.com`
  - パスワード: `password`

## PHPUnitを利用したテストに関して
以下のコマンド:  
```
//テスト用データベースの作成
docker-compose exec mysql bash
mysql -u root -p
//パスワードはrootと入力
create database test_database;

docker-compose exec php bash
php artisan migrate:fresh --env=testing
./vendor/bin/phpunit
```
※.env.testingにもStripeのAPIキーを設定してください。  