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

                            <div class="modal fade" id="progressModal{{ $task->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('patroll.task-progress.update', $task->id) }}">
                                            @csrf

                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Task Progress</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                <input type="hidden" name="patroll_session_id" value="{{ $currentSession->id }}">
                                                <input type="hidden" name="status" value="completed">
                                                <input type="hidden" name="is_worked" value="worked">

                                                <div class="mb-2">
                                                    <label class="form-label">Progress Description</label>
                                                    <textarea class="form-control" name="progress_description" rows="2"></textarea>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label">Ambil Foto</label>

                                                    <video id="cameraStream{{ $task->id }}" autoplay playsinline style="width:100%;border-radius:10px;background:#000"></video>

                                                    <canvas id="captureCanvas{{ $task->id }}" style="display:none;"></canvas>

                                                    <button type="button" class="btn btn-primary mt-2" onclick="takePhoto({{ $task->id }})">
                                                        Ambil Foto
                                                    </button>

                                                    <img id="photoPreview{{ $task->id }}" class="mt-2" style="width:100%;display:none;border-radius:10px" />

                                                    <input type="hidden" name="image_base64" id="imageBase64{{ $task->id }}">
                                                </div>

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

@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById('progressModal{{ $task->id }}');
        modal.addEventListener('shown.bs.modal', function () {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(stream => {
                    document.getElementById("cameraStream{{ $task->id }}").srcObject = stream;
                });
        });
    });

    function takePhoto(id) {
        const video = document.getElementById("cameraStream" + id);
        const canvas = document.getElementById("captureCanvas" + id);
        const preview = document.getElementById("photoPreview" + id);
        const base64Input = document.getElementById("imageBase64" + id);

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL("image/jpeg", 0.8);

        base64Input.value = imageData;
        preview.src = imageData;
        preview.style.display = "block";
    }
</script>
@endpush
