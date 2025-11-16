@extends('layouts.module')

@section('content')
<div class="page-content">
    <div class="page-title page-title-small">
        <h2 style="color: black"><a href="#" data-back-button><i class="fa fa-arrow-left" style="color: black"></i></a>{{ $typeLeave->name }}</h2>
        <div class="divider"></div>
    </div>
    <div class="content mt-0 mb-0">
        <form id="formStore" method="POST" action="{{ route('leave.store') }}" enctype="multipart/form-data">
            @csrf
                <div class="content mb-0"> 
                    <input type="hidden" name="type_id" value="{{ $typeLeave->id }}">
                    <div class="mt-2">
                        <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
                            <input type="date" name="start_date" class="form-control">
                            <label for="form2" class="color-highlight font-400 font-13">Tanggal Pengajuan</label>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
                            <input type="date" name="end_date" class="form-control">
                            <label for="form2" class="color-highlight font-400 font-13">Tanggal Berakhir</label>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span>Tambah Gambar</span>
                        <br>
                        <label for="file-upload" class="float-right btn btn-xs bg-highlight rounded-xl shadow-xl text-uppercase font-900 mt-2 font-11"><i class="fas fa-camera" style="color: white;"></i> Upload</label>  
                        <input type="file" id="file-upload" name="image" style="display: none;" onchange="previewImage()">
                        <img id="image-preview" src="/assets/mobiles/images/empty.png" alt="Preview" class="preload-img img-fluid bottom-20 mt-3">
                    </div>
                    <div class="mt-5">
                        <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
                            <textarea name="reason" class="form-control"></textarea>
                            <label for="form2" class="color-highlight font-400 font-13">Alasan</label>
                        </div>
                    </div>
                    <div class="mt-5">
                        <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
                            <input type="text" name="contact" class="form-control">
                            <label for="form2" class="color-highlight font-400 font-13">No Darurat</label>
                        </div>
                    </div>
                </div> 
        </form>
    </div>
    <div class="ad-300x50 ad-300x50-fixed">
        <a href="#" onclick="event.preventDefault(); submitForm();" id="submit-btn" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight">
            Submit
        </a>
    </div>
</div>

@endsection

@push('js')
<script>
    function previewImage() {
        var input = document.getElementById('file-upload');
        var imageContainer = document.getElementById('image-preview');
        var files = input.files;
        var file = files[files.length - 1];

        var reader = new FileReader();

        reader.onload = function(e) {
            imageContainer.src = e.target.result;
        };

        reader.readAsDataURL(file);
    }

    function submitForm() {
        // Disable the submit button to prevent multiple clicks
        var submitButton = document.getElementById('submit-btn');
        submitButton.disabled = true;

        // Submit the form
        document.getElementById('formStore').submit();
    }
</script>
@endpush