@extends('app')

@section('title', '商品登録')

@section('content')
<div class="form-wrapper">
    <h1>商品登録</h1>

    <form action="{{ route('products.confirm') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-row">
        <label>商品名</label>
        <input type="text" name="name" value="{{ old('name') }}">
    </div>
    @error('name')
        <div class="required">
            ※{{ $message }}
        </div>
    @enderror

    <div class="form-row">
        <label>商品カテゴリ</label>
        <select name="product_category_id" id="category">
            <option value="">選択してください</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ (old('product_category_id', request('product_category_id')) == $category->id) ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
        <select name="product_subcategory_id" id="subcategory">
            <option value="">選択してください</option>
        </select>
    </div>
    @error('product_category_id')
        <div class="required">
            ※{{ $message }}
        </div>
    @enderror
    @error('product_subcategory_id')
        <div class="required">
            ※{{ $message }}
        </div>
    @enderror

    <div class="form-row">
        <label>商品写真</label>
        <div class="product-images">
            @php
                $old      = old('imagePaths', []);
                $session  = session('tmp_image_paths', []);
                $oldImagePaths = [];

                for ($i = 0; $i < 4; $i++) {
                    $v = $old[$i] ?? null;
                    if (empty($v)) {
                        $v = $session[$i] ?? '';
                    }
                    $oldImagePaths[$i] = $v;
                }
            @endphp

            @for ($i = 0; $i < 4; $i++)
                @php $oldPath = $oldImagePaths[$i] ?? ''; @endphp

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
                    src="{{ $oldPath ? Storage::url($oldPath) : '' }}"
                    alt="プレビュー"
                    style="{{ $oldPath ? '' : 'display:none;' }} max-width:150px; max-height:150px; margin-right:10px;"
                    >

                    <input type="hidden" name="imagePaths[{{ $i }}]" value="{{ $oldPath }}">

                    <button type="button" onclick="document.getElementById('image{{ $i }}').click()">
                    アップロード
                    </button>
                </div>
            @endfor
        </div>
    </div>
    @for ($i = 0; $i < 4; $i++)
        @php $msg = $errors->first("images.$i") ?: $errors->first("imagePaths.$i"); @endphp
        @if ($msg)
        <div class="required">画像{{ $i + 1 }}: {{ $msg }}</div>
        @endif
    @endfor

    <div class="form-row">
        <label>商品説明</label>
        <input type="text" name="product_content" value="{{ old('product_content') }}">
    </div>
    @error('product_content')
        <div class="required">
            ※{{ $message }}
        </div>
    @enderror

    <button type="submit" class="submit-button">確認画面へ</button>
    </form>

    @php
        $previousUrl = url()->previous();
        $listUrl = route('products.list');
        $topUrl  = url('/top');
    @endphp

    @if(str_starts_with($previousUrl, $listUrl))
        <form action="{{ $listUrl }}" method="GET">
            <button type="submit" class="submit-button-back">一覧へ戻る</button>
        </form>
    @else
        <form action="{{ $topUrl }}" method="GET">
            <button type="submit" class="submit-button-back">トップへ戻る</button>
        </form>
    @endif
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
        const input = event.target;
        const preview = document.getElementById('preview' + index);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'inline';
            }
            reader.readAsDataURL(input.files[0]);

            // hiddenフィールドを空にして「新しい画像を選んだ」ことを明示
            input.closest('div').querySelector('input[type=hidden]').value = '';
        }
    }
</script>
@endsection