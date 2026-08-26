@extends('layouts.admin')

@section('title', 'Logs hoạt động')

@section('content')
    <style>
        .logs-page {
            --logs-primary: #2563eb;
            --logs-primary-soft: #eff6ff;
            --logs-border: #e5e7eb;
            --logs-text: #111827;
            --logs-muted: #6b7280;
            --logs-bg: #f8fafc;
        }

        .logs-page .page-header {
            margin-bottom: 24px;
        }

        .logs-page .page-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logs-page .title-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--logs-primary-soft);
            color: var(--logs-primary);
            font-size: 19px;
        }

        .logs-page .page-title h2 {
            margin: 0;
            color: var(--logs-text);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.4px;
        }

        .logs-page .page-subtitle {
            margin: 4px 0 0;
            color: var(--logs-muted);
            font-size: 14px;
        }

        .logs-page .logs-count {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-top: 10px;
            padding: 5px 10px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 12px;
            font-weight: 600;
        }

        .logs-page .clear-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 10px;
            padding: 9px 14px;
            font-weight: 600;
            transition: all .2s ease;
        }

        .logs-page .clear-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(220, 38, 38, .12);
        }

        .logs-page .logs-card {
            overflow: hidden;
            border: 1px solid var(--logs-border) !important;
            background: #fff;
            box-shadow: 0 8px 30px rgba(15, 23, 42, .05) !important;
        }

        .logs-page .table-wrapper {
            overflow-x: auto;
        }

        .logs-page table {
            min-width: 980px;
        }

        .logs-page .table thead th {
            padding: 15px 18px;
            border: 0;
            background: #ffff;
            color: #010101;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .045em;
            white-space: nowrap;
        }

        .logs-page .table thead th:first-child {
            padding-left: 22px;
        }

        .logs-page .table tbody td {
            padding: 16px 18px;
            border-color: #f0f2f5;
            color: #374151;
            font-size: 14px;
            vertical-align: middle;
        }

        .logs-page .table tbody tr {
            transition: background-color .18s ease, box-shadow .18s ease;
        }

        .logs-page .table tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 3px 0 0 var(--logs-primary);
        }

        .logs-page .log-id {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 28px;
            padding: 0 8px;
            border-radius: 8px;
            background: #f3f4f6;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
        }

        .logs-page .time-date {
            color: #1f2937;
            font-weight: 650;
        }

        .logs-page .time-clock {
            color: #9ca3af;
            font-size: 12px;
        }

        .logs-page .user-box {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .logs-page .user-avatar {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            font-size: 13px;
            font-weight: 800;
            box-shadow: inset 0 0 0 1px rgba(146, 64, 14, .08);
        }

        .logs-page .user-name {
            color: #1f2937;
            font-size: 14px;
            font-weight: 650;
            line-height: 1.35;
        }

        .logs-page .username {
            margin-top: 2px;
            color: #9ca3af;
            font-size: 12px;
        }

        .logs-page .action-badge {
            display: inline-flex;
            align-items: center;
            max-width: 220px;
            padding: 7px 11px;
            border-radius: 8px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
        }

        .logs-page .target-text {
            color: #374151;
            font-weight: 550;
        }

        .logs-page .detail-text {
            display: block;
            max-width: 320px;
            color: #6b7280;
            line-height: 1.55;
            word-break: break-word;
        }

        .logs-page .empty-state {
            padding: 55px 20px !important;
        }

        .logs-page .empty-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 14px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #9ca3af;
            font-size: 20px;
        }

        .logs-page .pagination {
            margin-bottom: 0;
        }

        .logs-page .pagination .page-link {
            border-radius: 8px !important;
            margin: 0 3px;
            border-color: #e5e7eb;
            color: #4b5563;
        }

        .logs-page .pagination .page-item.active .page-link {
            background: var(--logs-primary);
            border-color: var(--logs-primary);
        }

        @media (max-width: 768px) {
            .logs-page .page-header {
                align-items: flex-start !important;
                flex-direction: column;
            }

            .logs-page .page-title h2 {
                font-size: 21px;
            }

            .logs-page .title-icon {
                width: 42px;
                height: 42px;
            }
        }
    </style>

    <div class="container-fluid py-4 logs-page">

        {{-- Page Header --}}
        <div class="page-header d-flex justify-content-between align-items-center gap-3">

            <div>
                <div class="page-title">

                    <div>
                        <h2>Logs</h2>
                        <p class="page-subtitle">
                            Hành động của hệ thống và người dùng.
                        </p>

                        <span class="logs-count">
                            <i class="fa-solid fa-list-check"></i>
                            {{ $logs->total() }} hoạt động
                        </span>
                    </div>
                </div>
            </div>

            @if (auth()->user()->role === 'admin')
                <form action="{{ route('logs.clear') }}"
                      method="POST"
                      onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ logs không?');">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-outline-danger btn-sm clear-btn">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Xóa toàn bộ logs</span>
                    </button>
                </form>
            @endif

        </div>

        {{-- Logs Table --}}
        <div class="card logs-card border-0">

            <div class="card-body p-0">

                <div class="table-responsive table-wrapper">

                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Thời gian</th>
                                <th>Người thao tác</th>
                                <th>Hành động</th>
                                <th>Mục tiêu</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($logs as $log)

                                <tr>

                                    {{-- ID --}}
                                    <td>
                                        <span class="log-id">
                                            {{ $log->id }}
                                        </span>
                                    </td>

                                    {{-- Time --}}
                                    <td>
                                        <div class="time-date">
                                            {{ $log->created_at->format('d/m/Y') }}
                                        </div>

                                        <div class="time-clock">
                                            <i class="fa-regular fa-clock me-1"></i>
                                            {{ $log->created_at->format('H:i:s') }}
                                        </div>
                                    </td>

                                    {{-- User --}}
                                    <td>
                                        <div class="user-box">

                                            <div class="user-avatar">
                                                {{ strtoupper(substr($log->user->employee->name_ingame ?? $log->user->username ?? 'U', 0, 1)) }}
                                            </div>

                                            <div>
                                                <div class="user-name">
                                                    {{ $log->user->employee->name_ingame ?? $log->user->username ?? 'Không rõ' }}
                                                </div>

                                                <div class="username">
                                                    {{ $log->user->username ?? 'N/A' }}
                                                </div>
                                            </div>

                                        </div>
                                    </td>

                                    {{-- Action --}}
                                    <td>
                                        <span class="action-badge">
                                            {{ $log->action }}
                                        </span>
                                    </td>

                                    {{-- Target --}}
                                    <td>
                                        <span class="target-text">
                                            {{ $log->target }}
                                        </span>
                                    </td>

                                    {{-- Detail --}}
                                    <td>
                                        <span class="detail-text">
                                            {{ $log->detail ?: '—' }}
                                            {{ $log->ip_address ? ' (IP: ' . $log->ip_address . ')' : '' }} <br>
                                            {{ $log->user_agent ? $log->user_agent : '' }} <br>
                                            {{ $log->device ? ' (Thiết bị: ' . $log->device . ')' : '' }}
                                            {{ $log->browser ? ' (Trình duyệt: ' . $log->browser . ')' : '' }}
                                            {{ $log->platform ? ' (Nền tảng: ' . $log->platform . ')' : '' }}
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center empty-state">

                                        <div class="empty-icon">
                                            <i class="fa-regular fa-folder-open"></i>
                                        </div>

                                        <div class="fw-semibold text-dark mb-1">
                                            Chưa có hoạt động nào
                                        </div>

                                        <div class="text-muted small">
                                            Chưa có hoạt động nào được ghi lại.
                                        </div>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- Pagination --}}
        @if ($logs->hasPages())

            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links() }}
            </div>

        @endif

    </div>
@endsection