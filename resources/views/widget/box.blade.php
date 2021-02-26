<meta http-equiv="refresh" content="10" />

<style>
    body {
        background-color: {{ $widget->background_color }};
        font-family: {!! $font !!};
        font-size: {{ $widget->font_size }}px;
        color: {{ $widget->text_color }};
    }
</style>

<body>
    {{ $text }}
</body>