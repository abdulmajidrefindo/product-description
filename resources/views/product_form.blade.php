<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Input Produk - Stafbook Assessment</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6 font-sans text-gray-800 max-w-6xl mx-auto">

  <h1 class="text-2xl font-bold mb-4">Tambah Produk</h1>

  <form id="product-form" enctype="multipart/form-data" class="space-y-6">
    <div id="product-list" class="space-y-4"></div>

    <div class="flex items-center gap-4">
        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition" id="add-product">
            Tambah Produk
        </button>
        <span id="max-product-msg" class="text-red-600 hidden">Maksimal 5 produk.</span>
    </div>

    <button type="submit" class="mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
        Simpan Produk
    </button>
  </form>

  <hr class="my-10 border-gray-300">

  <h1 class="text-2xl font-bold mb-4">Produk Tersimpan</h1>

  <div class="overflow-x-auto border rounded bg-white shadow">
    <table class="min-w-full table-auto divide-y divide-gray-200" id="product-table">
        <thead class="bg-gray-100">
            <tr>
            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-[25%]">Nama Produk</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-[25%]">Kategori</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-[40%]">Gambar</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-[10%]">Aksi</th>
            </tr>
        </thead>
        <tbody id="product-table-body" class="bg-white divide-y divide-gray-200">
            <!-- Diisi oleh JS -->
        </tbody>
    </table>
  </div>

  <script>
    let productCount = 0;

    $(document).ready(function () {
      loadProducts();

      $('#add-product').click(function () {
        if (productCount >= 5) {
          $('#max-product-msg').show();
          return;
        }

        const index = productCount;
        const card = `
          <div class="border border-gray-300 p-4 rounded-md bg-white product-card" data-index="${index}">
            <div class="flex justify-between items-start mb-2">
              <input type="text" name="product_name[]" class="border rounded w-2/3 px-3 py-1" placeholder="Nama Produk">
              <button type="button" class="delete-product text-red-500 hover:underline">Hapus Produk</button>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-left border mt-2">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-3 py-2 border">Nama Kategori</th>
                    <th class="px-3 py-2 border">Gambar</th>
                    <th class="px-3 py-2 border w-12"></th>
                  </tr>
                </thead>
                <tbody class="category-list" data-product="${index}">
                  ${createCategoryRow(index)}
                </tbody>
              </table>
            </div>
            <div class="mt-2 text-left">
              <button type="button" class="add-category text-blue-600 hover:underline">+ Tambah Kategori</button>
              <span class="max-cat-msg text-red-500 text-sm hidden ml-2">Maksimal 3 kategori per produk.</span>
            </div>
          </div>
        `;
        $('#product-list').append(card);
        productCount++;
      });

      $(document).on('click', '.delete-product', function () {
        $(this).closest('.product-card').remove();
        productCount--;
        $('#max-product-msg').hide();
      });

      $(document).on('click', '.add-category', function () {
        const $card = $(this).closest('.product-card');
        const index = $card.data('index');
        const $tbody = $card.find('.category-list');
        const count = $tbody.children().length;

        if (count >= 3) {
          $card.find('.max-cat-msg').show();
          return;
        }

        $tbody.append(createCategoryRow(index));
      });

      $(document).on('click', '.delete-category', function () {
        $(this).closest('tr').remove();
      });

      $('#product-form').submit(function (e) {
        e.preventDefault();

        const formData = new FormData();
        let valid = true;

        $('.product-card').each(function (pIndex) {
          const productName = $(this).find('input[name="product_name[]"]').val();
          if (!productName) {
            alert("Nama produk harus diisi.");
            valid = false;
            return false;
          }

          formData.append(`products[${pIndex}][name]`, productName);

          $(this).find('tbody.category-list tr').each(function (cIndex) {
            const catName = $(this).find('input[name="category_name[]"]').val();
            const catImage = $(this).find('input[name="category_image[]"]')[0]?.files[0];

            if (!catName) {
              alert("Nama kategori harus diisi.");
              valid = false;
              return false;
            }

            formData.append(`products[${pIndex}][categories][${cIndex}][name]`, catName);
            if (catImage) {
              formData.append(`products[${pIndex}][categories][${cIndex}][image]`, catImage);
            }
          });
        });

        if (!valid) return;

        $.ajax({
          url: '/products',
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function () {
            alert("Produk berhasil disimpan!");
            $('#product-list').empty();
            productCount = 0;
            $('#max-product-msg').hide();
            loadProducts();
          },
          error: function (err) {
            alert("Gagal menyimpan produk");
            console.log(err);
          }
        });
      });

      $(document).on('click', '.delete-product-btn', function () {
        const id = $(this).data('id');
        if (confirm('Yakin ingin menghapus produk ini?')) {
          $.ajax({
            url: `/products/${id}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function () {
              loadProducts();
            },
            error: function (err) {
              console.error(err);
              alert('Gagal menghapus produk.');
            }
          });
        }
      });

      $(document).on('click', '.delete-category-btn', function () {
        const id = $(this).data('id');
        if (confirm('Yakin ingin menghapus kategori ini?')) {
          $.ajax({
            url: `/categories/${id}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function () {
              loadProducts();
            },
            error: function (err) {
              alert('Gagal menghapus kategori');
              console.log(err);
            }
          });
        }
      });
    });

    function createCategoryRow(index) {
      return `
        <tr>
          <td class="border px-3 py-2">
            <input type="text" name="category_name[]" class="border rounded px-2 py-1 w-full" placeholder="Nama Kategori">
          </td>
          <td class="border px-3 py-2">
            <input type="file" name="category_image[]" accept=".jpg,.jpeg,.png">
          </td>
          <td class="border px-3 py-2 text-center">
            <button type="button" class="delete-category text-red-500 hover:underline">🗑</button>
          </td>
        </tr>
      `;
    }

    function loadProducts() {
      $.get('/products/data', function (data) {
        const tbody = $('#product-table-body');
        tbody.empty();

        if (data.length === 0) {
          tbody.append(`
            <tr>
              <td colspan="4" class="text-center py-6 text-gray-500 italic">Belum ada produk yang disimpan.</td>
            </tr>
          `);
          return;
        }

        data.forEach(product => {
          const catCount = product.categories.length;

          product.categories.forEach((cat, i) => {
            const row = `
            <tr>
                ${i === 0 ? `<td class="px-4 py-2 align-top w-[25%]" rowspan="${catCount}">${product.name}</td>` : ''}
                <td class="px-4 py-2 w-[25%]">${cat.name}</td>
                <td class="px-4 py-2 w-[40%]">
                ${cat.image_path
                    ? `<img src="/storage/${cat.image_path}" alt="${cat.name}" class="w-90% h-auto rounded border">`
                    : '<span class="text-gray-400 italic">Tidak ada gambar</span>'}
                </td>
                <td class="px-4 py-2 w-[10%]">
                <button class="text-red-500 text-sm hover:underline delete-category-btn" data-id="${cat.id}">
                    Hapus
                </button>
                </td>
            </tr>
            `;
            tbody.append(row);
          });
        });
      });
    }
  </script>
</body>
</html>
