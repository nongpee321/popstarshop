<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
echo Hash::check('123456', User::where('username', 'noname')->first()->password) ? 'YES' : 'NO';
