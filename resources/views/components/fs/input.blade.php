@props(['label', 'name', 'type' => 'text', 'value' => null, 'hint' => null])

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-body-sm font-semibold text-ink-900">{{ $label }}</label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @unless ($type === 'password') value="{{ old($name, $value) }}" @endunless
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        {{ $attributes->class([
            'block min-h-11 w-full rounded-lg border bg-surface px-3 text-body text-ink-900 placeholder:text-ink-500',
            'border-line' => ! $errors->has($name),
            'border-danger' => $errors->has($name),
        ]) }}
    >

    @if ($hint && ! $errors->has($name))
        <p class="mt-1.5 text-caption text-ink-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 flex items-center gap-1 text-body-sm text-danger">
            <x-fs.icon name="alert" class="size-4 shrink-0" /> {{ $message }}
        </p>
    @enderror
</div>
