@extends('layouts.default')

@section('title','取引画面')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/transaction.css')  }}">
@endsection

@section('content')

@include('components.header')
<div class="transaction-container">
    <div class="transaction-sidebar">
        <h3 class="sidebar__title">その他の取引</h3>
        <ul class="sidebar__list">
            @foreach($otherTransactions as $other)
            <li class="sidebar__item">
                <a href="/transaction/{{ $other['transaction']->id }}" class="sidebar__link">
                    @if($other['unread_count'] > 0)
                    <span class="sidebar__badge">{{ $other['unread_count'] }}</span>
                    @endif
                    <span class="sidebar__item-name">{{ $other['item']->name }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    
    <div class="transaction-main">
        <div class="transaction-header">
            <div class="transaction-header__user">
                @if(isset($partner->profile->img_url))
                <img class="transaction-header__avatar" src="{{ \Storage::url($partner->profile->img_url) }}" alt="">
                @else
                <img class="transaction-header__avatar" src="{{ asset('img/icon.png') }}" alt="">
                @endif
                <h2 class="transaction-header__title">「{{ $partner->name }}」さんとの取引画面</h2>
            </div>
            @if(!$transaction->isCompleted() && $transaction->buyer_id == Auth::id())
            <form action="/transaction/{{ $transaction->id }}/complete" method="post" class="transaction-header__complete">
                @csrf
                <button type="submit" class="btn btn--complete">取引を完了する</button>
            </form>
            @endif
        </div>
        
        <div class="transaction-product">
            <div class="transaction-product__img">
                <img src="{{ \Storage::url($transaction->item->img_url) }}" alt="商品画像">
            </div>
            <div class="transaction-product__info">
                <h3 class="transaction-product__name">{{ $transaction->item->name }}</h3>
                <p class="transaction-product__price">¥ {{ number_format($transaction->item->price) }}</p>
            </div>
        </div>
        
        <div class="transaction-messages">
            @foreach($messages as $message)
            <div class="message {{ $message->user_id == Auth::id() ? 'message--own' : '' }}">
                <div class="message__user">
                    @if(isset($message->user->profile->img_url))
                    <img class="message__avatar" src="{{ \Storage::url($message->user->profile->img_url) }}" alt="">
                    @else
                    <img class="message__avatar" src="{{ asset('img/icon.png') }}" alt="">
                    @endif
                    <p class="message__name">{{ $message->user->name }}</p>
                </div>
                <div class="message__content">
                    @if($message->img_url)
                    <img class="message__image" src="{{ \Storage::url($message->img_url) }}" alt="メッセージ画像">
                    @endif
                    <p class="message__text">{{ $message->message }}</p>
                    @if($message->user_id == Auth::id())
                    <div class="message__actions">
                        <a href="/transaction/{{ $transaction->id }}/message/{{ $message->id }}/edit" class="message__edit">編集</a>
                        <form action="/transaction/{{ $transaction->id }}/message/{{ $message->id }}" method="post" class="message__delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="message__delete">削除</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        @if(!$transaction->isCompleted())
        <form action="/transaction/{{ $transaction->id }}/message" method="post" class="transaction-form" enctype="multipart/form-data">
            @csrf
            <div class="form__error">
                @error('message')
                    <p>{{ $message }}</p>
                @enderror
                @error('img_url')
                    <p>{{ $message }}</p>
                @enderror
            </div>
            <div class="transaction-form__input-group">
                <input type="text" name="message" class="transaction-form__input" placeholder="取引メッセージを記入してください" value="{{ old('message', session('transaction_message_' . $transaction->id, '')) }}">
                <label class="btn btn--image">
                    画像を追加
                    <input type="file" name="img_url" class="transaction-form__file" accept="image/jpeg,image/png">
                </label>
                <button type="submit" class="transaction-form__send">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

@if(isset($showRatingModal) && $showRatingModal)
<div class="modal" id="ratingModal">
    <div class="modal__content modal__content--rating">
        <h3 class="modal__title">取引が完了しました。</h3>
        <p class="modal__text">今回の取引相手はどうでしたか？</p>
        <form action="/transaction/{{ $transaction->id }}/rating" method="post" class="rating-form">
            @csrf
            <div class="rating-stars">
                @for($i = 1; $i <= 5; $i++)
                <label class="rating-star">
                    <input type="radio" name="rating" value="{{ $i }}" {{ old('rating', 3) == $i ? 'checked' : '' }} required>
                    <span class="star-icon {{ old('rating', 3) >= $i ? 'star-filled' : '' }}">★</span>
                </label>
                @endfor
            </div>
            <button type="submit" class="btn btn--rating-submit">送信する</button>
        </form>
    </div>
</div>
@endif

@endsection

