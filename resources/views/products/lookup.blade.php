@extends('layout')

@section('title', 'ค้นหารายละเอียดสินค้า - PopCentral')
@section('page-title', 'รายละเอียด / แก้ไขสินค้า')
@section('page-subtitle', 'สแกนบาร์โค้ด หรือพิมพ์รหัสสินค้าเพื่อดูข้อมูล')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6 text-center">
        <i class="bi bi-upc-scan text-muted" style="font-size: 5rem;"></i>
        <h4 class="mt-3 mb-4">ค้นหาสินค้าเพื่อดูรายละเอียดหรือแก้ไข</h4>
        
        <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control form-control-lg text-center" placeholder="พิมพ์ รหัสสินค้า (SKU), บาร์โค้ด หรือชื่อสินค้า..." autofocus autocomplete="off" style="border-radius: 30px;">
            <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 30px; padding-left: 25px; padding-right: 25px;">
                <i class="bi bi-search"></i>
            </button>
        </form>
        
        <div class="mt-4 text-muted small">
            หรือไปที่ <a href="{{ route('products.index') }}" class="text-decoration-none fw-bold">รายการสินค้าทั้งหมด</a> เพื่อเลือกจากตาราง
        </div>
    </div>
</div>
@endsection
