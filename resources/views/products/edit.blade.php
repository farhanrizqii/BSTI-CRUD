@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-600 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">
                        Edit Product
                    </h2>
                    <p class="text-slate-600 text-sm mt-1">Perbarui informasi product Anda</p>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white/70 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')
                
                {{-- Nama Product --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nama Product
                        <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ $product->name }}" 
                        required
                        class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all outline-none placeholder:text-slate-400"
                        placeholder="Masukkan nama product..."
                    >
                </div>

                {{-- Deskripsi --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea 
                        name="description" 
                        rows="4"
                        class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all outline-none placeholder:text-slate-400 resize-none"
                        placeholder="Masukkan deskripsi product..."
                    >{{ $product->description }}</textarea>
                </div>

                {{-- Gambar Saat Ini --}}
                @if($product->image)
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                            Gambar Saat Ini
                        </label>
                        <div class="relative inline-block group">
                            <img 
                                src="{{ $product->image }}" 
                                class="w-48 h-48 object-cover rounded-xl border-2 border-slate-200 shadow-md"
                                alt="{{ $product->name }}"
                            >
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 rounded-xl transition-all"></div>
                        </div>
                    </div>
                @endif

                {{-- Upload Gambar Baru --}}
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Gambar Baru
                        <span class="text-slate-500 font-normal">(opsional)</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="file" 
                            name="image" 
                            accept="image/*"
                            id="imageInput"
                            class="hidden"
                            onchange="previewImage(event)"
                        >
                        <label for="imageInput" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-slate-300 rounded-xl hover:border-amber-500 transition-all cursor-pointer bg-slate-50 hover:bg-amber-50 group">
                            <div id="uploadPlaceholder" class="text-center">
                                <svg class="w-12 h-12 mx-auto text-slate-400 group-hover:text-amber-500 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-sm font-medium text-slate-600 group-hover:text-amber-600 transition-colors">
                                    Klik untuk upload gambar baru
                                </p>
                                <p class="text-xs text-slate-400 mt-1">PNG, JPG, JPEG (Max. 2MB)</p>
                            </div>
                            <div id="imagePreview" class="hidden w-full h-full p-2">
                                <img id="previewImg" class="w-full h-full object-contain rounded-lg" alt="Preview">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-6 border-t border-slate-200">
                    <button 
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Product
                    </button>
                    <a 
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl transition-all"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('uploadPlaceholder').classList.add('hidden');
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('previewImg').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>

@endsection