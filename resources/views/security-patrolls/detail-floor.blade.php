@extends('layouts.app')

@section('content')

<div class="page-content pb-5">

    <div class="header header-fixed header-logo-center">
        <span class="header-title">Detail Floor</span>
        <a href="javascript:history.back()" class="header-icon header-icon-1">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <br><br>

    <div class="content mt-5">
        <h4 class="font-700 mb-1">{{ $floor->name ?? 'Floor' }}</h4>
        <p class="mb-3 color-gray">Detail dan daftar task untuk lantai ini.</p>

        <h4 class="font-700 mb-2">Task Planner</h4>

        @if ($taskPlanners->isEmpty())
            <div class="text-center py-3 color-gray">
                Tidak ada task untuk lantai ini.
            </div>
        @else
            <ul class="list-group mb-3">
                @foreach ($taskPlanners as $task)
                    @php
                        $progress = $task->patrollProgresses()
                                        ->where('status', 'reported')
                                        ->where('patroll_session_id', $currentSession->id ?? 0)
                                        ->first();
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $task->name ?? '-' }}</strong><br>
                            <small>{{ $task->service_type ?? '-' }} / {{ $task->work_type ?? '-' }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            @if ($progress)
                                <button type="button" class="btn btn-sm btn-success" disabled>Completed</button>
                            @else
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#progressModal{{ $task->id }}">
                                    Update Progress
                                </button>
                            @endif

                            <div class="modal fade" id="progressModal{{ $task->id }}" tabindex="-1" aria-labelledby="progressModalLabel{{ $task->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('patroll.task-progress.update', $task->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="progressModalLabel{{ $task->id }}">Update Task Progress</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="patroll_session_id" value="{{ $currentSession->id }}">
                                                <div class="mb-2">
                                                    <label class="form-label">Progress Description</label>
                                                    <textarea class="form-control" name="progress_description" rows="2"></textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" class="form-control" name="image" accept="image/*" capture="environment">
                                                </div>

                                                <input type="hidden" name="status" value="completed">
                                                <input type="hidden" name="is_worked" value="worked">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-success">Save Progress</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="ad-300x50 ad-300x50-fixed">
        <a href="{{ route('patroll.scan') }}" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight">
            KEMBALI SCAN
        </a>
    </div>

</div>

@endsection
