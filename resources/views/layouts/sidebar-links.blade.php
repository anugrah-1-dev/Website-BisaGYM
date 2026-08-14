<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    <i class="ph ph-squares-four text-xl mr-3"></i>
    {{ __('Dashboard') }}
</x-nav-link>

<div class="pt-4 pb-2">
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Management</p>
</div>

<x-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
    <i class="ph ph-users text-xl mr-3"></i>
    {{ __('Members') }}
</x-nav-link>

<x-nav-link :href="route('cashier.member')" :active="request()->routeIs('cashier.*')">
    <i class="ph ph-cash-register text-xl mr-3"></i>
    {{ __('Kasir Member') }}
</x-nav-link>

<x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')">
    <i class="ph ph-barcode text-xl mr-3"></i>
    {{ __('Scan Attendance') }}
</x-nav-link>

<x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
    <i class="ph ph-storefront text-xl mr-3"></i>
    {{ __('POS Snack') }}
</x-nav-link>

@hasanyrole('admin|developer|kasir|penjaga')
<x-nav-link :href="route('snacks.index')" :active="request()->routeIs('snacks.*')">
    <i class="ph ph-package text-xl mr-3"></i>
    {{ __('Inventaris Snack') }}
</x-nav-link>
@endhasanyrole

@php
    $isEmployee = \App\Models\Employee::where('user_id', Auth::id())->exists();
@endphp

@if($isEmployee)
<x-nav-link :href="route('employee.my-info')" :active="request()->routeIs('employee.my-info')">
    <i class="ph ph-briefcase text-xl mr-3"></i>
    {{ __('Informasi Pekerjaan') }}
</x-nav-link>
@endif

<div class="pt-4 pb-2">
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</p>
</div>

<x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
    <i class="ph ph-chart-line-up text-xl mr-3"></i>
    {{ __('Transactions') }}
</x-nav-link>

@hasanyrole('admin|developer|kasir|penjaga')
<x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
    <i class="ph ph-shopping-cart-open text-xl mr-3"></i>
    {{ __('Pengeluaran Operasional') }}
</x-nav-link>

<x-nav-link :href="route('financial-report.index')" :active="request()->routeIs('financial-report.*')">
    <i class="ph ph-wallet text-xl mr-3"></i>
    {{ __('Laporan Keuangan') }}
</x-nav-link>

<x-nav-link :href="route('shift-reports.index')" :active="request()->routeIs('shift-reports.*')">
    <i class="ph ph-clock-afternoon text-xl mr-3"></i>
    {{ __('Laporan Shift Kasir') }}
</x-nav-link>
@endhasanyrole

@role('developer')
<div class="pt-4 pb-2">
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Developer System</p>
</div>

<x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
    <i class="ph ph-users-three text-xl mr-3"></i>
    {{ __('Manajemen User Login') }}
</x-nav-link>

<x-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.*')">
    <i class="ph ph-clock-counter-clockwise text-xl mr-3"></i>
    {{ __('Riwayat Aktivitas') }}
</x-nav-link>
@endrole

@hasanyrole('admin|developer')
<div class="pt-4 pb-2">
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin Settings</p>
</div>

<x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
    <i class="ph ph-identification-badge text-xl mr-3"></i>
    {{ __('Manajemen Karyawan') }}
</x-nav-link>

<x-nav-link :href="route('employee-attendances.index')" :active="request()->routeIs('employee-attendances.*')">
    <i class="ph ph-calendar-check text-xl mr-3"></i>
    {{ __('Absensi Karyawan') }}
</x-nav-link>

<x-nav-link :href="route('gym-packages.index')" :active="request()->routeIs('gym-packages.*')">
    <i class="ph ph-tags text-xl mr-3"></i>
    {{ __('Paket & Harga') }}
</x-nav-link>

<x-nav-link :href="route('discounts.index')" :active="request()->routeIs('discounts.*')">
    <i class="ph ph-percent text-xl mr-3"></i>
    {{ __('Manajemen Diskon') }}
</x-nav-link>
@endhasanyrole
