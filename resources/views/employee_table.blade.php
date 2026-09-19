@extends('layouts.app')
@section('hide_css')
@endsection
<style>
    :root {
        --bg: #101010;
        --panel: #ffffff;
        --gold: #c99d1c;
        --gold2: #f0c85b;
        --line: #c69518;
        --text: #0c0c0c;
        --muted: #9ba2ad
    }

    * {
        box-sizing: border-box
    }

    body {
        margin: 0;
        background:
            radial-gradient(circle at 50% -15%, rgba(201, 157, 28, .10), transparent 30%), var(--bg);
        color: var(--text);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
    }

    .container {
        width: min(1500px, calc(100% - 24px));
        margin: auto
    }

    .header {
        padding: 24px 0 17px;
    }

    .brand {
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 1.8px;
        color: var(--gold2);
        text-transform: uppercase
    }

    h1 {
        margin: 7px 0 4px;
        font-size: 29px
    }

    .sub {
        margin: 0;
        color: var(--muted);
        font-size: 12px
    }

    .toolbar {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 220px 220px 105px;
        gap: 8px;
        margin-top: 18px
    }

    input,
    select {
        width: 100%;
        height: 40px;
        padding: 0 11px;
        border-radius: 6px;
        border: 1px solid var(--line);
        background: #ffff;
        color: var(--text);
        outline: none
    }

    input:focus,
    select:focus {
        border-color: var(--gold)
    }

    .toolbar button {
        cursor: pointer;
        color: #fff;
        border-color: var(--line);
        font-weight: 900;
        border-radius: 6px;
        background: var(--gold);

        &:hover {
            background: var(--gold2)
        }
    }

    .stats {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin: 16px 0
    }

    .stat {
        min-width: 135px;
        padding: 9px 13px;
        border: 1px solid var(--line);
        border-radius: 6px;
        background: var(--panel)
    }

    .stat small {
        display: block;
        color: var(--muted);
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase
    }

    .stat strong {
        color: var(--gold2);
        font-size: 20px
    }

    .management {
        margin-bottom: 15px
    }

    .section-title {
        margin: 0 0 8px;
        color: var(--gold2);
        font-size: 13px;
        text-transform: uppercase;
        font-weight: 950
    }

    .departments {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
        align-items: start;
    }

    .department {
        border: 1px solid var(--gold);
        border-radius: 5px;
        overflow: hidden;
        background: var(--panel)
    }

    .department-title {
        display: flex;
        align-items: center;
        gap: 7px;
        justify-content: center;
        padding: 7px 8px;
        background: linear-gradient(90deg, #b58a0c, #d4a92d, #b58a0c);
        color: #171208
    }

    .department-title h2 {
        margin: 0;
        font-size: 10px;
        font-weight: 950;
        text-transform: uppercase;
    }

    .department-title span {
        font-size: 8px;
        font-weight: 950;
        white-space: nowrap;
    }

    .table-wrap {
        overflow: auto
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        min-width: 0;
    }

    th {
        padding: 6px 3px;
        background: #f0c85b;
        color: #16120a;
        border-bottom: 1px solid var(--gold);
        font-size: 8px;
        font-weight: 950;
        text-transform: uppercase;
        text-align: center !important
    }

    td {
        padding: 5px 4px;
        border-bottom: 1px solid #2e281a;
        font-size: 10px;
        line-height: 1.15;
        vertical-align: middle
    }

    tr:last-child td {
        border-bottom: 0
    }

    tbody tr:hover {
        background: rgba(201, 157, 28, .08)
    }

    th:nth-child(1),
    td:nth-child(1) {
        width: 25%
    }

    th:nth-child(2),
    td:nth-child(2) {
        width: 18%
    }

    th:nth-child(3),
    td:nth-child(3) {
        width: 23%
    }

    th:nth-child(4),
    td:nth-child(4) {
        width: 34%
    }

    td:nth-child(1),
    td:nth-child(2),
    td:nth-child(3) {
        text-align: center
    }

    td:nth-child(2) {
        color: var(--gold2);
        font-weight: 900
    }

    td:nth-child(4) {
        color: #000;
        font-weight: 800;
        overflow-wrap: anywhere
    }

    .empty,
    .error {
        padding: 38px 15px;
        border: 1px solid var(--line);
        border-radius: 6px;
        text-align: center;
        color: var(--muted);
        background: var(--panel)
    }

    .error {
        color: #ffbbc1;
        border-color: #68373d;
        background: #1a1113
    }

    .footer {
        padding: 20px 0 30px;
        color: #626a74;
        text-align: center;
        font-size: 9px
    }

    .capCao table {
        border: 1px solid var(--line);
        width: 100%;
        text-align: center;
    }

    .capCao {
        margin: 0 200px;
    }

    @media(max-width:1150px) {
        .departments {
            grid-template-columns: repeat(2, minmax(0, 1fr))
        }
    }

    @media(max-width:700px) {
        .toolbar {
            grid-template-columns: 1fr
        }

        .departments {
            grid-template-columns: 1fr
        }

        h1 {
            font-size: 24px
        }
    }
</style>

@section('content')
<header class="header">
    <div class="container">
        <div class="brand">LOS SANTOS COUNTY SHERIFF'S DEPARTMENT</div>
        <h1>Danh sách nhân sự</h1>
        <p class="sub">Nhân sự thành viên LLCS LSSD SV1 - Chưa tính Quản Lý Cấp Cao</p>

        <div class="toolbar">
            <input id="search" type="search" placeholder="Tìm tên, số hiệu, chức vụ, quân hàm...">
            <select id="division">
                <option value="all">Tất cả đơn vị</option>
            </select>
            <select id="rank">
                <option value="all">Tất cả quân hàm</option>
            </select>
            <button class="btn" id="reload"><i class="fa-solid fa-arrows-rotate"></i> r-load</button>
        </div>
    </div>
</header>

<main class="container">
    <div class="stats">
        <div class="stat"><small>Tổng nhân sự</small><strong id="total">...</strong></div>
        <div class="stat"><small>Số bảng</small><strong id="tables">...</strong></div>
        <div class="stat"><small>Đội I - X</small><strong id="teamCount">...</strong></div>
        <div class="stat"><small>Traffic</small><strong id="trafficCount">...</strong></div>
    </div>
    <div class="arena_emp">
        <div class="capCao mb-3">
            <table class="roster">
                <thead>
                    <tr>
                        <th>Chức Vụ</th>
                        <th>Số Hiệu</th>
                        <th>Quân Hàm</th>
                        <th>Tên</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cucTruong as $employee)
                    <tr class="r-chief">
                        <td>{{ $employee->position->name_positions ?? 'N/A' }}</td>
                        <td class="c">000</td>
                        <td>{{ $employee->rank->name_ranks ?? 'N/A' }}</td>
                        <td>{{ $employee->name_ingame }}</td>
                    </tr>
                    @endforeach
                    @foreach ($phoCucTruong as $employee)
                    <tr class="r-deputy">
                        <td>{{ $employee->position->name_positions ?? 'N/A' }}</td>
                        <td class="c">00{{ $loop->index + 1 }}</td>
                        <td>{{ $employee->rank->name_ranks ?? 'N/A' }}</td>
                        <td>{{ $employee->name_ingame }}</td>
                    </tr>
                    @endforeach
                    @foreach ($troLy as $employee)
                    <tr class="r-deputy">
                        <td>{{ $employee->position->name_positions ?? 'N/A' }}</td>
                        <td class="c">002</td>
                        <td>{{ $employee->rank->name_ranks ?? 'N/A' }}</td>
                        <td>{{ $employee->name_ingame }}</td>
                    </tr>
                    @endforeach
                    @foreach ($thuKy as $employee)
                    <tr class="r-deputy">
                        <td>{{ $employee->position->name_positions ?? 'N/A' }}</td>
                        <td class="c">TK-00</td>
                        <td>{{ $employee->rank->name_ranks ?? 'N/A' }}</td>
                        <td>{{ $employee->name_ingame }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="managementArea"></div>
        <div id="departments" class="departments"></div>
        <div id="message"></div>
    </div>

    <div class="footer">LSSD Personnel Registry • Google Sheets → Apps Script API</div>
</main>

<script>
    const API_URL = "https://script.google.com/macros/s/AKfycbzvRfYogWHJwYVCnm1fk-LUApVMynENnYCe6Da6gLxdKaXG9QuIWL4R651WtNdN7E38oA/exec";
    let tables = [],
        employees = [];

    function clean(e) {
        return String(e ?? "").trim()
    }

    function normalize(e) {
        return clean(e).toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")
    }

    function esc(e) {
        return clean(e).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;")
    }

    function order(e, n) {
        const t = ["PHÒNG CẢNH SÁT ĐỘI I", "PHÒNG CẢNH SÁT ĐỘI II", "PHÒNG CẢNH SÁT ĐỘI III", "PHÒNG CẢNH SÁT ĐỘI IV", "PHÒNG CẢNH SÁT ĐỘI V", "PHÒNG CẢNH SÁT ĐỘI VI", "PHÒNG CẢNH SÁT ĐỘI VII", "PHÒNG CẢNH SÁT ĐỘI VIII", "PHÒNG CẢNH SÁT ĐỘI IX", "PHÒNG CẢNH SÁT ĐỘI X", "PHÒNG CẢNH SÁT ĐỘI NPDH", "PHÒNG CẢNH SÁT GIAO THÔNG (MW)"],
            o = t.indexOf(e),
            i = t.indexOf(n);
        return o < 0 && i < 0 ? e.localeCompare(n, "vi") : o < 0 ? 1 : i < 0 ? -1 : o - i
    }

    function matches(e) {
        const n = normalize(document.getElementById("search").value),
            t = document.getElementById("division").value,
            o = document.getElementById("rank").value,
            i = normalize([e.name, e.badge, e.position, e.rank, e.division].join(" "));
        return (!n || i.includes(n)) && ("all" === t || e.division === t) && ("all" === o || e.rank === o)
    }

    function updateStats() {
        document.getElementById("total").textContent = employees.length, document.getElementById("tables").textContent = tables.length;
        const e = new Set(employees.map(e => e.division).filter(e => /^PHÒNG CẢNH SÁT ĐỘI (I|II|III|IV|V|VI|VII|VIII|IX|X)$/.test(e)));
        document.getElementById("teamCount").textContent = e.size, document.getElementById("trafficCount").textContent = employees.filter(e => normalize(e.division).includes("giao thong")).length
    }

    function buildFilters() {
        const e = [...new Set(employees.map(e => e.division).filter(Boolean))].sort(order),
            n = [...new Set(employees.map(e => e.rank).filter(Boolean))].sort((e, n) => e.localeCompare(n, "vi"));
        document.getElementById("division").innerHTML = '<option value="all">Tất cả đơn vị</option>' + e.map(e => `<option value="${esc(e)}">${esc(e)}</option>`).join(""), document.getElementById("rank").innerHTML = '<option value="all">Tất cả quân hàm</option>' + n.map(e => `<option value="${esc(e)}">${esc(e)}</option>`).join("")
    }

    function filteredRows(e) {
        return (e.rows || []).map(n => ({
            ...n,
            division: e.division || "",
            tableTitle: e.title || ""
        })).filter(matches)
    }

    function tableHtml(e) {
        const n = filteredRows(e);
        return n.length ? `\n    <section class="department">\n      <div class="department-title">\n        <h2>${esc(e.division||e.title)}</h2>\n          </div>\n      <div class="table-wrap">\n        <table>\n          <thead>\n            <tr>\n              <th>Chức vụ</th>\n              <th>Số hiệu</th>\n              <th>Quân hàm</th>\n              <th>Tên</th>\n            </tr>\n          </thead>\n          <tbody>\n            ${n.map(e=>`\n              <tr>\n                <td>${esc(e.position)}</td>\n                <td>${esc(e.badge)}</td>\n                <td>${esc(e.rank)}</td>\n                <td>${esc(e.name)}</td>\n              </tr>\n            `).join("")}\n          </tbody>\n        </table>\n      </div>\n    </section>\n  ` : ""
    }

    function render() {
        const e = tables.filter(e => filteredRows(e).length),
            n = e.filter(e => normalize(e.division).includes("quan ly ban nganh")),
            t = e.filter(e => !normalize(e.division).includes("quan ly ban nganh"));
        document.getElementById("managementArea").innerHTML = n.length ? `<div class="management">\n           <div class="section-title">Quản lý ban ngành LSSD</div>\n           ${n.map(tableHtml).join("")}\n         </div>` : "", document.getElementById("departments").innerHTML = t.sort((e, n) => order(e.division, n.division)).map(tableHtml).join(""), document.getElementById("message").innerHTML = e.length ? "" : '<div class="empty">Không tìm thấy nhân sự phù hợp.</div>'
    }
    async function loadData() {
        const e = document.getElementById("message");
        e.innerHTML = '<div class="empty">Đang tải dữ liệu từ Google Sheets...</div>';
        try {
            const e = await fetch(API_URL, {
                cache: "no-store"
            });
            if (!e.ok) throw new Error("HTTP " + e.status);
            const n = await e.json();
            if (!1 === n.success) throw new Error(n.message || "API báo lỗi");
            if (tables = Array.isArray(n.tables) ? n.tables : [], employees = Array.isArray(n.employees) ? n.employees : [], !tables.length && employees.length) {
                const e = {};
                employees.forEach(n => {
                    const t = n.division || "Khác";
                    e[t] || (e[t] = []), e[t].push(n)
                }), tables = Object.keys(e).map((n, t) => ({
                    order: t + 1,
                    title: n,
                    division: n,
                    rows: e[n].map(e => ({
                        position: e.position || "",
                        badge: e.badge || "",
                        rank: e.rank || "",
                        name: e.name || ""
                    }))
                }))
            }
            if (!tables.length) throw new Error("API chưa trả về tables[].");
            updateStats(), buildFilters(), render()
        } catch (n) {
            console.error(n), e.innerHTML = `<div class="error"><b>Không lấy được dữ liệu.</b><br><br>${esc(n.message)}</div>`
        }
    }
    document.getElementById("search").addEventListener("input", render), document.getElementById("division").addEventListener("change", render), document.getElementById("rank").addEventListener("change", render), document.getElementById("reload").addEventListener("click", loadData), loadData();
</script>
@endsection