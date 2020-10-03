<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{

    private $activeLanguages;
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
        $this->activeLanguages=array_filter(array_map(fn($lang)=>$lang['status']?$lang:null ,config('languages.lang')));

        if ($this->route('id')){
            $rules=['photo'=>'image|mimes:png,jpg,jpeg'];
        }else{
            $rules=['photo'=>'required|image|mimes:png,jpg,jpeg'];
        }

        foreach ($this->activeLanguages as $key => $language) {
            if ($this->route('id')){
                $rules[$key.'.title']   ='required|string|max:191|'.Rule::unique('posts_translations', 'title')->where('locale',$key)->ignore($this->route('id'),'post_id');
            }else{
                $rules[$key.'.title']   ='required|string|max:191|'.Rule::unique('posts_translations', 'title')->where('locale',$key);
            }
            $rules[$key.'.content'] = 'required|string';
        }

        return  $rules;
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {

        $messages=[];
        foreach ($this->activeLanguages as $key => $language) {
            $messages[$key.'.title.required']     =__(TRANS."title-$key-is-required");
            $messages[$key.'.title.string']       =__(TRANS."title-$key-must-be-string");
            $messages[$key.'.title.max']          =__(TRANS."title-$key-max");
            $messages[$key.'.title.unique']       =__(TRANS."title-$key-must-be-unique");
            $messages[$key.'.content.required']   =__(TRANS."content-$key-is-required");
            $messages[$key.'.content.string']     =__(TRANS."content-$key-must-be-string");
        }
    return  $messages;
    }
}
