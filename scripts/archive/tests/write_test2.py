with open(r'd:\pop-erp-food\ERPPOP-main\test_cashier.php', 'w', encoding='utf-8') as f:
    f.write('''<?php
require __DIR__.'/vendor/autoload.php';
app = require_once __DIR__.'/bootstrap/app.php';
app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

branchId = 6;
code = 'noname';
salesmen = App\Models\Salesman::query()
    ->with(['user.roles', 'user.branchRoles.permissions'])
    ->where('is_active', true)
    ->whereNotNull('user_id')
    ->where('code', code)
    ->whereHas('user', function (user) use (branchId) {
        user->where('is_active', true);
        user->where(function (access) use (branchId) {
            access->whereHas('branchRoles', function (role) use (branchId) {
                role->where('user_branch_roles.branch_id', branchId)
                     ->where('user_branch_roles.is_active', true)
                     ->whereHas('permissions', function (permission) {
                         permission->where('code', 'pos.sell');
                     });
            })
            ->orWhere(function (primary) use (branchId) {
                primary->whereDoesntHave('branchRoles', function (role) use (branchId) {
                    role->where('user_branch_roles.branch_id', branchId);
                })
                ->where('branch_id', branchId)
                ->whereHas('roles.permissions', function (permission) {
                    permission->where('code', 'pos.sell');
                });
            });
        });
    })->get();
echo "Count: " . count(salesmen) . "\\n";
'''.replace('app', '$'+'app').replace('branchId', '$'+'branchId').replace('code', '$'+'code').replace('salesmen', '$'+'salesmen').replace('user', '$'+'user').replace('access', '$'+'access').replace('role', '$'+'role').replace('permission', '$'+'permission').replace('primary', '$'+'primary'))
