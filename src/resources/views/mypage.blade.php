@extends('layouts.default')

<!-- タイトル -->
@section('title','マイページ')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}" >
<link rel="stylesheet" href="{{ asset('/css/mypage.css')  }}" >
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<div class="container">
    <div class="user">
            <div class="user__info">
                <div class="user__img">
                    @if (isset($user->profile->img_url))
                        <img class="user__icon" src="{{ \Storage::url($user->profile->img_url) }}" alt="">
                    @else
                        <img id="myImage" class="user__icon" src="{{ asset('img/icon.png') }}" alt="">
                    @endif
                </div>
                <div>
                    <p class="user__name">{{$user->name}}</p>
                    @if(isset($averageRating))
                    <div class="user__rating">
                        @for($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= $averageRating ? 'star--filled' : '' }}">★</span>
                        @endfor
                    </div>
                    @endif
                </div>
            </div>
            <div class="mypage__user--btn">
            <a class="btn2" href="/mypage/profile">プロフィールを編集</a>
            </div>
    </div>
    <div class="border">
        <ul class="border__list">
            <li><a href="/mypage?page=sell" class="{{ (!request()->has('page') || request()->get('page') == 'sell') ? 'border__list--active' : '' }}">出品した商品</a></li>
            <li><a href="/mypage?page=buy" class="{{ request()->get('page') == 'buy' ? 'border__list--active' : '' }}">購入した商品</a></li>
            <li><a href="/mypage?page=transaction" class="{{ request()->get('page') == 'transaction' ? 'border__list--active' : '' }}">取引中の商品
                @if(isset($transactions) && $transactions->where('unread_count', '>', 0)->count() > 0)
                <span class="transaction-badge">{{ $transactions->where('unread_count', '>', 0)->count() }}</span>
                @endif
            </a></li>
        </ul>
    </div>
    @if(isset($transactions))
    <div class="items">
        @foreach ($transactions as $transactionData)
        <div class="item">
            <a href="/transaction/{{ $transactionData['transaction']->id }}">
                <div class="item__img--container">
                    @if($transactionData['unread_count'] > 0)
                    <span class="item__notification">{{ $transactionData['unread_count'] }}</span>
                    @endif
                    <img src="{{ \Storage::url($transactionData['item']->img_url) }}" class="item__img" alt="商品画像">
                </div>
                <p class="item__name">{{ $transactionData['item']->name }}</p>
            </a>
        </div>
        @endforeach
    </div>
    @else
    <div class="items">
        @foreach ($items as $item)
        <div class="item">
            <a href="/item/{{$item->id}}">
                @if ($item->sold())
                    <div class="item__img--container sold">
                        <img src="{{ \Storage::url($item->img_url) }}" class="item__img" alt="商品画像">
                    </div>
                @else
                    <div class="item__img--container">
                        <img src="{{ \Storage::url($item->img_url) }}" class="item__img" alt="商品画像">
                    </div>
                @endif
                <p class="item__name">{{$item->name}}</p>
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
