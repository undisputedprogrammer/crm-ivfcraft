<?php

namespace Ynotz\AccessControl\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth()->user();
        return $user->hasPermissionTo('permission.create_any');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:permissions,name'
        ];
    }
}
