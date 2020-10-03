<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Modules\Blog\Post;
use App\Modules\Blog\PostTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{

    private $activeLanguages;
    public  function __construct()
    {
        $this->activeLanguages=array_filter(array_map(fn($lang)=>$lang['status']?$lang:null ,config('languages.lang')));
    }
    public  function create(){
        return view('Backend.blog.create');
    }
    public  function blog(){

        toast(__('messages.successMessage'),'success')->position(position());

        $posts=Post::with(['posts'=>function($query){
            $query->where('locale',app()->getLocale());
        }])->paginate(PAGINATION);
        return view('Backend.blog.blog',compact('posts'));
    }
    public  function blogSave(PostRequest $request){
        $post=Post::create(['photo'=>$request->file('photo')->store('posts')]);
        if (!$post){
            toast(__('messages.errorMessage'),'error')->position(position());
             return back();
        }else{
            $this->createTranslationsPosts($post,$request);
            toast(__('messages.successMessage'),'success')->position(position());

            return back();
        }
    }
    private function validatePostRequest(Request  $request){
        $rules=['photo'=>'required'];
        foreach ($this->activeLanguages as $key => $language) {
            $rules[$key.'.title']   ='required|string|max:191|'.Rule::unique('posts_translations', 'title')->where('locale','en');
            $rules[$key.'.content'] = 'required|string';
        }
            $request->validate($rules);
    }
    private function createTranslationsPosts($mainPost,Request $request){
        foreach ($this->activeLanguages as $key => $language) {
            if (!$language['status']) { continue; }
            PostTranslation::create([
                'title'=>$request->get($key)['title'],
                'content'=>$request->get($key)['content'],
                'locale'=>$key,
                'post_id'=>$mainPost->id
            ]);
        }
    }
    private function updateTranslationsPosts($mainPost,Request $request){
        foreach ($this->activeLanguages as $key => $language) {
            if (!$language['status']) { continue; }
            $post=PostTranslation::where('post_id',$mainPost->id)->where('locale',$key)->first();
            if ($post){
                $post->update([
                    'title'=>$request->get($key)['title'],
                    'content'=>$request->get($key)['content']
                ]);
            }else{
                PostTranslation::create([
                    'title'=>$request->get($key)['title'],
                    'content'=>$request->get($key)['content'],
                    'locale'=>$key,
                    'post_id'=>$mainPost->id
                ]);
            }

        }
    }
    public  function destroy($id){
        $post=Post::findOrFail($id);
        Storage::disk('local')->delete($post->photo);
        toast(__('messages.successMessage'),'success')->position(position());
        $post->delete();
        return redirect()->back();
    }
    public  function edit($id){
        $post=Post::with('posts')->findOrFail($id);
        $translatedPostedLocales=array_keys($post->posts->groupBy('locale')->toArray());
        return view('Backend.blog.edit',['post'=>$post,'translatedPostedLocales'=>$translatedPostedLocales]);
    }
    public  function update(PostRequest $request, $id){
        $post=Post::with('posts')->findOrFail($id);
        if ($request->hasFile('photo')){
            Storage::disk('local')->delete($post->photo);
            $path=$request->file('photo')->store('posts');
        }else{
            $path=$post->photo;
        }

        if ($post->update(['photo'=>$path])){
            $this->updateTranslationsPosts($post,$request);
            toast(__('messages.successMessage'),'success')->position(position());
            return back();
        }else{
            toast(__('messages.errorMessage'),'error')->position(position());
            return back();
        }
    }
}

/*
 *

//old function

//public function blogSave(Request $request){
//    // validate request
//    $request->validate(['photo'=>'required']);
//    foreach (config('languages.lang') as $key => $language) {
//        if (!$language['status']) { continue; }
//        $request->validate([
//            $key . '.title' => ['required', 'string', 'max:191', Rule::unique('posts_translations', 'title')->where('locale', $key)],
//            $key . 'content' => 'required|string',
//        ]);
//    }
//    // store main and non translated data
//    $post=Post::create(['photo'=>$request->get('photo')]);
//    // if storing the main (common) data failed
//    if (!$post){
//        toast('Failed');
//    }else{
//        foreach (config('languages.lang') as $key => $language) {
//            if (!$language['status']) { continue; }
//            PostTranslation::create([
//                'title'=>$request->get($key)['title'],
//                'content'=>$request->get($key)['content'],
//                'locale'=>$key,
//                'post_id'=>$post->id
//            ]);
//        }
//        toast('Success');
//    }
//    return back();
}

 * */
