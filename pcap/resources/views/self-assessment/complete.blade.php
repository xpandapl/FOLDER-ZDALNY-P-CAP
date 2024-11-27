@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ocena zakończona</title>
    <style>
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            text-align: center;
        }
        .email-form {
            margin-top: 20px;
        }
        .email-form input[type="email"] {
            padding: 10px;
            width: 80%;
            margin-bottom: 10px;
        }
        .email-form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .email-form button:hover {
            background-color: #45a049;
        }
        .download-button {
            background-color: deepskyblue;
            border: none;
            padding: 10px;
            border-radius: 10px;
            color: white;
            padding-right: 15px;
            padding-left: 15px;
        }
        .download-button:hover {
            background-color:dodgerblue;
        }
        .download-options {
            margin-bottom:30px;
        }
        .button {
            display: inline-block;
            background-color: deepskyblue;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
        }

        .button:hover {
            background-color: dodgerblue;
            color:white;
        }
        .button-home {
            display: inline-block;
            background-color: grey;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
        }

        .button-home:hover {
            background-color: lightgrey;
            color:white
        }

        .button i {
            margin-right: 8px;
        }
        .edit-link-container {
            text-align: center;
            width: 70%;
            margin: auto;
        }


        .edit-link-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .home-button-container {
            text-align: center;
            margin-top: 150px; /* Możesz dostosować wartość, aby uzyskać pożądany odstęp */
        }



        #editLink {
            width: 50%;
            padding: 10px;
            font-size: 14px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .copy-button {
            background-color: #4CAF50;
            border:none;
            font-size:10px;
        }

        .copy-button:hover {
            background-color: #45a049;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
    <input type="hidden" name="uuid" value="{{ $uuid }}">
        <h1><i class="fas fa-check-circle"></i> Ocena zakończona</h1>
        <p>Dziękujemy za wypełnienie formularza samooceny.</p>
        <div class="download-options">
        <a href="{{ route('self.assessment.generate_pdf', ['uuid' => $uuid]) }}" target="_blank" class="button download-button">
            <i class="fas fa-file-pdf"></i> Pobierz podsumowanie w PDF
        </a>
        <a href="{{ route('self.assessment.generate_xls', ['uuid' => $uuid]) }}" target="_blank" class="button download-button">
            <i class="fas fa-file-excel"></i> Pobierz podsumowanie w XLS
        </a>
        </div>




        <div class="edit-link-container">
            <p>Zapisz ten link, pozwoli Ci on wrócić i edytować Twoje odpowiedzi do czasu zamknięcia samooceny tj. 19-11-2024 (po tym czasie edycja nie będzie możliwa):</p>
            <div class="edit-link-wrapper">
                <input type="text" id="editLink" value="{{ route('form.edit', ['uuid' => $uuid]) }}" readonly>
                <button onclick="copyToClipboard()" class="button copy-button">
                    <i class="fas fa-copy"></i> Kopiuj link
                </button>
            </div>
            <div class="home-button-container">
            <a href="{{ url('/') }}" class="button-home">
                <i class="fas fa-home"></i> Powrót do strony głównej
            </a>
        </div>
        </div>

    </div>
    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("editLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // Dla urządzeń mobilnych

            document.execCommand("copy");

            alert("Link został skopiowany do schowka.");
        }
    </script>
    </body>
</html>
