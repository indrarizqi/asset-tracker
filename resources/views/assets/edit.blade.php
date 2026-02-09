<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Aset: {{ $asset->asset_tag }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('assets.update', $asset->id) }}" method="POST">
                    @csrf
                    @method('PUT') <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Aset</label>
                        <input type="text" name="name" value="{{ $asset->name }}" class="w-full border rounded py-2 px-3 text-gray-700">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Kategori Aset</label>
                        <select name="category" class="w-full border rounded py-2 px-3 text-gray-700">
                            <option value="mobile" {{ $asset->category == 'mobile' ? 'selected' : '' }}>Mobile Asset</option>
                            <option value="semi-mobile" {{ $asset->category == 'semi-mobile' ? 'selected' : '' }}>Semi-Mobile</option>
                            <option value="fixed" {{ $asset->category == 'fixed' ? 'selected' : '' }}>Fixed Asset</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Status</label>
                        <select name="status" class="w-full border rounded py-2 px-3 text-gray-700">
                            <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="in_use" {{ $asset->status == 'in_use' ? 'selected' : '' }}>In Use</option>
                            <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="broken" {{ $asset->status == 'broken' ? 'selected' : '' }}>Broken</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded">Update Aset</button>
                    <a href="{{ route('dashboard') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal

                </form>

            </div>
        </div>
    </div>
</x-app-layout>