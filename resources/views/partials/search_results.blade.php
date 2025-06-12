@if($results->isEmpty())
    <div class="text-muted">Không tìm thấy nhân sự nào phù hợp.</div>
@else
    <ul class="list-group">
        @foreach($results as $emp)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $emp->name_ingame }}</strong> - {{ $emp->position->name_positions ?? '' }}
                </div>
                <a href="#" class="btn btn-sm btn-outline-primary">Chi tiết</a>
            </li>
        @endforeach
    </ul>
@endif