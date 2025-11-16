@extends('layouts.master')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="#" class="header-title">Jadwal Pekerjaan</a>
    <a href="{{ route('home') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>
<br>
<div class="content pt-5" id="tab-group-1">
    <div class="tab-controls tabs-small tabs-rounded" data-highlight="bg-highlight">
        <a href="#" data-active data-bs-toggle="collapse" data-bs-target="#pending">Pending</a>
        <a href="#" data-bs-toggle="collapse" data-bs-target="#in_progress">Berlangsung</a>
        <a href="#" data-bs-toggle="collapse" data-bs-target="#completed">Selesai</a>
        <a href="#" data-bs-toggle="collapse" data-bs-target="#tomorrow">Besok</a>
    </div>
    <div class="clearfix mb-3"></div>

    <!-- Pending -->
    <div data-bs-parent="#tab-group-1" class="collapse show" id="pending">
        <div class="content mb-0">
            <h4>Pending</h4>
            <div class="list-group list-custom-large me-2">
                @foreach ($tasksPending as $task)  
                    <a href="{{ route('schedule.show', $task->id) }}">
                        <span>{{ $task->name }}</span>
                        <strong>{{ $task->work_type }} - Mulai {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>      
                @endforeach
            </div>  
        </div>
    </div>

    <!-- In Progress -->
    <div data-bs-parent="#tab-group-1" class="collapse" id="in_progress">
        <div class="content mb-0">
            <h4>Berlangsung</h4>
            <div class="list-group list-custom-large me-2">
                @foreach ($taskProgressInProgress as $taskProgress)  
                    <a href="#">
                        <span>{{ $taskProgress->taskPlanner->name }}</span>
                        <strong>{{ $taskProgress->taskPlanner->work_type }} - Mulai {{ \Carbon\Carbon::parse($taskProgress->start_time)->format('H:i') }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>      
                @endforeach
            </div>
        </div>
    </div>

    <!-- Completed -->
    <div data-bs-parent="#tab-group-1" class="collapse" id="completed">
        <div class="content mb-0">
            <h4>Selesai</h4>
            <div class="list-group list-custom-large me-2">
                @foreach ($taskProgressCompleted as $taskProgress)  
                    <a href="#">
                        <span>{{ $taskProgress->taskPlanner->name }}</span>
                        <strong>{{ $taskProgress->taskPlanner->work_type }} - Mulai {{ \Carbon\Carbon::parse($taskProgress->start_time)->format('H:i') }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>      
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tomorrow -->
    <div data-bs-parent="#tab-group-1" class="collapse" id="tomorrow">
        <div class="content mb-0">
            <h4>Besok</h4>
            <div class="list-group list-custom-large me-2">
                @foreach ($tasksTomorrow as $task)
                    <a href="#">
                        <span>{{ $task->name }}</span>
                        <strong>{{ $task->work_type }} - Mulai {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
