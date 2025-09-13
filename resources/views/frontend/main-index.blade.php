{{-- resources/views/frontend/main-index.blade.php --}}
@extends('frontend.layouts.layout')

@section('title', 'Home | ' . config('Lucky Draw', 'Lucky Draw'))

@section('content')
    {{-- CSRF meta for AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* prevent click on QR to open; caption remains clickable */
        .qr-box { pointer-events: none; }
        .qr-caption { pointer-events: auto; font-size: 0.85rem; color: #6c757d; }
    </style>

    {{-- Hero Section --}}
    <section class="text-center bg-light py-5">
        <div class="container">
            <h1 class="display-4 fw-bold">Lucky Draw</h1>
            <div class="mt-4">
                <input type="number" id="qrInput" class="form-control w-50 mx-auto" placeholder="Enter value (e.g. 5)">
                <button id="generateBtn" class="btn btn-primary mt-3">Generate QR</button>
                <a href="{{ url('/') }}" class="btn btn-secondary mt-3">Reset</a>
            </div>
        </div>
    </section>

    {{-- Scanner Section --}}
    <section class="py-5">
        <div class="container">
            <div class="row g-4 text-center">
                {{-- Scanner One --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold">Scanner One</h5>
                            <div id="scannerOne" class="d-flex flex-column align-items-center mt-3"></div>
                        </div>
                    </div>
                </div>
                {{-- Scanner Two --}}
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold">Scanner Two</h5>
                            <div id="scannerTwo" class="d-flex flex-column align-items-center mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

{{-- Put scripts here so they load after DOM (if your layout yields 'scripts' section use that instead) --}}

    <!-- Replace with your actual local files or CDN links -->
    <script src="{{ asset('frontend/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/js/qrcode.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const generateBtn = document.getElementById('generateBtn');
    generateBtn.addEventListener('click', generateQRCodes);
});

const baseScannerUrlB64 = "{{ url('/scanner/form/b64') }}";
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function generateQRCodes() {
    let value = parseInt(document.getElementById("qrInput").value) || 0;
    let scannerOne = document.getElementById("scannerOne");
    let scannerTwo = document.getElementById("scannerTwo");

    scannerOne.innerHTML = "";
    scannerTwo.innerHTML = "";

    if (value <= 0) {
        alert('Please enter a number greater than 0');
        return;
    }

    // single timestamp for the batch (milliseconds, 13 digits)
    const ts = Date.now();

    // Build array of unique IDs with timestamp: ID-<timestamp>-<i>
    const uniqueIds = [];
    for (let i = 1; i <= value; i++) {
        uniqueIds.push(`ID-${ts}-${i}`);
    }

    // Bulk check existing IDs via one POST
    let existingSet = new Set();
    try {
        const resp = await fetch('{{ route("scanner.existsBulk") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ ids: uniqueIds })
        });

        if (resp.ok) {
            const json = await resp.json();
            (json.existing || []).forEach(id => existingSet.add(id));
        } else {
            console.warn('exists-bulk responded with', resp.status);
        }
    } catch (e) {
        console.warn('exists-bulk failed', e);
    }

    // Filter to only IDs that do NOT exist
    const toRender = uniqueIds.filter(id => !existingSet.has(id));

    if (toRender.length === 0) {
        const msg = document.createElement('div');
        msg.className = 'alert alert-info w-100';
        msg.innerText = 'No new QR codes to display — all IDs already registered.';
        scannerOne.appendChild(msg.cloneNode(true));
        scannerTwo.appendChild(msg);
        return;
    }

    // Render QRs — each points to /scanner/form/b64/{base64}
    toRender.forEach(uniqueId => {
        // standard base64, then make URL-safe with encodeURIComponent
        const b64 = encodeURIComponent(btoa(uniqueId));
        const openUrl = baseScannerUrlB64 + '/' + b64;

        const makeBox = (container) => {
            const wrapper = document.createElement('div');
            wrapper.classList.add('d-flex', 'flex-column', 'align-items-center', 'mb-3');

            const qrHolder = document.createElement('div');
            qrHolder.classList.add("qr-box", "p-2", "border", "rounded", "d-flex", "justify-content-center");
            qrHolder.style.cursor = 'default';
            qrHolder.dataset.url = openUrl;

            try {
                new QRCode(qrHolder, {
                    text: openUrl,
                    width: 150,
                    height: 150
                });
            } catch (err) {
                console.error('QRCode generation failed for', uniqueId, err);
                const errNote = document.createElement('div');
                errNote.className = 'text-danger';
                errNote.innerText = 'QR error';
                qrHolder.appendChild(errNote);
            }

            const caption = document.createElement('div');
            caption.classList.add('qr-caption', 'mt-1');
            // caption.innerText = `${uniqueId} (Base64 link)`;

            wrapper.appendChild(qrHolder);
            wrapper.appendChild(caption);
            container.appendChild(wrapper);
        };

        makeBox(scannerOne);
        makeBox(scannerTwo);
    });
}
</script>

