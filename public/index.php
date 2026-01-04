<html>
<head>
    <title>RAG PHP</title>
    <style>
        form {
            width: 280px;
            height: 400px;
            float: left;
            margin: 20px;
            background-color: #fcfcfc;
            padding: 20px 50px 40px;
            box-shadow: 1px 4px 10px 1px #aaa;
            font-family: sans-serif;
        }

        form.ollama {
            margin-bottom: 20px;
        }

        form * {
            box-sizing: border-box;
        }

        form label {
            color: #777;
            font-size: 0.8em;
            text-align: center;
        }

        form button[type=submit] {
            display: block;
            margin: 20px auto 0;
            width: 150px;
            height: 40px;
            border-radius: 25px;
            border: none;
            color: #eee;
            font-weight: 700;
            box-shadow: 1px 4px 10px 1px #aaa;
            cursor: pointer;

            background: #207cca; /* Old browsers */
            background: -moz-linear-gradient(left, #095516 0%, #539D6CFF 100%); /* FF3.6-15 */
            background: -webkit-linear-gradient(left, #095516FF 0%, #539d6c 100%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(to right, #095516FF 0%,#539D6CFF 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#207cca', endColorstr='#9f58a3',GradientType=1 ); /* IE6-9 */
        }

        form.ollama button[type=submit] {
            background: -moz-linear-gradient(left, #290955 0%, #53769d 100%); /* FF3.6-15 */
            background: -webkit-linear-gradient(left, #290955 0%, #53769d 100%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(to right, #290955 0%, #53769d 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#207cca', endColorstr='#9f58a3',GradientType=1 ); /* IE6-9 */
        }

        #response .data {
            font-size: 18px;
            margin-top: 20px;
            padding-top: 20px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <form method="post" action="process.php?api=1&evaluate=1">
        <label><h1> Find answer in websites database using LLM</h1></label>
        <br />
        <textarea name="prompt" cols="30" rows="5" placeholder="Is Michał Żarnecki programmer the same person as Michał Żarnecki audio engineer."></textarea>
        <input type="checkbox" name="evaluate" value="1" /><label>Run evaluation</label>
        <br /><br />
        <button type="submit">Generate text</button>
    </form>
    <div id="response">
        <img class="spinner" src="spinner.gif" style="display: none;"/>
        <div class="data"></div>
    </div>
</body>
</html>