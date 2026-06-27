@props(['disabled' => false])

<div x-data="{
    model: @entangle($attributes->wire('model')),
    display: '',
    timeout: null,
    init() {
        this.display = this.format(this.model);
        this.$watch('model', value => {
            if (this.normalize(this.display) !== this.normalize(value)) {
                this.display = this.format(value);
            }
        });
    },
    separators() {
        return {
            thousand: window.thousandSeparator || '.',
            decimal: window.decimalSeparator || ',',
        };
    },
    normalize(value) {
        if (value === null || value === undefined || value === '') return '';
        let raw = String(value).trim();
        const { thousand, decimal } = this.separators();

        if (thousand) {
            raw = raw.split(thousand).join('');
        }

        if (decimal && decimal !== '.') {
            raw = raw.replace(decimal, '.');
        }

        raw = raw.replace(/[^0-9.\-]/g, '');

        const firstDot = raw.indexOf('.');
        if (firstDot !== -1) {
            raw = raw.slice(0, firstDot + 1) + raw.slice(firstDot + 1).replace(/\./g, '');
        }

        return raw;
    },
    format(value) {
        if (value === null || value === '') return '';
        let raw = this.normalize(value);

        if (raw === '' || raw === '-') return '';

        let negative = raw.startsWith('-');
        if (negative) raw = raw.slice(1);

        let [integerPart, decimalPart = ''] = raw.split('.');
        integerPart = integerPart || '0';

        const { thousand, decimal } = this.separators();
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousand);

        let formatted = negative ? '-' + integerPart : integerPart;

        if (decimalPart !== '') {
            formatted += decimal + decimalPart.slice(0, 3);
        }

        return formatted;
    },
    update(event) {
        let raw = this.normalize(event.target.value);

        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            this.model = raw === '' ? null : raw;
            this.display = this.format(raw);
        }, 800);
    }
}"
class="w-full"
>
    <input
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50']) !!}
        type="text"
        :value="display"
        @input="update($event)"
        @blur="display = format(model)"
    />
</div>
