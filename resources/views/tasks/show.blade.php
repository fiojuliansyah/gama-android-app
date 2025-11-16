@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="#" class="header-title">Tugas Hari Ini</a>
    <a href="{{ route('schedule') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>
<br>
<div class="content pt-5">
    <form id="formStore" method="POST" action="{{ route('progress.start') }}" enctype="multipart/form-data">
        @csrf
        <div class="content mb-0 mt-3">
            <input type="hidden" name="task_planner_id" value="{{ $task->id }}">

            <div>
                <span class="color-highlight font-300 d-block text-uppercase font-10 pt-3">{{ $task->date }}</span>
                <strong class="color-theme font-20 d-block mt-n2 mb-n2">{{ $task->name }}</strong>
                <span class="font-11 color-theme opacity-30 d-block pb-2 pt-2"><i class="fa fa-map-marker pe-2"></i>{{ $task->floor }}</span>
                <div class="clearfix"></div>
            </div>
            <div class="divider mt-3 mb-2"></div>
            <div class="content mb-0 mx-0">
                <div class="row mb-0">
                    <div class="col-6">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('formStore').submit();" class="btn btn-full btn-icon rounded-sm btn-m bg-green-dark text-uppercase font-700"><i class="fa fa-check bg-transparent"></i> Mulai</a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('schedule') }}" class="btn btn-full btn-icon rounded-sm btn-m bg-red-dark text-uppercase font-700"><i class="fa fa-times bg-transparent"></i> Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection