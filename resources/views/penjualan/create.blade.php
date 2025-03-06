@extends('layouts.layout')

@section('content')
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header">
            <h4 class="mb-0">Tambah Penjualan</h4>
        </div>
        <div class="card-body">
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('penjualan.create') }}">
                <div class="input-group mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>            

            <form action="{{ route('penjualan.store') }}" method="POST" onsubmit="updateCart()">
                @csrf

                <div class="mb-4">
                    <label for="member_id" class="form-label fw-bold">Pilih Member (Opsional)</label>
                    <select name="member_id" id="member_id" class="form-select">
                        <option value="">Umum</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label fw-bold">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <h5 class="mb-3 fw-bold">Pilih Produk</h5>
                <!-- Daftar Produk -->
                <div class="row" id="produk-container">
                    @foreach($produk as $p)
                        @php
                            $stok_total = $p->penerimaanBarang->sum('qty');
                            $harga_jual = $p->penerimaanBarang->first()->harga_jual ?? 0;
                        @endphp

                        @if($stok_total > 0) <!-- Hanya tampilkan produk yang masih memiliki stok -->
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                                <div class="card h-100 d-flex flex-column">
                                    <div class="card-body d-flex flex-column">
                                        <!-- Gambar -->
                                        <div class="bg-label-primary rounded-3 mb-3 text-center p-3">
                                            <img class="img-fluid" src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_barang }}" style="width: 50%; height: 120px; object-fit: cover;" />
                                        </div>

                                        <!-- Nama Barang -->
                                        <h6 class="mb-2">{{ $p->nama_barang }}</h6>

                                        <!-- Harga & Stok -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <small class="text-muted">Harga</small>
                                                <h6 class="mb-0 fw-bold">Rp {{ number_format($harga_jual, 0, ',', '.') }}</h6>
                                            </div>
                                            <div>
                                                <small class="text-muted">Stok</small>
                                                <h6 class="mb-0"><span id="stok-{{ $p->id }}">{{ $stok_total }}</span></h6>
                                            </div>
                                        </div>

                                        <!-- Input Quantity -->
                                        <input type="number" class="form-control text-center mb-3" 
                                            id="qty-{{ $p->id }}" 
                                            min="1"
                                            max="{{ $stok_total ?? 1 }}"
                                            value="1"> <!-- Default ke 1 -->

                                        <!-- Tombol -->
                                        <button type="button" class="btn btn-primary w-100 mt-auto" onclick="tambahKeCart({{ $p->id }}, '{{ $p->nama_barang }}', {{ $harga_jual }}, {{ $stok_total }})">
                                            Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $produk->appends(['search' => request('search')])->links(('pagination::bootstrap-4')) }}
                </div>

                <h5 class="mt-4 fw-bold">Keranjang</h5>
                <div class="table-responsive mb-5">
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

                <div class="card shadow-sm border p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-muted">Total Bayar:</span>
                        <h4 id="totalBayar" class="mb-0 text-primary fw-bold">Rp 0</h4>
                    </div>
                </div>                

                {{-- <button type="submit" class="btn btn-primary w-100 mt-5">Simpan Penjualan</button> --}}
                <button type="submit" class="btn btn-primary w-100 mt-5" id="submit-btn" disabled>Simpan Penjualan</button>

            </form>
        </div>
    </div>

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
    let qtyInput = document.getElementById(`qty-${id}`);
    let qty = parseInt(qtyInput.value) || 1;

    let stokElement = document.getElementById(`stok-${id}`);
    let stokTersedia = parseInt(stokElement.innerText);

    if (qty > stokTersedia) {
        alert('Jumlah melebihi stok yang tersedia!');
        return;
    }

    let existingItem = cart.find(item => item.id === id);
    if (existingItem) {
        existingItem.qty += qty;
    } else {
        cart.push({ id, nama, harga, qty });
    }

    stokElement.innerText = stokTersedia - qty;
    renderCart();
    saveCartToStorage();
}

function renderCart() {
    let cartContainer = document.getElementById('cart-container');
    let totalBayar = 0;
    let submitBtn = document.getElementById("submit-btn");

    cartContainer.innerHTML = '';

    if (cart.length === 0) {
        cartContainer.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Belum ada barang di keranjang</td></tr>`;
        submitBtn.disabled = true;
    } else {
        cart.forEach((item, index) => {
            let totalHarga = item.harga * item.qty;
            totalBayar += totalHarga;

            cartContainer.innerHTML += `
                <tr>
                    <td>${item.nama}</td>
                    <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="ubahQtyCart(${index}, ${item.id}, -1)">-</button>
                        <span class="mx-2">${item.qty}</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="ubahQtyCart(${index}, ${item.id}, 1)">+</button>
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
    cart.splice(index, 1);

    let stokElement = document.getElementById(`stok-${id}`);
    if (stokElement) {
        stokElement.innerText = parseInt(stokElement.innerText) + qty;
    }

    renderCart();
    saveCartToStorage();

    // Cek apakah keranjang kosong
    if (cart.length === 0) {
        document.getElementById('totalBayar').innerText = 'Rp 0';
        document.querySelector('button[type="submit"]').disabled = true; // Disable tombol simpan
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

function ubahQtyCart(index, id, perubahan) {
    let stokElement = document.getElementById(`stok-${id}`);
    let stokTersedia = parseInt(stokElement.innerText);

    if (cart[index]) {
        let newQty = cart[index].qty + perubahan;

        // Jika jumlah menjadi 0, hapus barang
        if (newQty < 1) {
            hapusDariCart(index, id, cart[index].qty);
            return;
        }

        // Periksa stok sebelum menambah qty
        if (perubahan > 0 && stokTersedia <= 0) {
            alert("Stok habis!");
            return;
        }

        // Update stok
        stokElement.innerText = stokTersedia - perubahan;

        // Update qty di keranjang
        cart[index].qty = newQty;

        // Render ulang tampilan keranjang
        renderCart();
        saveCartToStorage();
    }
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


function ubahQty(id, jumlah) {
  let input = document.getElementById(`qty-${id}`);
  if (!input) {
    console.error(`Input qty-${id} tidak ditemukan`);
    return;
  }

  let stok = parseInt(input.max);
  let qty = parseInt(input.value) + jumlah;

  console.log(`ID: ${id}, Stok: ${stok}, Qty: ${qty}`); // Debugging

  if (isNaN(stok)) {
    console.error("Stok tidak valid!");
    return;
  }

  if (qty < 1) qty = 1;
  if (qty > stok) qty = stok;

  input.value = qty;
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

document.addEventListener('DOMContentLoaded', function () {
    loadCartFromStorage();
});

</script>
@endsection