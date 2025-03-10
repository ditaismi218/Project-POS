@extends('layouts.layout')

@section('content')
    <div class="card">
        {{-- <h5 class="card-header text-md-start text-center">Laporan Penjualan Barang</h5> --}}

        {{-- Form Filter --}}
        {{-- <form method="GET" action="{{ route('laporan.penjualan') }}" class="mb-3 d-flex gap-2">
            <select name="kategori" class="form-control w-auto">
                <option value="">-- Semua Kategori --</option>
                @foreach ($kategoriList as $kategori)
                    <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('laporan.penjualan') }}" class="btn btn-secondary">Reset</a> --}}
        {{-- </form> --}}

        <div class="card-datatable">
            {{-- <table class="dt-scrollableTable table table-bordered"> --}}
            <table class="datatables-basic table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Total Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->nama_kategori }}</td>
                            <td class="text-center">{{ $item->total_terjual }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end">Total Keseluruhan:</td>
                        <td class="text-center">{{ $totalQty }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function(e) {
            var r,
                t = document.querySelector(".datatables-basic");
            let o;
            t &&
                ((s = document.createElement("h5")).classList.add(
                        "card-title",
                        "mb-0",
                        "text-md-start",
                        "text-center"
                    ),
                    (o = new DataTable(t, {
                        select: {
                            style: "multi",
                            selector: "td:nth-child(2)"
                        },
                        order: [
                            [2, "desc"]
                        ],
                        layout: {
                            top2Start: {
                                rowClass: "row card-header flex-column flex-md-row pb-0",
                                features: [s],
                            },
                            top2End: {
                                features: [{
                                    buttons: [{
                                        extend: "collection",
                                        className: "btn btn-label-primary dropdown-toggle me-4",
                                        text: '<span class="d-flex align-items-center gap-2"><i class="icon-base bx bx-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
                                        buttons: [{
                                                extend: "print",
                                                text: '<span class="d-flex align-items-center"><i class="icon-base bx bx-printer me-1"></i>Print</span>',
                                                className: "dropdown-item",
                                                exportOptions: {
                                                    columns: [0, 1, 2, 3, 4],
                                                    format: {
                                                        body: function(e, t, a) {
                                                            if (
                                                                e.length <= 0 ||
                                                                !(
                                                                    -1 <
                                                                    e.indexOf("<")
                                                                )
                                                            )
                                                                return e;
                                                            {
                                                                e =
                                                                    new DOMParser()
                                                                    .parseFromString(
                                                                        e,
                                                                        "text/html"
                                                                    );
                                                                let t = "";
                                                                var s =
                                                                    e
                                                                    .querySelectorAll(
                                                                        ".user-name"
                                                                    );
                                                                return (
                                                                    0 < s
                                                                    .length ?
                                                                    s.forEach(
                                                                        (
                                                                            e
                                                                        ) => {
                                                                            e =
                                                                                e
                                                                                .querySelector(
                                                                                    ".fw-medium"
                                                                                )
                                                                                ?.textContent ||
                                                                                e
                                                                                .querySelector(
                                                                                    ".d-block"
                                                                                )
                                                                                ?.textContent ||
                                                                                e
                                                                                .textContent;
                                                                            t +=
                                                                                e
                                                                                .trim() +
                                                                                " ";
                                                                        }
                                                                    ) :
                                                                    (t =
                                                                        e.body
                                                                        .textContent ||
                                                                        e.body
                                                                        .innerText
                                                                    ),
                                                                    t.trim()
                                                                );
                                                            }
                                                        },
                                                    },
                                                },
                                                customize: function(e) {
                                                    (e.document.body.style.color =
                                                        config.colors.headingColor),
                                                    (e.document.body.style
                                                        .borderColor =
                                                        config.colors.borderColor),
                                                    (e.document.body.style
                                                        .backgroundColor =
                                                        config.colors.bodyBg);
                                                    e =
                                                        e.document.body
                                                        .querySelector(
                                                            "table"
                                                        );
                                                    e.classList.add("compact"),
                                                        (e.style.color = "inherit"),
                                                        (e.style.borderColor =
                                                            "inherit"),
                                                        (e.style.backgroundColor =
                                                            "inherit");
                                                },
                                            },
                                            {
                                                extend: "csv",
                                                text: '<span class="d-flex align-items-center"><i class="icon-base bx bx-file me-1"></i>Csv</span>',
                                                className: "dropdown-item",
                                                exportOptions: {
                                                    columns: [0, 1, 2, 3, 4],
                                                    format: {
                                                        body: function(e, t, a) {
                                                            if (e.length <= 0)
                                                                return e;
                                                            e =
                                                                new DOMParser()
                                                                .parseFromString(
                                                                    e,
                                                                    "text/html"
                                                                );
                                                            let s = "";
                                                            var n =
                                                                e.querySelectorAll(
                                                                    ".user-name"
                                                                );
                                                            return (
                                                                0 < n.length ?
                                                                n.forEach(
                                                                    (e) => {
                                                                        e =
                                                                            e
                                                                            .querySelector(
                                                                                ".fw-medium"
                                                                            )
                                                                            ?.textContent ||
                                                                            e
                                                                            .querySelector(
                                                                                ".d-block"
                                                                            )
                                                                            ?.textContent ||
                                                                            e
                                                                            .textContent;
                                                                        s +=
                                                                            e
                                                                            .trim() +
                                                                            " ";
                                                                    }
                                                                ) :
                                                                (s =
                                                                    e.body
                                                                    .textContent ||
                                                                    e.body
                                                                    .innerText),
                                                                s.trim()
                                                            );
                                                        },
                                                    },
                                                },
                                            },
                                            {
                                                extend: "excel",
                                                text: '<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-export me-1"></i>Excel</span>',
                                                className: "dropdown-item",
                                                exportOptions: {
                                                    columns: [0, 1, 2, 3, 4],
                                                    format: {
                                                        body: function(e, t, a) {
                                                            if (e.length <= 0)
                                                                return e;
                                                            e =
                                                                new DOMParser()
                                                                .parseFromString(
                                                                    e,
                                                                    "text/html"
                                                                );
                                                            let s = "";
                                                            var n =
                                                                e.querySelectorAll(
                                                                    ".user-name"
                                                                );
                                                            return (
                                                                0 < n.length ?
                                                                n.forEach(
                                                                    (e) => {
                                                                        e =
                                                                            e
                                                                            .querySelector(
                                                                                ".fw-medium"
                                                                            )
                                                                            ?.textContent ||
                                                                            e
                                                                            .querySelector(
                                                                                ".d-block"
                                                                            )
                                                                            ?.textContent ||
                                                                            e
                                                                            .textContent;
                                                                        s +=
                                                                            e
                                                                            .trim() +
                                                                            " ";
                                                                    }
                                                                ) :
                                                                (s =
                                                                    e.body
                                                                    .textContent ||
                                                                    e.body
                                                                    .innerText),
                                                                s.trim()
                                                            );
                                                        },
                                                    },
                                                },
                                            },
                                            {
                                                extend: "pdf",
                                                text: '<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-pdf me-1"></i>Pdf</span>',
                                                className: "dropdown-item",
                                                exportOptions: {
                                                    columns: [0, 1, 2, 3, 4],
                                                    format: {
                                                        body: function(e, t, a) {
                                                            if (e.length <= 0)
                                                                return e;
                                                            e =
                                                                new DOMParser()
                                                                .parseFromString(
                                                                    e,
                                                                    "text/html"
                                                                );
                                                            let s = "";
                                                            var n =
                                                                e.querySelectorAll(
                                                                    ".user-name"
                                                                );
                                                            return (
                                                                0 < n.length ?
                                                                n.forEach(
                                                                    (e) => {
                                                                        e =
                                                                            e
                                                                            .querySelector(
                                                                                ".fw-medium"
                                                                            )
                                                                            ?.textContent ||
                                                                            e
                                                                            .querySelector(
                                                                                ".d-block"
                                                                            )
                                                                            ?.textContent ||
                                                                            e
                                                                            .textContent;
                                                                        s +=
                                                                            e
                                                                            .trim() +
                                                                            " ";
                                                                    }
                                                                ) :
                                                                (s =
                                                                    e.body
                                                                    .textContent ||
                                                                    e.body
                                                                    .innerText),
                                                                s.trim()
                                                            );
                                                        },
                                                    },
                                                },
                                            },
                                            {
                                                extend: "copy",
                                                text: '<i class="icon-base bx bx-copy me-1"></i>Copy',
                                                className: "dropdown-item",
                                                exportOptions: {
                                                    columns: [0, 1, 2, 3, 4],
                                                    format: {
                                                        body: function(e, t, a) {
                                                            if (e.length <= 0)
                                                                return e;
                                                            e =
                                                                new DOMParser()
                                                                .parseFromString(
                                                                    e,
                                                                    "text/html"
                                                                );
                                                            let s = "";
                                                            var n =
                                                                e.querySelectorAll(
                                                                    ".user-name"
                                                                );
                                                            return (
                                                                0 < n.length ?
                                                                n.forEach(
                                                                    (e) => {
                                                                        e =
                                                                            e
                                                                            .querySelector(
                                                                                ".fw-medium"
                                                                            )
                                                                            ?.textContent ||
                                                                            e
                                                                            .querySelector(
                                                                                ".d-block"
                                                                            )
                                                                            ?.textContent ||
                                                                            e
                                                                            .textContent;
                                                                        s +=
                                                                            e
                                                                            .trim() +
                                                                            " ";
                                                                    }
                                                                ) :
                                                                (s =
                                                                    e.body
                                                                    .textContent ||
                                                                    e.body
                                                                    .innerText),
                                                                s.trim()
                                                            );
                                                        },
                                                    },
                                                },
                                            },
                                        ],
                                    }, ],
                                }, ],
                            },
                            topStart: {
                                rowClass: "row m-3 my-0 justify-content-between",
                                features: [{
                                    pageLength: {
                                        menu: [10, 25, 50, 100],
                                        text: "Show_MENU_entries",
                                    },
                                }, ],
                            },
                            topEnd: {
                                search: {
                                    placeholder: ""
                                }
                            },
                            bottomStart: {
                                rowClass: "row mx-3 justify-content-between",
                                features: ["info"],
                            },
                            bottomEnd: {
                                paging: {
                                    firstLast: !1
                                }
                            },
                        },
                        language: {
                            paginate: {
                                next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-sm"></i>',
                                previous: '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-sm"></i>',
                            },
                        },
                        responsive: {
                            details: {
                                display: DataTable.Responsive.display.modal({
                                    header: function(e) {
                                        return "Details of " + e.data().full_name;
                                    },
                                }),
                                type: "column",
                                renderer: function(e, t, a) {
                                    var s,
                                        n,
                                        r,
                                        a = a
                                        .map(function(e) {
                                            return "" !== e.title ?
                                                `<tr data-dt-row="${e.rowIndex}" data-dt-column="${e.columnIndex}">
                      <td>${e.title}:</td>
                      <td>${e.data}</td>
                    </tr>` :
                                                "";
                                        })
                                        .join("");
                                    return (
                                        !!a &&
                                        ((s = document.createElement("div")).classList.add(
                                                "table-responsive"
                                            ),
                                            (n = document.createElement("table")),
                                            s.appendChild(n),
                                            n.classList.add("table"),
                                            n.classList.add("datatables-basic"),
                                            ((r = document.createElement("tbody")).innerHTML =
                                                a),
                                            n.appendChild(r),
                                            s)
                                    );
                                },
                            },
                        },
                    })),
                    (r = 101),
                    fv.on("core.form.valid", function() {
                        var e = document.querySelector(
                                ".add-new-record .dt-full-name"
                            ).value,
                            t = document.querySelector(".add-new-record .dt-post").value,
                            a = document.querySelector(".add-new-record .dt-email").value,
                            s = document.querySelector(".add-new-record .dt-date").value,
                            n = document.querySelector(".add-new-record .dt-salary").value;
                        "" != e &&
                            (o.row
                                .add({
                                    id: r,
                                    full_name: e,
                                    post: t,
                                    email: a,
                                    start_date: s,
                                    salary: "$" + n,
                                    status: 5,
                                })
                                .draw(),
                                r++,
                                offCanvasEl.hide());
                    }),
                    document.addEventListener("click", function(e) {
                        e.target.classList.contains("delete-record") &&
                            (o.row(e.target.closest("tr")).remove().draw(),
                                (e = document.querySelector(".dtr-bs-modal"))) &&
                            e.classList.contains("show") &&
                            bootstrap.Modal.getInstance(e)?.hide();
                    })),
                    setTimeout(() => {
            [
                {
                    selector: ".dt-buttons .btn",
                    classToRemove: "btn-secondary",
                },
                {
                    selector: ".dt-search .form-control",
                    classToRemove: "form-control-sm",
                    classToAdd: "ms-4",
                },
                {
                    selector: ".dt-length .form-select",
                    classToRemove: "form-select-sm",
                },
                { selector: ".dt-layout-table", classToRemove: "row mt-2" },
                { selector: ".dt-layout-end", classToAdd: "mt-0" },
                {
                    selector: ".dt-layout-end .dt-search",
                    classToAdd: "mt-0 mt-md-6",
                },
                { selector: ".dt-layout-start", classToAdd: "mt-0" },
                { selector: ".dt-layout-end .dt-buttons", classToAdd: "mb-0" },
            ].forEach(({ selector: e, classToRemove: a, classToAdd: s }) => {
                document.querySelectorAll(e).forEach((t) => {
                    a && a.split(" ").forEach((e) => t.classList.remove(e)),
                        s && s.split(" ").forEach((e) => t.classList.add(e));
                });
            });
        }, 100);

        });
    </script>
@endpush
