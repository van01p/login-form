<label>Nama</label>
<input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>

<label>Kategori</label>
<select name="category_id" required>
    @foreach ($categories as $c)
        <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id ?? '') == $c->id)>
            {{ $c->name }}
        </option>
    @endforeach
</select>

<label>Deskripsi</label>
<textarea name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>

<label>Harga</label>
<input type="number" name="price" min="0" step="0.01" value="{{ old('price', $product->price ?? '') }}" required>

<label>Stok</label>
<input type="number" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}" required>

<label>Gambar</label>
<input type="file" name="image" accept="image/*">
@if (!empty($product->image))
    <img class="thumb" style="margin-top:8px" src="{{ asset('storage/'.$product->image) }}">
@endif

<br><br>
<button class="btn">Simpan</button>
<a class="btn btn-light" href="{{ route('products.index') }}">Batal</a>