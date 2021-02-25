<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Spotify -> Nightbot</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.css" rel="stylesheet">

</head>

<body class="bg-gradient-secondary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <img height="85" src="img/logo.png" alt="Spotify-Nightbot">
                                    </div>
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Let Nightbot tell your viewers the name of the song currently playing</h1>
                                    </div>
                                    <form class="user">
                                        <hr>
                                        <a href="{{ route('spotify.login') }}" class="btn btn-spotify btn-user btn-block">
                                            <i class="fab fa-spotify fa-fw"></i> Login with Spotify
                                        </a>

                                        @csrf
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        Spotify-Nightbot is a third-party service that provides automated integration between the two services. 
                                        It was created for myself but I decided to share it with the world by open-sourcing it, making a nice UI,
                                        hosting it on my servers and allowing anyone to use it for free.
                                        <br><br>
                                        If you like it, you can thank me on my twitch by whatever means you wish <a target="_blank" href="https://www.twitch.tv/rumd3x">@rumd3x</a>.
                                        <br>
                                        Also, if you are a developer, star this project on Github! <a style="margin-top: 3px; margin-left: 3px;" class="github-button" href="https://github.com/rumd3x/spotify-nightbot" data-icon="octicon-star" data-show-count="true" aria-label="Star rumd3x/spotify-nightbot on GitHub">Star</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>