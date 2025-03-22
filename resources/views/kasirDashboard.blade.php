@extends('layouts.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="col-xxl-8 mb-6 order-0">
        <div class="card">
            <div class="d-flex align-items-start row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">Selamat Datang Kembali, {{ Auth::user()->name }}!</h5>
                        <p class="mb-6">Catat transaksi penjualan dan lihat laporan dengan akurat untuk kelancaran
                            operasional.</p>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-label-primary dropdown-toggle" type="button" id="dropdownTransaksi"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Tambah & Lihat Transaksi
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownTransaksi">
                                <li><a class="dropdown-item" href="{{ route('penjualan.create') }}">Tambah Transaksi</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('laporan.transaksi') }}">Laporan Transaksi</a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-6">
                        <img src="{{ asset('asset') }}/assets/img/illustrations/man-with-laptop.png" height="175"
                            class="scaleX-n1-rtl" alt="View Badge User" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-xxl-4">
        <div class="row">
            <div class="col-xxl-6 col-md-4 col-sm-6 col-12 mb-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                            <div class="avatar flex-shrink-0 w-px-40 h-px-40">
                                <img src="{{ asset('asset') }}/assets/img/icons/unicons/cc-warning.png" alt="wallet info"
                                    class="rounded" />
                            </div>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                    <a class="dropdown-item" href="{{ route('member.index') }}">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                        <p class="mb-1">Jumlah Member</p>
                        <h4 class="card-title mb-3">
                            {{ $jumlahMember }}
                        </h4>
                        {{-- <a href="{{ route('kategori.index') }}" class="text-primary text-decoration-none">
                        Lihat Kategori
                    </a> --}}
                    </div>
                </div>
            </div>

            <div class="col-xxl-6 col-md-4 col-sm-6 col-12 mb-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                            <div class="avatar flex-shrink-0 w-px-40 h-px-40">
                                <img src="{{ asset('asset') }}/assets/img/icons/unicons/cc-warning.png" alt="wallet info"
                                    class="rounded" />
                            </div>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                    <a class="dropdown-item" href="{{ route('penjualan.index') }}">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                        <p class="mb-1">Jumlah Penjualan</p>
                        <h4 class="card-title mb-3">
                            {{ $jumlahPenjualan }}
                        </h4>
                        {{-- <a href="{{ route('kategori.index') }}" class="text-primary text-decoration-none">
                        Lihat Kategori
                    </a> --}}
                    </div>
                </div>
            </div>

            <div class="col-xxl-6 col-md-4 col-sm-6 col-12 mb-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                            <div class="avatar flex-shrink-0 w-px-40 h-px-40">
                                <img src="{{ asset('asset') }}/assets/img/icons/unicons/cc-warning.png" alt="wallet info"
                                    class="rounded" />
                            </div>
                        </div>
                        <p class="mb-1">Jumlah Penjualan Hari Ini</p>
                        <h4 class="card-title mb-3">
                            {{ $jumlahPenjualanHariIni }}
                        </h4>
                        {{-- <a href="{{ route('kategori.index') }}" class="text-primary text-decoration-none">
                        Lihat Kategori
                    </a> --}}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="col-lg-12 col-xxl-4">
        <div class="row">

            <div class="col-xl-8 col-12 mb-6">
                <div class="card h-100">
                    <div class="card-header header-elements">
                        <h5 class="card-title mb-0">Grafik Penjualan</h5>
                        <div class="card-action-element ms-auto py-0">
                            <div class="dropdown">
                                <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="icon-base bx bx-calendar"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" id="filterDropdown">
                                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                            data-filter="today">Hari Ini</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                            data-filter="yesterday">Kemarin</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                            data-filter="last_7_days">7 Hari Terakhir</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                            data-filter="last_30_days">30 Hari Terakhir</a></li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                            data-filter="current_month">Bulan Ini</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                            data-filter="last_month">Bulan Lalu</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="barChart" class="chartjs" data-height="400"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-12 mb-6">
                <div class="card h-100">
                    <h5 class="card-header">Produk Paling Laris</h5>
                    <div class="card-body">
                        <canvas id="doughnutChart" class="chartjs mb-6" data-height="350"></canvas>
                        <ul class="doughnut-legend d-flex flex-wrap justify-content-start ps-0 mb-2 pt-1"
                            style="gap: 1rem;">
                            @php
                                $colors = ['#6a82fb', '#fc5c7d', '#45b649', '#f9d423', '#f953c6'];
                                $totalQty = $produkTerlaris->sum('total_terjual');
                            @endphp

                            @foreach ($produkTerlaris as $index => $produk)
                                @php
                                    $persen = $totalQty > 0 ? round(($produk->total_terjual / $totalQty) * 100, 2) : 0;
                                @endphp
                                <li class="ct-series-{{ $index }} d-flex align-items-center">
                                    <span class="badge badge-dot rounded-pill me-2"
                                        style="background-color: {{ $colors[$index % count($colors)] }}; width: 20px; height: 6px;"></span>
                                    <div class="d-flex flex-column">
                                        <span class="text-truncate" style="max-width: 120px;"
                                            title="{{ $produk->nama_barang }}">
                                            {{ $produk->nama_barang }}
                                        </span>
                                        <small class="text-muted">{{ $persen }}%</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('syle')
    <link rel="stylesheet" href="{{ asset('asset') }}/assets/vendor/libs/chartjs/chartjs.css" />
