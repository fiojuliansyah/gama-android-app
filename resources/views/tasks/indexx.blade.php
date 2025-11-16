@extends('layouts.master')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="#" class="header-title">Jadwal Pekerjaan</a>
    <a href="{{ route('home') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>
<br>
<div class="page-content pt-5">
    <div class="calendar bg-theme shadow-xl rounded-m">
        <div class="cal-header">
            <h4 class="cal-title text-center text-uppercase font-800 bg-highlight color-white">{{ now()->format('F Y') }}</h4>
        </div>
        <div class="clearfix"></div>

        <div class="cal-days bg-highlight opacity-80 bottom-0">
            @foreach(['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'] as $day)
                <a href="#">{{ $day }}</a>
            @endforeach
            <div class="clearfix"></div>
        </div>

        <div class="cal-dates cal-dates-border">
            @php
                $firstDayOfMonth = now()->startOfMonth()->format('w');
                $daysInMonth = now()->daysInMonth;
            @endphp        

            @for ($i = 0; $i < $firstDayOfMonth; $i++)
                <a href="#" class="cal-disabled">&nbsp;</a>
            @endfor

            @for ($i = 1; $i <= $daysInMonth; $i++)
                @php
                    $currentDate = now()->format('Y-m-') . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $tasksForDay = $tasks->where('date', $currentDate);
                    $dailyDot = $weeklyDot = $monthlyDot = false;
                @endphp
                <a href="#" class="cal-date {{ $currentDate == now()->format('Y-m-d') ? 'cal-selected' : '' }}" 
                   data-date="{{ $currentDate }}">
                    @foreach ($tasksForDay as $task)
                        @if($task->work_type == 'daily' && !$dailyDot)
                            <i class="fa fa-circle color-highlight" style="font-size: 7px; bottom: 2px; left: 50%; transform: translateX(-50%);"></i>
                            @php $dailyDot = true; @endphp
                        @elseif($task->work_type == 'weekly' && !$weeklyDot)
                            <i class="fa fa-circle color-blue-dark" style="font-size: 7px; bottom: 2px; left: 50%; transform: translateX(-50%);"></i>
                            @php $weeklyDot = true; @endphp
                        @elseif($task->work_type == 'monthly' && !$monthlyDot)
                            <i class="fa fa-circle color-yellow-dark" style="font-size: 7px; bottom: 2px; left: 50%; transform: translateX(-50%);"></i>
                            @php $monthlyDot = true; @endphp
                        @endif
                    @endforeach
                    <span>{{ $i }}</span>
                </a>
            @endfor
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="decoration decoration-margins"></div>

    <div class="calendar bg-theme shadow-xl rounded-m">
        <div class="cal-footer">
            <h6 class="cal-sub-title uppercase bold bg-highlight color-white">Schedule Box</h6>
            <div class="divider mb-0"></div>
            @foreach($tasks as $task)
                <div class="cal-schedule" data-date="{{ $task->date }}" 
                     style="display: {{ $task->date == now()->format('Y-m-d') ? 'block' : 'none' }};">
                    <em>
                        {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }}
                        <br>
                        @if ($task->task_progress && $task->task_progress->status == 'in_progress')
                            <button class="badge bg-blue-dark rounded-xl">progress</button>
                        @elseif($task->task_progress && $task->task_progress->status == 'completed')
                            <button class="badge bg-green-dark rounded-xl">completed</button> 
                        @else
                            <button class="badge bg-yellow-dark rounded-xl">pending</button>
                        @endif
                    </em>
                    <div class="d-flex w-100 justify-content-between">
                        <strong class="
                        d-block mb-n2
                        @if($task->work_type == 'weekly')
                        color-blue-dark
                        @elseif($task->work_type == 'monthly')
                        color-yellow-dark
                        @else
                        @endif
                        ">{{ $task->name }}</strong>
                        <div style="padding-right: 20px">
                            @if (Carbon\Carbon::parse($task->date)->isToday())
                                @if($task->task_progress && $task->task_progress->status == 'in_progress')
                                    <a href="#" data-menu="menu-end-{{ $task->id }}" class="btn btn-xxs rounded-xl bg-green-dark mt-3 menu-open">Selesaikan</a>
                                @else
                                    <a href="#" data-menu="menu-join-event-{{ $task->id }}" class="btn btn-xxs rounded-xl bg-highlight mt-3 menu-open">Mulai</a>
                                @endif
                            @endif
                        </div>
                    </div>
                    <span><i class="fas fa-map-pin"></i>{{ $task->floor }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Inside your foreach loop -->
@foreach($tasks as $task)  
    <div id="menu-join-event-{{ $task->id }}" class="menu menu-box-bottom menu-box-detached" data-menu-height="400">
        <div class="menu-title"><h1>Mulai Pekerjaan</h1><p class="color-highlight">Mulai atau batalkan pekerjaan ini</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a></div>
        <form id="formStore-{{ $task->id }}" method="POST" action="{{ route('progress.start') }}" enctype="multipart/form-data">
            @csrf
            <div class="content mb-0 mt-3">
                <input type="hidden" name="task_planner_id" value="{{ $task->id }}">
                <div class="file-data pb-4">
                    <input type="file" id="file-upload-{{ $task->id }}" name="image"
                        class="upload-file border-highlight rounded-sm" accept="image/*">
                    <p class="upload-file-text color-theme opacity-70 border pt-4 pb-5 rounded-sm"><i
                            class="fa fa-image ms-n3 pe-3"></i>Upload Pekerjaan Sebelumnya</p>
                </div>
                <div class="list-group list-custom-large upload-file-data disabled">
                    <img id="image-data-{{ $task->id }}" src="/mobile/images/empty.png" class="img-fluid rounded-m mb-4">
                    <div class="disabled">
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-info-circle color-blue-dark"></i>
                            <span>File Name</span>
                            <strong class="upload-file-name">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-weight-hanging color-brown-dark"></i>
                            <span>File Size</span>
                            <strong class="upload-file-size">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-tag color-red-dark"></i>
                            <span>File Type</span>
                            <strong class="upload-file-type">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0 pb-4">
                            <i class="fa font-14 fa-clock color-blue-dark"></i>
                            <span>Modified Date</span>
                            <strong class="upload-file-modified">JS Populated</strong>
                        </a>  
                    </div>
                </div>
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
                            <a href="#" onclick="event.preventDefault(); document.getElementById('formStore-{{ $task->id }}').submit();" class="btn btn-full btn-icon rounded-sm btn-m bg-green-dark text-uppercase font-700"><i class="fa fa-check bg-transparent"></i> Mulai</a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="close-menu btn btn-full btn-icon rounded-sm btn-m bg-red-dark text-uppercase font-700"><i class="fa fa-times bg-transparent"></i> Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="menu-end-{{ $task->id }}" class="menu menu-box-bottom menu-box-detached" data-menu-height="450">
        <div class="menu-title"><h1>Selesaikan Pekerjaan</h1><p class="color-highlight">Selesaikan atau batalkan pekerjaan ini</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a></div>
        <form id="formStore-end-{{ $task->id }}" method="POST" action="{{ route('progress.end') }}" enctype="multipart/form-data">
            @csrf
            <div class="content mb-0 mt-3">
                <input type="hidden" name="task_planner_id" value="{{ $task->id }}">
                <div class="file-data pb-4">
                    <input type="file" id="file-upload-{{ $task->id }}" name="image"
                        class="upload-file border-highlight rounded-sm" accept="image/*">
                    <p class="upload-file-text color-theme opacity-70 border pt-4 pb-5 rounded-sm"><i
                            class="fa fa-image ms-n3 pe-3"></i>Upload Pekerjaan Setelahnya</p>
                </div>
                <div class="list-group list-custom-large upload-file-data disabled">
                    <img id="image-data-{{ $task->id }}" src="/mobile/images/empty.png" class="img-fluid rounded-m mb-4">
                    <div class="disabled">
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-info-circle color-blue-dark"></i>
                            <span>File Name</span>
                            <strong class="upload-file-name">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-weight-hanging color-brown-dark"></i>
                            <span>File Size</span>
                            <strong class="upload-file-size">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-tag color-red-dark"></i>
                            <span>File Type</span>
                            <strong class="upload-file-type">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0 pb-4">
                            <i class="fa font-14 fa-clock color-blue-dark"></i>
                            <span>Modified Date</span>
                            <strong class="upload-file-modified">JS Populated</strong>
                        </a>  
                    </div>
                </div>
                <div class="input-style has-borders no-icon mb-4">
                    <input type="text" class="form-control" id="form7" name="progress_description" placeholder="Progress yang dikerjakan"> 
                    <label for="form7" class="color-highlight">Progress yang dikerjakan</label>
                </div>
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
                            <a href="#" onclick="event.preventDefault(); document.getElementById('formStore-end-{{ $task->id }}').submit();" class="btn btn-full btn-icon rounded-sm btn-m bg-green-dark text-uppercase font-700"><i class="fa fa-check bg-transparent"></i> Selesaikan</a>                        </div>
                        <div class="col-6">
                            <a href="#" class="close-menu btn btn-full btn-icon rounded-sm btn-m bg-red-dark text-uppercase font-700"><i class="fa fa-times bg-transparent"></i> Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endforeach
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.cal-date').forEach(function(dateElement) {
                dateElement.addEventListener('click', function(event) {
                    event.preventDefault();
                    
                    let selectedDate = this.getAttribute('data-date');
                    let taskList = document.querySelectorAll('.cal-schedule');
                    
                    document.querySelectorAll('.cal-date').forEach(el => el.classList.remove('cal-selected'));
                    
                    this.classList.add('cal-selected');
                    
                    taskList.forEach(function(task) {
                        if (task.getAttribute('data-date') === selectedDate) {
                            task.style.display = 'block';
                        } else {
                            task.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
@endpush
