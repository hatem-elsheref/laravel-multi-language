@extends('Backend.shared.master')

    @section('content')
            <form class="needs-validation" novalidate="" method="post" action="{{route('settings.translations.save')}}">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-ml-12">
                        <div class="row">
                            <!-- Server side start -->
                            <div class="col-12">
                                <div class="card mt-5">
                                    <div class="card-body">
                                        @foreach($errors->all() as $error)
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>{{$error}}</strong>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span class="fa fa-times"></span>
                                                </button>
                                            </div>
                                        @endforeach
                                        <h4 class="header-title"> <i class="ti-plus"></i> Add New Translation</h4>
                                        <div class="form-row">
{{--                                            <div class="col-md-12 mb-3">--}}
{{--                                                <label for="validationCustom01">Filter</label>--}}
{{--                                                <input type="text" class="form-control" id="validationCustom01" placeholder="First name" value="Mark" required="">--}}
{{--                                                <div class="valid-feedback">--}}
{{--                                                    Looks good!--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                            @foreach($languages as $languageLocaleCode => $languageNativeName)
                                                <div class="col-md-12 mb-3">
                                                    <h5 class="header-title">{{$languageNativeName['name']}}</h5>
                                                    <hr style="width: 15%">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom02">Translation Key</label>
                                                    <input type="text" class="form-control"  name="{{$languageLocaleCode}}[]"  value="{{old($languageLocaleCode)[0]??''}}" >

                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom02">Translation Value</label>
                                                    <input type="text" class="form-control" name="{{$languageLocaleCode}}[]"  value="{{old($languageLocaleCode)[1]??''}}" >
                                                </div>
                                            @endforeach

                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label class="col-form-label">File Name</label>
                                                    <select class="custom-select" name="fileName">
                                                        <option   selected disabled>Choice The Needed File</option>
                                                      @foreach($files as $file)
                                                            <option value="{{$file}}" @if(old('fileName')===$file) selected @endif>{{$file}}</option>
                                                      @endforeach
                                                    </select>
                                                    <hr>
                                                    <button class="btn btn-xs btn-block btn-outline-primary" onclick="return false" data-toggle="modal" data-target="#add-translation-file"><i class="ti-plus"></i> Add New Translation File</button>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Server side end -->
                        </div>
                    </div>
                    <div class="col-lg-8 col-ml-12">
                        <div class="row">
                            <!-- Textual inputs start -->
                            <div class="col-12">
                                <div class="card mt-5">
                                    <div class="card-body">
                                        <nav>
                                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                @foreach($languages as $languageLocaleCode => $languageNativeName)
                                                <a class="nav-item nav-link @if($loop->first) active show  @endif" id="nav-home-tab" data-toggle="tab" href="#nav-{{$languageLocaleCode}}" role="tab" aria-controls="nav-{{$languageLocaleCode}}" aria-selected="true">{{$languageNativeName['name']}}</a>
                                                @endforeach
                                            </div>
                                        </nav>
                                        <div class="tab-content mt-3" id="nav-tabContent">
                                            <button class="btn btn-primary pull-right" type="submit">Save</button>
                                            @foreach($translationFiles as $localeCode => $localeContent)
                                                <div class="tab-pane fade @if($loop->first) active show  @endif" id="nav-{{$localeCode}}" role="tabpanel" aria-labelledby="nav-home-tab">
                                                    <div class="card-body">
                                                        @foreach($localeContent as $fileName => $fileContent)
                                                            <h4 class="header-title" style="text-transform: lowercase"><i class="fa fa-file-o"></i>  ({{$fileName.'.php'}})</h4>
                                                            <div class="form-row">
                                                                @foreach($fileContent as $transkey => $transValue)
                                                                    <div class="col-md-6 mb-3" id="translation-key-{{$localeCode}}-{{$transkey}}">
                                                                        <label for="validationCustom01">
                                                                            <i class="ti-trash text-danger" style="cursor: pointer" title="{{trans('messages.removeFile')}}" onclick="removeThisKey('translation-key-{{$localeCode}}-{{$transkey}}')"></i>{{$transkey}}</label>
                                                                        <input type="text" class="form-control" value="{{$transValue}}" name="{{strtolower($fileName)}}[{{$localeCode}}][{{$transkey}}]">
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                        @endforeach

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- Textual inputs end -->

                        </div>
                    </div>
                </div>
            </form>
            </button>
            <div class="modal fade" id="add-translation-file">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New File</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('settings.translations.add')}}" method="post" id="addNewFileForm">
                                @csrf
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">
                                        File Name
                                    </label>
                                    <input name="file" class="form-control" type="text"   placeholder="example.php" >
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('addNewFileForm').submit()">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

    @endsection


@Section('js')
<script>
    function removeThisKey(nodeId){
        const myNode = document.getElementById(nodeId);
        myNode.remove();
    }
</script>

@endsection



