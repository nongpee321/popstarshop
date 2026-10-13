import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace the static select dropdown
static_select = """        <div class="ms-3">
            <select class="form-select dash-input fw-semibold" style="min-width: 200px;">
                <option>สาขาปัจจุบัน: สำนักงานใหญ่</option>
            </select>
        </div>"""

dynamic_select = """        <div class="ms-3">
            <form id="branchFilterForm" method="GET" action="{{ route('dashboard') }}" class="m-0">
                <input type="hidden" name="from" value="{{ $from }}">
                <input type="hidden" name="to" value="{{ $to }}">
                <select name="branch_id" class="form-select dash-input fw-semibold" style="min-width: 200px;" onchange="document.getElementById('branchFilterForm').submit();">
                    <option value="all" {{ $currentBranchId === null ? 'selected' : '' }}>รวมยอดทุกสาขา</option>
                    @foreach($allBranches as $branch)
                        <option value="{{ $branch->id }}" {{ $currentBranchId == $branch->id ? 'selected' : '' }}>สาขา: {{ $branch->name_th }}</option>
                    @endforeach
                </select>
            </form>
        </div>"""

content = content.replace(static_select, dynamic_select)

# Replace the static branch info badge
static_badge = """<div class="dash-bg-red-soft px-2 py-1 rounded fw-bold"><i class="bi bi-shop me-1"></i> แสดงข้อมูลเฉพาะสาขาของคุณ: สำนักงานใหญ่</div>"""
dynamic_badge = """<div class="dash-bg-red-soft px-2 py-1 rounded fw-bold"><i class="bi bi-shop me-1"></i> ข้อมูล: {{ $scopeBranchName ?? 'รวมยอดทุกสาขา' }}</div>"""

content = content.replace(static_badge, dynamic_badge)

# Also fix the branch info passed dynamically into the date filter form
# Wait, the date filter form currently submits GET to dashboard with from/to. 
# It should also preserve branch_id!
static_filter_form = """<form class="d-flex align-items-center gap-2 flex-wrap m-0">"""
dynamic_filter_form = """<form class="d-flex align-items-center gap-2 flex-wrap m-0">\n        <input type="hidden" name="branch_id" value="{{ $currentBranchId ?? 'all' }}">"""

content = content.replace(static_filter_form, dynamic_filter_form)

# Add branch_id to the Quick Links (วันนี้, 7 วัน, 30 วัน)
link1 = """<a href="{{ route('dashboard', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold">วันนี้</a>"""
new_link1 = """<a href="{{ route('dashboard', ['from' => now()->toDateString(), 'to' => now()->toDateString(), 'branch_id' => $currentBranchId ?? 'all']) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold">วันนี้</a>"""
content = content.replace(link1, new_link1)

link2 = """<a href="{{ route('dashboard', ['from' => now()->subDays(6)->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold">7 วัน</a>"""
new_link2 = """<a href="{{ route('dashboard', ['from' => now()->subDays(6)->toDateString(), 'to' => now()->toDateString(), 'branch_id' => $currentBranchId ?? 'all']) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold">7 วัน</a>"""
content = content.replace(link2, new_link2)

link3 = """<a href="{{ route('dashboard', ['from' => now()->subDays(29)->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold">30 วัน</a>"""
new_link3 = """<a href="{{ route('dashboard', ['from' => now()->subDays(29)->toDateString(), 'to' => now()->toDateString(), 'branch_id' => $currentBranchId ?? 'all']) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold">30 วัน</a>"""
content = content.replace(link3, new_link3)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated dashboard.blade.php dropdowns")
