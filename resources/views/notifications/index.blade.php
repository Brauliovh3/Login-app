@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-bell"></i> Notificaciones</h1>
            <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-check-double"></i> Marcar todas como leídas
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="d-flex justify-content-between align-items-start mb-3 p-3 border rounded {{ $notification->read ? 'bg-light' : 'bg-white border-primary' }}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    @switch($notification->type)
                                        @case('success')
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            @break
                                        @case('error')
                                            <i class="fas fa-exclamation-circle text-danger me-2"></i>
                                            @break
                                        @case('warning')
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            @break
                                        @default
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                    @endswitch
                                    
                                    <h6 class="mb-0 {{ $notification->read ? '' : 'fw-bold' }}">
                                        {{ $notification->title }}
                                    </h6>
                                    
                                    @if(!$notification->read)
                                        <span class="badge bg-primary ms-2">Nuevo</span>
                                    @endif
                                </div>
                                
                                <p class="text-muted mb-2">{{ $notification->message }}</p>
                                
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                            
                            <div class="ms-3">
                                @if(!$notification->read)
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="markAsRead({{ $notification->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @endif
                                
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar esta notificación?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="d-flex justify-content-center">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tienes notificaciones</h5>
                        <p class="text-muted">Cuando tengas nuevas notificaciones aparecerán aquí.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
