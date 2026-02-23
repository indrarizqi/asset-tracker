<x-app-layout>
    <x-slot name="title">Edit Asset</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Aset : ') }} <span class="text-indigo-600">{{ $asset->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-8 bg-white">
                    
                    <form action="{{ route('assets.update', $asset->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-5">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Asset Name*</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $asset->name) }}" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 shadow-sm"
                                placeholder="Masukkan nama aset">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-5">
                            <label for="person_in_charge" class="block text-sm font-semibold text-gray-700 mb-1">Penanggung Jawab*</label>
                            <input type="text" name="person_in_charge" id="person_in_charge" value="{{ old('person_in_charge', $asset->person_in_charge) }}" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 shadow-sm"
                                placeholder="Kosongkan jika tidak ada">
                            @error('person_in_charge') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label for="category" class="block text-sm font-semibold text-gray-700 mb-1">Kategori Aset*</label>
                                <select name="category" id="category" required
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 cursor-pointer shadow-sm">
                                    <option value="mobile" {{ (old('category', $asset->category) == 'mobile') ? 'selected' : '' }}>Mobile Asset</option>
                                    <option value="semi-mobile" {{ (old('category', $asset->category) == 'semi-mobile') ? 'selected' : '' }}>Semi-Mobile</option>
                                    <option value="fixed" {{ (old('category', $asset->category) == 'fixed') ? 'selected' : '' }}>Fixed Asset</option>
                                </select>
                                @error('category') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status*</label>
                                <select name="status" id="status" required
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 cursor-pointer shadow-sm">
                                    <option value="in_use" {{ (old('status', $asset->status) == 'in_use') ? 'selected' : '' }}>In Use</option>
                                    <option value="not_used" {{ (old('status', $asset->status) == 'not_used') ? 'selected' : '' }}>Not Used</option>
                                    <option value="maintenance" {{ (old('status', $asset->status) == 'maintenance') ? 'selected' : '' }}>Maintenance</option>
                                    <option value="broken" {{ (old('status', $asset->status) == 'broken') ? 'selected' : '' }}>Broken</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="condition" class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Fisik*</label>
                            <select name="condition" id="condition" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 cursor-pointer shadow-sm">
                                <option value="Baik" {{ (old('condition', $asset->condition) == 'Baik') ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak" {{ (old('condition', $asset->condition) == 'Rusak') ? 'selected' : '' }}>Rusak</option>
                                <option value="Rusak Total" {{ (old('condition', $asset->condition) == 'Rusak Total') ? 'selected' : '' }}>Rusak Total</option>
                            </select>
                            @error('condition') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-5">
                            <label for="purchase_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pembelian*</label>
                            <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $asset->purchase_date) }}" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 shadow-sm">
                            @error('purchase_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-8">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 shadow-sm"
                                placeholder="Tambahkan deskripsi atau keterangan detail mengenai aset ini...">{{ old('description', $asset->description) }}</textarea>
                            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2 border-t border-gray-100 mt-6">
                            <button type="submit" 
                                class="inline-flex items-center px-6 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                Update Aset
                            </button>

                            <a href="{{ route('dashboard') }}" 
                                class="inline-flex items-center px-6 py-2.5 bg-gray-500 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>