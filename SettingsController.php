<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;
class SettingController extends Controller
{

    private $activeLanguages;

    public function __construct()
    {
        $this->activeLanguages=\LaravelLocalization::getSupportedLocales();
    }

    public function translation(){

        $filesContent=[];
        $files=[];
        $skippedFiles=['.','..','validation.php','passwords.php','pagination.php','auth.php'];
        $languagePath=resource_path('lang'.DIRECTORY_SEPARATOR);
        foreach($this->activeLanguages as $languageLocaleCode => $languageNativeName){
            $allLanguageFilesOfCurrentLanguage=scandir($languagePath.$languageLocaleCode);
            foreach ($allLanguageFilesOfCurrentLanguage as $file){
                if (!in_array($file,$skippedFiles)){
                    array_push($files,$file);
                    $filePath=$languagePath.$languageLocaleCode.DIRECTORY_SEPARATOR.$file;
                    $fileName=explode('.php',$file);
                    $filesContent[$languageLocaleCode][strtolower($fileName[0])]=array_filter(File::getRequire($filePath));
                }
            }

        }

        $data['languages']=$this->activeLanguages;
        $data['translationFiles']=$filesContent;
        $data['files']=array_unique($files);
        return view('Backend.settings.translation',$data);

    }

    public function saveTranslation(Request  $request){


        $contentsStart='<?php return [ '.PHP_EOL;
        $contentEnd=' ];';
        $newContent=[];

        $request->validate(['fileName'=>'required']);
        $fileName=strtolower(explode('.php',$request->fileName)[0]);;
        foreach ($this->activeLanguages as $languageLocaleCode => $languageNativeName) {
            $request->validate([
                "$fileName.$languageLocaleCode"=>'required',
                "$fileName.$languageLocaleCode.*"=>'required'
            ]);
        }

        foreach ($this->activeLanguages as $languageLocaleCode => $languageNativeName) {
            $data=$request->$fileName[$languageLocaleCode];
            $buildArray='';
            foreach ($data as $key=>$value){
                $buildArray.=$this->build($key,$value);
            }
            $newContent[$languageLocaleCode]=$buildArray;
        }




        foreach ($this->activeLanguages as $languageLocaleCode => $languageNativeName) {
            if (!is_null($request->$languageLocaleCode[0])){
                $request->validate([
                    "$languageLocaleCode.1"=>'required'
                ]);
            }
            $newTranslationData=$request->$fileName[$languageLocaleCode];;
            $newKey=$request->$languageLocaleCode[0];
            $newValue=$request->$languageLocaleCode[1];
            if(!key_exists($newKey,$newTranslationData)){
                $newContent[$languageLocaleCode].=$this->build($newKey,$newValue);
            }
        }

        foreach ($newContent as $contentLocale => $content){
            $newData=$contentsStart.$content.$contentEnd;
            $newFilePath=$this->getFilePath($contentLocale,$fileName);
            File::delete($newFilePath);
            File::put($newFilePath,$newData);
        }

        Alert::success(__('messages.successTitle'),__('messages.successMessage'));

        return back();

    }

    public function addNewFile(Request $request){
        $request->validate([
            'file'=>'required|string'
        ]);
        foreach($this->activeLanguages as $languageLocaleCode => $languageNativeName) {
            $fileName=explode('.php',$request->file)[0];
            $path=$this->getFilePath($languageLocaleCode,$fileName);
            if (!file_exists($path)){
                $data="<?php return ['fileName'=>'$fileName'];";
                File::put($path,$data);
                Alert::success(__('messages.successTitle'),__('messages.successMessage'));
            }else{
                Alert::info(__('messages.attention'),__('messages.fileAlreadyExist'));

            }
        }

        return back();

    }
    private function build($key,$value){
        return "'$key' => '$value' ,".PHP_EOL;
    }

    private function getFilePath($locale,$filename){
        return resource_path('lang'.DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.strtolower($filename).'.php');
    }
}
