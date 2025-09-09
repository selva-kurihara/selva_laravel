@extends('admin')

@section('title', isset($product->id) ? '商品編集' : '商品登録')

@section('header')
    <div class="header-container">
        <div class="header-left">{{ isset($product->id) ? '商品編集' : '商品登録' }}</div>
        <div class="header-right">
            <form action="{{ route('admin.products.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="form-wrapper">
  <h1>{{ isset($product->id) ? '商品編集' : '商品登録' }}</h1>

    <form action="{{ route('admin.products.confirm') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($product->id))
            <input type="hidden" name="id" value="{{ $product->id }}">
    @endif

    <div class="form-row">
        <label>商品ID</label>
        <div class="name-inputs">
            @if(isset($product->id))
                <span>{{ $product->id }}</span>
            @else
                <span>登録後に自動採番</span>
            @endif
        </div>
    </div>

    <div class="form-row">
        <label>会員</label>
        <select name="member_id">
            <option value="">選択してください</option>
            @foreach($members as $member)
                <option value="{{ $member->id }}"
                    {{ old('member_id', $product->member_id ?? '') == $member->id ? 'selected' : '' }}>
                    {{ $member->name_sei }}{{ $member->name_mei }}
                </option>
            @endforeach
        </select>
    </div>
    @error('member_id')
        <div class="error">
            ※{{ $message }}
        </div>
    @enderror

    <div class="form-row">
        <label>商品名</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}">
    </div>
    @error('name')
        <div class="error">
            ※{{ $message }}
        </div>
    @enderror

    <div class="form-row">
      <label>商品カテゴリ</label>
      <select name="product_category_id" id="category">
          <option value="">選択してください</option>
          @foreach($categories as $cat)
              <option value="{{ $cat->id }}" 
                  {{ old('product_category_id', $product->category->id ?? '') == $cat->id ? 'selected' : '' }}>
                  {{ $cat->name }}
              </option>
          @endforeach
      </select>

      <select name="product_subcategory_id" id="subcategory">
          <option value="">選択してください</option>
          @if(isset($product->category))
              @foreach($product->category->subCategories ?? [] as $sub)
                  <option value="{{ $sub->id }}" 
                      {{ old('product_subcategory_id', $product->product_subcategory_id ?? '') == $sub->id ? 'selected' : '' }}>
                      {{ $sub->name }}
                  </option>
              @endforeach
          @endif
      </select>
    </div>
    @error('product_category_id')
        <div class="error">
            ※{{ $message }}
        </div>
    @enderror
    @error('product_subcategory_id')
        <div class="error">
            ※{{ $message }}
        </div>
    @enderror

    <div class="form-row">
        <label>商品写真</label>
        <div class="product-images">
            @php
                $old      = old('imagePaths', []);
                $session  = session('tmp_image_paths', []);
                $initial  = $initialImagePaths ?? [];
                $imagePaths = [];

                for ($i = 0; $i < 4; $i++) {
                    $v = $old[$i] ?? null;
                    if (empty($v)) $v = $session[$i] ?? null;
                    if (empty($v)) $v = $initial[$i] ?? '';
                    $imagePaths[$i] = $v;
                }

                function buildImgSrc($path) {
                    if (!$path) return '';
                    if (preg_match('#^https?://|^/storage/#', $path)) {
                        return $path;
                    }
                    return \Illuminate\Support\Facades\Storage::url($path);
                }
            @endphp

            @for ($i = 0; $i < 4; $i++)
                @php
                    $path = $imagePaths[$i] ?? '';
                    $src  = $path ? buildImgSrc($path) : '';
                @endphp

                <div class="image-slot">
                    <input
                        type="file"
                        name="images[{{ $i }}]"
                        accept="image/*"
                        id="image{{ $i }}"
                        style="display:none;"
                        onchange="previewImage(event, {{ $i }})"
                    >

                    <img
                        id="preview{{ $i }}"
                        src="{{ $src }}"
                        alt="プレビュー"
                        style="{{ $src ? '' : 'display:none;' }} max-width:150px; max-height:150px; margin-right:10px;"
                    >

                    <input type="hidden" name="imagePaths[{{ $i }}]" value="{{ $path }}">

                    <button type="button" onclick="document.getElementById('image{{ $i }}').click()">
                        アップロード
                    </button>
                </div>
            @endfor
        </div>
    </div>
    @for ($i = 0; $i < 4; $i++)
        <span class="file-error" aria-live="polite"></span>
    @endfor

    @for ($i = 0; $i < 4; $i++)
        @php $msg = $errors->first("images.$i") ?: $errors->first("imagePaths.$i"); @endphp
        @if ($msg)
        <div class="error">画像{{ $i + 1 }}: {{ $msg }}</div>
        @endif
    @endfor

    <div class="form-row">
        <label>商品説明</label>
        <textarea name="product_content" rows="4">{{ old('product_content', $product->product_content ?? '') }}</textarea>
    </div>
    @error('product_content')
        <div class="error">
            ※{{ $message }}
        </div>
    @enderror

    <button type="submit" class="submit-button">確認画面へ</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function loadSubcategories(selectedSubcategoryId = null) {
        const categoryId = $('#category').val();
        const $sub = $('#subcategory');

        $sub.empty().append('<option value="">選択してください</option>');

        if (categoryId) {
            $.getJSON(`/subcategories/${categoryId}`, function(data) {
                $.each(data, function(_, sc) {
                    $sub.append(new Option(sc.name, sc.id));
                });

                if (selectedSubcategoryId) {
                    $sub.val(String(selectedSubcategoryId));
                }
            });
        }
    }

    $('#category').on('change', function() {
        loadSubcategories();
    });

    $(document).ready(function() {
        const oldCategoryId = "{{ old('product_category_id', request('product_category_id')) }}";
        const oldSubcategoryId = "{{ old('product_subcategory_id', request('product_subcategory_id')) }}";

        if (oldCategoryId) {
            $('#category').val(oldCategoryId);
            loadSubcategories(oldSubcategoryId);
        }
    });

    function previewImage(event, index) {
        const input   = event.target;
        const slot    = input.closest('.image-slot');
        const preview = slot.querySelector('#preview' + index);
        const errorEl = document.querySelectorAll('.file-error')[index];

        // 初期化
        if (errorEl) errorEl.textContent = '';

        const file = input.files && input.files[0];
        if (!file) {
            preview.removeAttribute('src');
            preview.style.display = 'none';
            return;
        }

        const allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        const maxSize = 10 * 1024 * 1024; // 10MB

        if (!allowed.includes(file.type)) {
            if (errorEl) errorEl.textContent = '画像ファイル（JPEG/PNG/GIF/WebP）を選択してください。';
            input.value = '';
            preview.removeAttribute('src');
            preview.style.display = 'none';
            return;
        }
        if (file.size > maxSize) {
            if (errorEl) errorEl.textContent = 'ファイルサイズは10MB以下にしてください。';
            input.value = '';
            preview.removeAttribute('src');
            preview.style.display = 'none';
            return;
        }

        // バリデーションOKになってから hidden を空にする
        const hidden = slot.querySelector('input[type=hidden]');
        if (hidden) hidden.value = '';

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'inline';
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection