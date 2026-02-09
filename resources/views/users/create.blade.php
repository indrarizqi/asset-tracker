<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah User Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block font-bold mb-1">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold mb-1">Email</label>
                        <input type="email" name="email" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold mb-1">Password</label>
                            <input type="password" name="password" class="w-full border rounded p-2" required>
                        </div>
                        <div>
                            <label class="block font-bold mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block font-bold mb-1">Role / Jabatan</label>
                        <select name="role" class="w-full border rounded p-2">
                            <option value="admin">Admin / Staff (Bisa Scan & Input)</option>
                            <option value="super_admin">Super Admin (Full Akses)</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-bold">Simpan User</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>