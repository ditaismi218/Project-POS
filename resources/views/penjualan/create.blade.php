@extends('layouts.layout')

@section('content')
    <div class="row">
        <!-- KIRI: Form Pencarian & Produk -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header">
                    <h4 class="mb-0">Tambah Penjualan</h4>
                </div>
                <div class="card-body">

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Pencarian -->
                    <form method="GET" action="{{ route('penjualan.create') }}">
                        <div class="input-group mb-3">
                            <input type="text" name="search" class="form-control" placeholder="Cari produk..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </form>

                    <!-- Input Barcode Scanner -->
                    <form onsubmit="handleScanBarcode(event)">
                        <div class="input-group mb-3">
                            <input type="text" id="barcodeInput" class="form-control"
                                placeholder="Scan barcode produk..." autofocus autocomplete="off">
                        </div>
                    </form>

                    <!-- Daftar Produk -->
                    <h5 class="mb-3 fw-bold">Pilih Produk</h5>

                    @if ($produk->count() > 0)
                        <div class="row g-3" id="produk-container">
                            @foreach ($produk as $p)
                                @php
                                    $stok_total = $p->penerimaanBarang->sum('qty');
                                    // $stok_total = $p->stok;
                                    $harga_jual =
                                        \App\Models\PenerimaanBarang::where('produk_id', $p->id)
                                            ->where('qty', '>', 0)
                                            ->orderBy('tgl_masuk', 'desc')
                                            ->orderBy('id', 'desc')
                                            ->first()->harga_jual ?? 0;
                                @endphp

                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="card h-100 shadow-sm border-0 rounded">
                                        <div class="ratio ratio-1x1 bg-light rounded-top">
                                            <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_barang }}"
                                                class="img-fluid rounded-top object-fit-cover">
                                        </div>

                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 fw-bold text-dark">{{ $p->nama_barang }}</h6>
                                                <small class="text-muted">{{ $p->kategori->nama_kategori ?? '-' }}</small>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <small class="text-muted">Harga</small>
                                                    <h6 class="mb-0 fw-bold text-primary">
                                                        Rp {{ number_format($harga_jual, 0, ',', '.') }}
                                                    </h6>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Stok</small>
                                                    <h6 class="mb-0"><span
                                                            id="stok-{{ $p->id }}">{{ $stok_total }}</span></h6>
                                                </div>
                                            </div>

                                            <input type="number" class="form-control text-center mb-3 border-0 shadow-sm"
                                                id="qty-{{ $p->id }}" min="1" max="{{ $stok_total ?? 1 }}"
                                                value="1">

                                            <button type="button" class="btn btn-primary w-100 mt-auto shadow-sm"
                                                onclick="tambahKeCart({{ $p->id }}, '{{ $p->nama_barang }}', {{ $harga_jual }}, {{ $stok_total }})">
                                                <i class="fas fa-cart-plus me-2"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $produk->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <p class="text-center text-muted">Tidak ada produk dengan stok tersedia.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- KANAN: Pilih Member & Keranjang (Scrollable) -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <form action="{{ route('penjualan.store') }}" method="POST" onsubmit="updateCart()">
                        @csrf

                        <!-- Pilih Member -->
                        <div class="mb-4">
                            <label for="member_id" class="form-label fw-bold">Pilih Member (Opsional)</label>
                            <select name="member_id" id="member_id" class="form-select">
                                <option value="">Umum</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Keranjang Belanja -->
                        <h5 class="fw-bold">Keranjang</h5>
                        <div class="table-responsive mb-4" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-container">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada barang di keranjang</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Bayar -->
                        <div class="card shadow-sm border p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold text-muted">Total Bayar:</span>
                                <h4 id="totalBayar" class="mb-0 text-primary fw-bold">Rp 0</h4>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="submit-btn" disabled>Simpan
                            Penjualan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const produkList = @json($dataProduk);
        console.log('Data Produk Lengkap:', produkList); // Periksa di console browser

        function handleScanBarcode(event) {
            event.preventDefault();
            const barcodeInput = document.getElementById('barcodeInput');
            const kode = barcodeInput.value.trim().toLowerCase();

            if (!kode) return;

            // Cari produk dengan kode yang sesuai
            const produk = produkList.find(p =>
                p.kode_barang.toLowerCase() === kode
            );

            if (produk) {
                // Gunakan data yang sudah diproses dari controller
                tambahKeCart(
                    produk.id,
                    produk.nama_barang,
                    produk.harga_jual,
                    produk.stok
                );

                barcodeInput.value = '';
                barcodeInput.focus();
            } else {
                alert('Produk dengan barcode tersebut tidak ditemukan.');
                barcodeInput.value = '';
                barcodeInput.focus();
            }
        }

        // Pastikan event Enter ditangkap juga
        const barcodeInput = document.getElementById('barcodeInput');
        barcodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                handleScanBarcode(e);
            }
        });
    </script>

    <script>
        let cart = [];

        function saveCartToStorage() {
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        function loadCartFromStorage() {
            let storedCart = localStorage.getItem('cart');
            if (storedCart) {
                cart = JSON.parse(storedCart);
                console.log("Loaded Cart:", cart); // Debugging
                renderCart();
            }
        }

        function tambahKeCart(id, nama, harga, stok) {
            // Cari elemen stok yang benar
            let stokElement = document.getElementById(`stok-${id}`);
            let stokTersedia = stokElement ? parseInt(stokElement.innerText) : stok;

            // Cek apakah item sudah ada di cart
            let existingItem = cart.find(item => item.id === id);
            let qtyToAdd = 1; // Default quantity

            // Validasi stok
            if ((existingItem ? existingItem.qty + qtyToAdd : qtyToAdd) > stokTersedia) {
                alert('Jumlah melebihi stok yang tersedia!');
                return;
            }

            // Update atau tambah item ke cart
            if (existingItem) {
                existingItem.qty += qtyToAdd;
            } else {
                cart.push({
                    id,
                    nama,
                    harga,
                    qty: qtyToAdd
                });
            }

            // Update tampilan stok jika elemen ditemukan
            if (stokElement) {
                stokElement.innerText = stokTersedia - qtyToAdd;
            }

            renderCart();
            saveCartToStorage();
        }

        function renderCart() {
            let cartContainer = document.getElementById('cart-container');
            let totalBayar = 0;
            let submitBtn = document.getElementById("submit-btn");

            cartContainer.innerHTML = '';

            if (cart.length === 0) {
                cartContainer.innerHTML =
                    `<tr><td colspan="5" class="text-center text-muted">Belum ada barang di keranjang</td></tr>`;
                submitBtn.disabled = true;
            } else {
                cart.forEach((item, index) => {
                    let totalHarga = item.harga * item.qty;
                    totalBayar += totalHarga;

                    // Tambahkan class 'disabled' jika qty = 1
                    const minusDisabled = item.qty === 1 ? 'disabled' : '';

                    cartContainer.innerHTML += `
                <tr>
                    <td>${item.nama}</td>
                    <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary ${minusDisabled}" 
                                onclick="ubahQtyCart(${index}, ${item.id}, -1)" 
                                ${minusDisabled}>
                            -
                        </button>
                        <span class="mx-2">${item.qty}</span>
                        <button class="btn btn-sm btn-outline-primary" 
                                onclick="ubahQtyCart(${index}, ${item.id}, 1)">
                            +
                        </button>
                    </td>
                    <td>Rp ${totalHarga.toLocaleString('id-ID')}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="hapusDariCart(${index}, ${item.id}, ${item.qty})">Hapus</button></td>
                </tr>
            `;
                });
                submitBtn.disabled = false;
            }

            document.getElementById('totalBayar').innerText = 'Rp ' + totalBayar.toLocaleString('id-ID');
        }


        function hapusDariCart(index, id, qty) {
            let stokElement = document.getElementById(`stok-${id}`);
            if (stokElement) {
                let currentStok = parseInt(stokElement.innerText);
                stokElement.innerText = currentStok + qty;
            }

            cart.splice(index, 1);
            renderCart();
            saveCartToStorage();

            if (cart.length === 0) {
                document.getElementById('totalBayar').innerText = 'Rp 0';
                document.getElementById('submit-btn').disabled = true;
            }
        }

        function updateCart(event) {
            if (cart.length === 0) {
                alert("Keranjang masih kosong! Tambahkan produk terlebih dahulu.");
                event.preventDefault();
                return false;
            }

            let cartContainer = document.getElementById('cart-container');
            let form = document.querySelector('form[action="{{ route('penjualan.store') }}"]');

            // Hapus input sebelumnya agar tidak duplikat
            document.querySelectorAll('input[name^="cart["]').forEach(input => input.remove());

            cart.forEach((item, index) => {
                let inputProduk = document.createElement("input");
                inputProduk.type = "hidden";
                inputProduk.name = `cart[${index}][produk_id]`;
                inputProduk.value = item.id;

                let inputQty = document.createElement("input");
                inputQty.type = "hidden";
                inputQty.name = `cart[${index}][qty]`;
                inputQty.value = item.qty;

                form.appendChild(inputProduk);
                form.appendChild(inputQty);
            });

            // Hapus keranjang dari localStorage setelah submit form
            localStorage.removeItem("cart");
        }

        function hapusItemCart(index, id) {
            let stokElement = document.getElementById(`stok-${id}`);
            let stokTersedia = parseInt(stokElement.innerText);

            // Kembalikan stok ke jumlah sebelum item dihapus
            stokElement.innerText = stokTersedia + cart[index].qty;

            // Hapus item dari array cart
            cart.splice(index, 1);

            // Render ulang tampilan keranjang
            renderCart();
        }

        function ubahQtyCart(index, id, perubahan) {
            let stokElement = document.getElementById(`stok-${id}`);
            if (!stokElement) {
                console.error(`Element stok-${id} tidak ditemukan`);
                return;
            }

            let stokTersedia = parseInt(stokElement.innerText);
            let item = cart[index];

            if (!item) return;

            // Jika mencoba mengurangi dan qty sudah 1, tidak perlu melakukan apa-apa
            if (perubahan < 0 && item.qty === 1) {
                return;
            }

            let newQty = item.qty + perubahan;

            // Jika jumlah menjadi 0, hapus barang
            if (newQty < 1) {
                hapusDariCart(index, id, item.qty);
                return;
            }

            // Validasi stok untuk penambahan
            if (perubahan > 0 && stokTersedia < perubahan) {
                alert("Stok tidak mencukupi!");
                return;
            }

            // Update stok di tampilan
            stokElement.innerText = stokTersedia - perubahan;

            // Update qty di keranjang
            item.qty = newQty;

            renderCart();
            saveCartToStorage();
        }

        function cekQty(id) {
            let input = document.getElementById(`qty-${id}`);
            let stok = parseInt(input.max);
            let qty = parseInt(input.value);

            console.log(`Cek Qty - ID: ${id}, Stok: ${stok}, Qty: ${qty}`); // Debugging

            if (isNaN(stok) || stok < 1) {
                console.error(`Stok untuk ID ${id} tidak valid!`);
                return;
            }

            if (qty < 1) input.value = 1;
            if (qty > stok) input.value = stok;
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadCartFromStorage();
        });
    </script>
@endsection
