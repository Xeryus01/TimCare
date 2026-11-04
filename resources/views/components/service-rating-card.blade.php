@props([
    'model',
    'routeName',
    'title' => 'Penilaian Layanan',
    'subtitle' => 'Bantu kami meningkatkan kualitas pelayanan.',
])

@php
    $aspects = [
        'service_speed_rating' => ['label' => 'Kecepatan respon', 'hint' => 'Seberapa cepat permintaan ditangani?'],
        'service_quality_rating' => ['label' => 'Kualitas penyelesaian', 'hint' => 'Apakah solusi/hasil sesuai kebutuhan?'],
        'service_communication_rating' => ['label' => 'Komunikasi petugas', 'hint' => 'Kejelasan informasi dan keramahan layanan.'],
    ];
    $labels = [1 => 'Sangat kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik', 5 => 'Sangat baik'];
@endphp

<div class="rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 via-white to-white p-6 shadow-sm dark:border-yellow-500/20 dark:from-yellow-500/10 dark:via-dark-800 dark:to-dark-800">
    <div class="mb-5 flex items-start gap-3">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-yellow-400 text-xl text-white shadow-sm">★</div>
        <div>
            <h3 class="text-base font-bold uppercase tracking-wide text-gray-700 dark:text-gray-200">{{ $title }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
        </div>
    </div>

    @if($model->service_rating)
        <div class="mb-5 rounded-xl border border-yellow-200 bg-white/80 p-4 dark:border-yellow-500/20 dark:bg-white/5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata penilaian</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($model->service_rating, 0) }}<span class="text-sm font-medium text-gray-500">/5</span></p>
                </div>
                <div class="text-2xl text-yellow-400">
                    @for($i = 1; $i <= 5; $i++){{ $i <= $model->service_rating ? '★' : '☆' }}@endfor
                </div>
            </div>
            @if($model->service_feedback)
                <p class="mt-3 whitespace-pre-line rounded-lg bg-gray-50 p-3 text-sm text-gray-700 dark:bg-dark-900/40 dark:text-gray-300">{{ $model->service_feedback }}</p>
            @endif
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Dinilai pada {{ optional($model->rated_at)?->format('d/m/Y H:i') }}</p>
        </div>
    @endif

    @if(auth()->id() === $model->requester_id)
        <form method="POST" action="{{ route($routeName, $model) }}" class="space-y-5 js-service-rating-form">
            @csrf
            @foreach($aspects as $field => $aspect)
                @php $current = (int) old($field, $model->{$field}); @endphp
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-900/30">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-white">{{ $aspect['label'] }}</label>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $aspect['hint'] }}</p>
                        </div>
                        <span class="js-rating-label rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:bg-white/10 dark:text-gray-300" data-default="Pilih">{{ $current ? $labels[$current] : 'Pilih' }}</span>
                    </div>

                    <div class="js-star-rating flex gap-1.5" data-labels='@json($labels)'>
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="js-star rounded-lg p-1 text-3xl leading-none transition hover:scale-110 {{ $i <= $current ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" data-value="{{ $i }}" aria-label="{{ $i }} bintang">★</button>
                        @endfor
                        <input type="hidden" name="{{ $field }}" value="{{ $current ?: '' }}" required>
                    </div>
                    @error($field)<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            @endforeach

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white">Catatan feedback <span class="font-normal text-gray-400">(opsional)</span></label>
                <textarea name="service_feedback" rows="3" maxlength="1000" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm transition focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 dark:border-gray-600 dark:bg-dark-900 dark:text-white" placeholder="Tuliskan masukan singkat agar pelayanan semakin baik...">{{ old('service_feedback', $model->service_feedback) }}</textarea>
                @error('service_feedback')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-500/20 dark:bg-yellow-500/10">
                <p class="mb-3 text-xs font-medium text-yellow-800 dark:text-yellow-200">
                    Cara submit: pilih bintang pada ketiga aspek, isi catatan jika perlu, lalu klik tombol di bawah ini.
                </p>
                <button type="submit" class="js-rating-submit flex w-full items-center justify-center gap-2 rounded-xl bg-yellow-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500">
                    <span>✓</span>
                    <span>{{ $model->service_rating ? 'Perbarui Penilaian' : 'Submit / Kirim Penilaian' }}</span>
                </button>
            </div>
        </form>
    @elseif(! $model->service_rating)
        <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Belum ada penilaian dari pemohon.</p>
    @endif
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-star-rating').forEach(function (group) {
        const form = group.closest('.js-service-rating-form');
        const input = group.querySelector('input[type="hidden"]');
        const stars = Array.from(group.querySelectorAll('.js-star'));
        const label = group.closest('.rounded-xl').querySelector('.js-rating-label');
        const labels = JSON.parse(group.dataset.labels || '{}');

        function updateSubmitState() {
            if (!form) return;
            const submit = form.querySelector('.js-rating-submit');
            const inputs = Array.from(form.querySelectorAll('.js-star-rating input[type="hidden"]'));
            const complete = inputs.every(function (item) { return Number(item.value) >= 1 && Number(item.value) <= 5; });
            if (submit) submit.disabled = !complete;
        }

        function paint(value) {
            stars.forEach(function (star) {
                const active = Number(star.dataset.value) <= Number(value);
                star.classList.toggle('text-yellow-400', active);
                star.classList.toggle('text-gray-300', !active);
                star.classList.toggle('dark:text-gray-600', !active);
            });
            if (label) label.textContent = value ? labels[value] : (label.dataset.default || 'Pilih');
        }

        stars.forEach(function (star) {
            star.addEventListener('mouseenter', function () { paint(star.dataset.value); });
            star.addEventListener('click', function () {
                input.value = star.dataset.value;
                paint(input.value);
                updateSubmitState();
            });
        });
        group.addEventListener('mouseleave', function () { paint(input.value); });
        paint(input.value);
        updateSubmitState();
    });
    document.querySelectorAll('.js-service-rating-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const missing = Array.from(form.querySelectorAll('.js-star-rating input[type="hidden"]')).some(function (input) {
                return !(Number(input.value) >= 1 && Number(input.value) <= 5);
            });
            if (missing) {
                event.preventDefault();
                alert('Mohon pilih bintang untuk semua aspek penilaian sebelum submit.');
            }
        });
    });
});
</script>
@endonce
