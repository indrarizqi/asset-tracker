<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block font-bold mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold mb-1">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4 bg-yellow-50 p-4 rounded border border-yellow-200">
                        <label class="block font-bold mb-1 text-yellow-800">Ganti Password (Opsional)</label>
                        <div class="text-xs text-gray-500 mb-2">Biarkan kosong jika tidak ingin mengganti password.</div>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="password" name="password" placeholder="Password Baru" class="w-full border rounded p-2">
                            <input type="password" name="password_confirmation" placeholder="Ulangi Password" class="w-full border rounded p-2">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block font-bold mb-1">Role / Jabatan</label>
                        <select name="role" class="w-full border rounded p-2">
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin / Staff</option>
                            <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-bold">Update User</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>