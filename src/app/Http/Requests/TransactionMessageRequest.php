<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // 入力値をセッションに保存（バリデーションエラー時の保持用）
        // バリデーションエラー時は自動的にリダイレクトされるが、セッションには保存される
        if ($this->has('message')) {
            $transaction_id = $this->route('transaction_id');
            if ($transaction_id && $this->filled('message')) {
                session()->put('transaction_message_' . $transaction_id, $this->input('message'));
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => 'required|max:400',
            'img_url' => 'nullable|image|mimes:jpeg,png,jpg',
        ];
    }

    public function messages()
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max' => '本文は400文字以内で入力してください',
            'img_url.image' => '「.png」または「.jpeg」形式でアップロードしてください',
            'img_url.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
