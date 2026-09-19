@extends('layouts.admin')

@section('title', 'Lịch Sử Truy Cập')

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
        <div>
            <h1 class="h4 mb-1">Lịch Sử Truy Cập</h1>
            <p class="text-muted mb-0">Theo dõi page view hợp lệ và trạng thái xác thực.</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-chart-line me-1"></i> Tổng quan</a>
    </div>

    <form class="card border-0 shadow-sm p-3 mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-2"><select name="period" class="form-select"><option value="">Tất cả thời gian</option><option value="today" @selected(request('period') === 'today')>Hôm nay</option><option value="7d" @selected(request('period') === '7d')>7 ngày</option><option value="30d" @selected(request('period') === '30d')>30 ngày</option></select></div>
            <div class="col-md-2"><select name="visitor" class="form-select"><option value="">Guest / User</option><option value="guest" @selected(request('visitor') === 'guest')>Guest</option><option value="user" @selected(request('visitor') === 'user')>User</option></select></div>
            <div class="col-md-3"><input name="user" value="{{ request('user') }}" class="form-control" placeholder="Username hoặc tên nhân sự"></div>
            <div class="col-md-3"><input name="url" value="{{ request('url') }}" class="form-control" placeholder="Tìm URL"></div>
            <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill"><i class="fa-solid fa-filter"></i></button><a href="{{ route('admin.visits.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a></div>
        </div>
    </form>

    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>User</th><th>IP</th><th>URL / Route</th><th>Device</th><th>Browser</th><th>Thời gian</th><th>Trạng thái</th></tr></thead>
        <tbody>
        @forelse($visits as $visit)
            @php($employee = $visit->user?->employee)
            <tr>
                <td><div class="d-flex align-items-center gap-2"><img src="{{ $employee?->avatar ? asset('storage/'.$employee->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($employee?->name_ingame ?? $visit->user?->username ?? 'Guest') }}" width="32" height="32" class="rounded-circle" alt="Avatar"><span>{{ $employee?->name_ingame ?? $visit->user?->username ?? 'Guest' }}</span></div></td>
                <td class="font-monospace small">{{ $visit->ip_address ?? '-' }}</td>
                <td><div class="text-break small">{{ $visit->url }}</div><small class="text-muted">{{ $visit->route_name ?? '-' }}</small></td>
                <td>{{ ucfirst($visit->device_type ?? '-') }}</td><td>{{ $visit->browser ?? '-' }}</td>
                <td title="{{ $visit->created_at }}">{{ $visit->created_at?->diffForHumans() }}</td>
                <td><span class="badge {{ $visit->user_id ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $visit->user_id ? 'Authenticated' : 'Guest' }}</span></td>
            </tr>
        @empty <tr><td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu truy cập.</td></tr> @endforelse
        </tbody>
    </table></div></div>
    <div class="mt-3">{{ $visits->links() }}</div>
</div>
@endsection