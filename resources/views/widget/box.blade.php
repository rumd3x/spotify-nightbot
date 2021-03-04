<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style type="text/css">
        body {
            background-color: {{ $widget->background_color }};
            font-family: {!! $font !!};
            font-size: {{ $widget->font_size }}px;
            color: {{ $widget->text_color }};
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        const animateCSS = (element, animation, prefix = 'animate__') =>
        new Promise((resolve, reject) => {
            const animationName = `${prefix}${animation}`;
            const node = document.querySelector(element);

            node.classList.add(`${prefix}animated`, animationName);

            function handleAnimationEnd(event) {
                event.stopPropagation();
                node.classList.remove(`${prefix}animated`, animationName);
                resolve('Animation ended');
            }

            node.addEventListener('animationend', handleAnimationEnd, {once: true});
        });
    </script>
    <script type="text/javascript">
        const fetchCurrentlyPlaying = () => {
            $.get(window.location.href + '/raw').then(function(text) {
                setTimeout(function(){
                    fetchCurrentlyPlaying();
                }, 10000);

                let currentText = $("#nowPlaying").text();
                if (currentText !== text) {
                    $("#nowPlaying").text(text);
                    $("#nowPlaying").show();

                    if ('{{ $widget->transition_in }}' !== 'none') {
                        animateCSS("#nowPlaying", '{{ $widget->transition_in }}');
                    }
                    if ('{{ $widget->transition_out }}' !== 'none') {
                        setTimeout(function(){
                            animateCSS("#nowPlaying", '{{ $widget->transition_out }}').then(() => {
                                $("#nowPlaying").hide();
                            });
                        }, 15000);                
                    }
                }
            });
        };
    </script>
</head>

<body>
    <div id="nowPlaying">
    </div>

    <script type="text/javascript">
        fetchCurrentlyPlaying()
    </script>
</body>