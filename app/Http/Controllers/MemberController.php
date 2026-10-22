<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $typeId = $request->input('member_type_id');
        $branchId = $request->input('branch_id');
        $status = $request->input('status');

        $query = Member::with(['memberType', 'branch']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('member_code', 'ilike', "%{$q}%")
                    ->orWhere('name', 'ilike', "%{$q}%")
                    ->orWhere('phone', 'ilike', "%{$q}%");
            });
        }

        if ($typeId) {
            $query->where('member_type_id', $typeId);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $members = $query->orderBy('member_code')->paginate(25)->withQueryString();
        $memberTypes = MemberType::orderBy('code')->get();
        $branches = Branch::orderBy('code')->get();

        $stats = [
            'total' => Member::count(),
            'active' => Member::where('is_active', true)->count(),
            'inactive' => Member::where('is_active', false)->count(),
            'total_points' => Member::sum('points'),
        ];

        return view('members.index', compact('members', 'memberTypes', 'branches', 'stats', 'q', 'typeId', 'branchId', 'status'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateMember($request);
        Member::create($data);

        return redirect()->route('members.index')->with('success', "เพิ่มสมาชิก {$data['member_code']} แล้ว");
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $this->validateMember($request, $member->id);
        $member->update($data);

        return redirect()->route('members.index')->with('success', 'บันทึกสมาชิกแล้ว');
    }

    private function validateMember(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'member_code' => ['required', 'string', 'max:30', 'unique:members,member_code,'.($ignoreId ?? 'NULL').',id'],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'member_type_id' => ['nullable', 'integer', 'exists:member_types,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'points' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['points'] = $data['points'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
