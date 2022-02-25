<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        @trixassets
        @livewireStyles

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <div class="event-notification-box fixed right-0 top-0 text-white bg-green-400 mt-3 mr-3 px-5 py-3 rounded-sm shadow-lg transform duration-700 opacity-0">
                    Test
                </div>    

                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>

            /**
             * Initialize the websocket client
             */
            function clientSocket(config = {}){
                let route = config.route || "127.0.0.1";
                let port = config.port || "3280";
                window.Websocket = window.Websocket || window.MozWebSocket;
                return new WebSocket("ws://"+ route + ":" + port);
            }
            
            // Instantiate a connection 
            var connection = clientSocket();

            /**
             * The Event Listener that will be dispatched
             * to the websocket server.
             */
            window.addEventListener('event-notification', event => {
                connection.send(JSON.stringify({
                    eventName: event.detail.eventName,
                    eventMessage: event.detail.eventMessage
                }));
            });

            /**
             * When the connection is open
             */
            connection.onopen = function () {
                console.log("Connection is open!");
            }

            /**
             * Will receive messages from the websocket server
             */
            connection.onmessage = function (message){
                var result = JSON.parse(message.data);
                var notificationMessage = `
                    <h3>${result.eventName}</h3>
                    <p>${result.eventMessage}</p>
                `;
                //Begin Animation - Display Message
                $(".event-notification-box").html(notificationMessage);
                $(".event-notification-box").removeClass("opacity-0");
                $(".event-notification-box").addClass("opacity-100");

                // Hide the message after 3 seconds
                setTimeout(() => {
                    $(".event-notification-box").removeClass("opacity-100");
                    $(".event-notification-box").addClass("opacity-0");
                }, 3000);
            }

        </script>
    </body>
</html>
