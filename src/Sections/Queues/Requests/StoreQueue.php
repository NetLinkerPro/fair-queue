<?php

namespace NetLinker\FairQueue\Sections\Queues\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQueue extends FormRequest
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
            'horizon_uuid' => 'required|string|max:36',
            'supervisor_uuid' => 'required|string|max:36',
            'queue' => 'required|string|max:255',
        ];
    }
}


