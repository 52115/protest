@extends('layouts.default')

@section('title','メッセージ編集')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/common.css')  }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <div class="center">
        <h1 class="page__title">メッセージを編集</h1>
        <form action="/transaction/{{ $transaction->id }}/message/{{ $message->id }}/update" method="post" enctype="multipart/form-data" class="transaction-edit-form">
            @csrf
            
            <div class="form__error">
                @error('message')
                    {{ $message }}
                @enderror
                @error('img_url')
                    {{ $message }}
                @enderror
            </div>

            <label for="message" class="entry__name">本文</label>
            <textarea name="message" id="message" class="input" rows="5" required>{{ old('message', $message->message) }}</textarea>

            @if($message->img_url)
            <div class="current-image">
                <p class="entry__name">現在の画像</p>
                <img src="{{ \Storage::url($message->img_url) }}" alt="現在の画像" style="max-width: 300px; margin-bottom: 20px;">
            </div>
            @endif

            <label for="img_url" class="entry__name">画像（変更する場合）</label>
            <input type="file" name="img_url" id="img_url" accept="image/jpeg,image/png" class="input">

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn--big">編集を保存</button>
                <a href="/transaction/{{ $transaction->id }}" class="btn btn--big" style="margin-top: 10px; background-color: #666; text-decoration: none; display: block; text-align: center;">キャンセル</a>
            </div>
        </form>
    </div>
</div>
@endsection

