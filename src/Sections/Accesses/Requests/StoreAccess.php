<?php

namespace NetLinker\FairQueue\Sections\Accesses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccess extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'queue_uuid' => 'required|string|max:36',
            'type' => 'required|string|max:255',
            'object_uuid' => 'required|string|max:36',
        ];
    }
}