@endpush

@push('script')
    <script src="{{ asset('asset') }}/assets/js/charts-chartjs-legend.js"></script>
    <script src="{{ asset('asset') }}/assets/js/charts-chartjs.js"></script>
    <script src="{{ asset('asset') }}/assets/vendor/libs/chartjs/chartjs.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById("doughnutChart").getContext("2d");

            const labels = @json($produkTerlaris->pluck('nama_barang'));
            const data = @json($produkTerlaris->pluck('total_terjual'));
            const backgroundColors = ['#6a82fb', '#fc5c7d', '#45b649', '#f9d423', '#f953c6'];

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        borderWidth: 0,
                        pointStyle: "rectRounded"
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 500
                    },
                    cutout: "68%",
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return " " + labels[tooltipItem.dataIndex] + " : " + data[
                                        tooltipItem.dataIndex] + " unit";
                                }
                            },
                            backgroundColor: "#fff",
                            titleColor: "#333",
                            bodyColor: "#333",
                            borderWidth: 1,
                            borderColor: "#ddd"
                        }
                    }
                }
            });
        });
    </script>

    <script>
         document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById("barChart").getContext("2d");

            const noDataPlugin = {
                id: "noDataMessage",
                beforeDraw: function(chart) {
                    let ctx = chart.ctx;
                    let width = chart.width;
                    let height = chart.height;

                    if (chart.data.datasets[0].data.length === 0) {
                        ctx.save();
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";
                        ctx.font = "16px Arial";
                        ctx.fillStyle = "#999";
                        ctx.fillText("Tidak ada data tersedia", width / 2, height / 2);
                        ctx.restore();
                    }
                }
            };

            // Menghitung stepSize secara dinamis berdasarkan data
            function calculateStepSize(data) {
                let maxData = Math.max(...data);
                let step = 10000; // default step size
                if (maxData <= 50000) {
                    step = 5000; // Untuk data kecil
                } else if (maxData <= 200000) {
                    step = 20000; // Untuk data menengah
                } else if (maxData <= 1000000) {
                    step = 50000; // Untuk data besar
                } else {
                    step = 100000; // Untuk data sangat besar
                }
                return step;
            }

            // Hitung stepSize dinamis berdasarkan data penjualan
            let stepSize = calculateStepSize({!! json_encode($dataPenjualan) !!});

            window.myChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                            label: "Total Penjualan",
                            data: {!! json_encode($dataPenjualan) !!},
                            backgroundColor: "#71dd37",
                            borderColor: "transparent",
                            maxBarThickness: 15,
                            borderRadius: {
                                topRight: 15,
                                topLeft: 15
                            },
                            yAxisID: "y-axis-penjualan"
                        },
                        {
                            label: "Jumlah Transaksi",
                            data: {!! json_encode($jumlahTransaksi) !!},
                            backgroundColor: "#ff6384",
                            borderColor: "transparent",
                            maxBarThickness: 15,
                            borderRadius: {
                                topRight: 15,
                                topLeft: 15
                            },
                            yAxisID: "y-axis-transaksi"
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 500
                    },
                    plugins: {
                        noDataMessage: true,
                        tooltip: {
                            backgroundColor: "#fff",
                            titleColor: "#333",
                            bodyColor: "#333",
                            borderWidth: 1,
                            borderColor: "#ddd"
                        },
                        legend: {
                            display: true,
                            position: "top"
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: "#ddd"
                            },
                            ticks: {
                                color: "#666"
                            }
                        },
                        "y-axis-penjualan": {
                            position: "left",
                            ticks: {
                                stepSize: stepSize, // Menggunakan stepSize yang dinamis
                                callback: function(value) {
                                    return value.toLocaleString('id-ID'); // Format IDR
                                },
                                color: "#888"
                            },
                            grid: {
                                drawBorder: false
                            },
                            suggestedMin: 0, // Menambahkan batas minimum pada Y
                            suggestedMax: Math.max(...{!! json_encode($dataPenjualan) !!}) *
                                1.2 // Membuat batas maksimum sedikit lebih besar
                        },
                        "y-axis-transaksi": {
                            position: "right",
                            ticks: {
                                stepSize: 5,
                                color: "#888"
                            },
                            grid: {
                                drawBorder: false,
                                drawOnChartArea: false
                            }
                        }
                    }
                },
                plugins: [noDataPlugin]
            });

            document.querySelectorAll("#filterDropdown a").forEach(item => {
                item.addEventListener("click", async function() {
                    let filter = this.getAttribute("data-filter");

                    try {
                        let response = await fetch(
                            `/kasirHome/filter-penjualan?filter=${filter}`);
                        let data = await response.json();
                        updateChart(data.labels, data.dataPenjualan, data.jumlahTransaksi);
                    } catch (error) {
                        console.error("Error fetching data:", error);
                    }
                });
            });

            function updateChart(labels, dataPenjualan, jumlahTransaksi) {
                if (window.myChart) {
                    window.myChart.data.labels = labels;
                    window.myChart.data.datasets[0].data = dataPenjualan; // Data untuk total penjualan
                    window.myChart.data.datasets[1].data = jumlahTransaksi; // Data untuk jumlah transaksi
                    window.myChart.update();
                } else {
                    console.error("Chart not initialized!");
                }
            }
        });
    </script>
@endpush
