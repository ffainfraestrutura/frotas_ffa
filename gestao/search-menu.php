<?php
header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Portal FFA</title>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- vendor jquery js -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="./control/src/style.css" rel="stylesheet" />
</head>
<body>
    <nav id="menu_container" class="p-2 sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <div class="col-sm-3">
            <img id="logo" class="ms-5" src="../src/images/logoffa.png">
        </div>
        <form class="col align-self-sm-center ps-5">
            <div id = "form-container" class="col">
                <div class="row">
                    <div class="w-9 d-flex justify-content-around">
                        <input type="radio" class="d-none" value="driver" id="search_driver" name="search_type" checked></input>
                        <label for="search_driver" class=""><i class="text-custom fa fa-user" aria-hidden="true"></i></label>
                        <input type="radio" class="d-none" value="car" id="search_car" name="search_type"></input>
                        <label for="search_car" class=""><i class="text-custom fa-solid fa-car"></i></label>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <input type="text" id="search-input" autocomplete="off" class="shadow-none col rounded-start border-0 p-1" name="search_input" placeholder="Nome | CNH | CPF | Centro de Custo | Matricula">
                            <button class="col-sm-1 rounded-end border-0 p-1 ms-1"><i class=" fas fa-search"></i></button>
                            <div id="dropdown-container" class="bg-gray pt-1 rounded-bottom position-absolute d-none">
                                <div id="dropdown" class="dropdown">
                                </div>
                            <div id="roll-down" class="row h3 p-2 text-center w-100 d-none"><i class="fa fa-angle-down gray-transparent" aria-hidden="true"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>



    <script>
        mouseLeaveImput = 0;
        mouseLeaveDropdown = 0;
        ["input", "mouseover", "click"].forEach((event) => {
            $("#search-input")[0].addEventListener(event, (e) => {
                mouseLeaveImput = 1;
                formHeight = $("#form-container").height();
                formWidth = $("#search-input").outerWidth();
                $("#search-input").removeClass("rounded-start");
                $("#search-input").addClass("rounded-top");
                $("#dropdown-container").css("transform", "translateY(" + formHeight + "px)");
                $("#dropdown-container").css("width", formWidth);
                $("#dropdown-container").removeClass("d-none");         
            });
        });


        ["input", "mouseover", "click"].forEach((event) => {
            $("#dropdown-container")[0].addEventListener(event, (e) => {
                mouseLeaveDropdown = 1;
            });
        });


        ["mouseout"].forEach((event) => {
            $("#search-input")[0].addEventListener(event, (e) => {
                mouseLeaveImput = 0;
                setTimeout(mouseLeftBoth, 10);
            });
        });

        ["mouseout"].forEach((event) => {
            $("#dropdown-container")[0].addEventListener(event, (e) => {
                mouseLeaveDropdown = 0;
                setTimeout(mouseLeftBoth, 10);
            });
        });


        function mouseLeftBoth(){
            if(mouseLeaveImput == 0 && mouseLeaveDropdown == 0){
                $("#dropdown-container").addClass("d-none");
                $("#search-input").addClass("rounded-start");
                $("#search-input").removeClass("rounded-top");
            }
        }


        $(document).ready(function () {
            $('input:radio[name=search_type]').change(function () {
                if ($("input[name='search_type']:checked").val() == 'car') {
                    $("#search-input")[0].placeholder = "Placa | Renavan | Unidade | Centro de Custo";
                }
                if ($("input[name='search_type']:checked").val() == 'driver') {
                    $("#search-input")[0].placeholder = "Nome | CNH | CPF | Centro de Custo | Matricula";
                }
            });
        });


        let typingTimer;
        const delay = 200; // Tempo de atraso em milissegundos

        $("#search-input")[0].addEventListener("input", (e) => {
            clearTimeout(typingTimer); // Limpa o temporizador anterior, se houver
            typingTimer = setTimeout(() => {
                search(e.target.value);
            }, delay);
        });


        $("#search_driver")[0].addEventListener("click", function(){
            search($("#search-input")[0].value)
        })

        $("#search_car")[0].addEventListener("click", function(){
            search($("#search-input")[0].value)
        })

        function each(el, callback) {
            var allDivs = document.querySelectorAll(el),
                alltoArr = Array.prototype.slice.call(allDivs);
            Array.prototype.forEach.call(alltoArr, function (selector, index) {
                if (callback) return callback(selector);
            });
        };


        function search(value) {
            var dropdown = document.getElementById('dropdown');
                dropdownContainer = document.getElementById('dropdown-container');
                dropdown.innerHTML = ''; // Limpa os resultados anteriores
                $('#roll-down').addClass('d-none');
                if ($("input[name='search_type']:checked").val() == 'car') {
                    var url = 'http://10.17.0.3/frotas/gestao/control/api/veiculo/select/' +  value.replace(/ /g, '+');

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url, true);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                var responseText = xhr.responseText;
                                try {
                                    var response = JSON.parse(responseText);
                                    if (response.status === "sucesso") {
                                        var dados = response.dados;

                                        for (var i = 0; i < dados.length; i++) {
                                            var valor = dados[i];
                                            var oficina = valor.oficina ? '<div class="row mt-4"><i class="fa-solid fa-screwdriver-wrench"></i></div>' : '';

                                            var dropdownitem = document.createElement('div');
                                            dropdownitem.className = "search-item row py-1 shadow-bottom";
                                            dropdownitem.innerHTML = '<form method = "post" class="row" action="./editarcadveiculo.php">'+
                                            '<button class ="row button-search-dropdown" type="submit">'+
                                            '<input type="hidden" name="idtbveiculo" value="'+valor.idtbveiculo+'">'+
                                            '<div class="col-sm-1 w-15 align-self-sm-center text-center overflow-hidden">' +
                                            '<div class="row"><i class="text-custom fa-solid fa-car"></i></div>' +
                                            oficina +
                                            '</div>' +
                                            '<div class="col-sm-8 bl-drop text-center overflow-hidden">' +
                                            '<div class="row h5 ">' +
                                            '<p>' + valor.modelo + '</p>' +
                                            '</div>' +
                                            '<div class="row text-start ps-1">' +
                                            '<p>Placa: ' + valor.placa + '</p>' +
                                            '</div>' +
                                            '<div class="row text-start ps-1">' +
                                            '<p>Renavam: ' + valor.renavam + '</p>' +
                                            '</div>' +
                                            '</div>' +
                                            '<div class="col bl-drop text-center overflow-hidden py-2">' +
                                            '<div class="row">' +
                                            '<p>' + valor.unidade + '</p>' +
                                            '</div>' +
                                            '<div class="row">' +
                                            '<p class="f-size-12">' + valor.ccusto + '</p>' +
                                            '</div>' +
                                            '</div></button></form>';

                                            formWidth = $("#search-input").outerWidth();
                                            dropdownitem.style.width = formWidth + 'px !important';
                                            dropdown.appendChild(dropdownitem);
                                        }
                                        if(dados.length > 1){
                                            $('#roll-down').removeClass('d-none');
                                        }else{
                                            $('#roll-down').addClass('d-none');
                                        }
                                    } else {
                                        console.log('Nenhum veículo encontrado.');
                                    }
                                } catch (error) {
                                    console.log('Erro ao decodificar a resposta JSON: ' + error);
                                }
                            } else {
                                console.log('Erro ao fazer a requisição: ' + xhr.status);
                            }
                        }
                    };

                    xhr.send();
                }
                if ($("input[name='search_type']:checked").val() == 'driver') {
                    // Lógica para busca de motoristas
                    var url = 'http://10.17.0.3/frotas/gestao/control/api/condutor/select/' + value.replace(/ /g, '+');

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url, true);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                var responseText = xhr.responseText;
                                try {
                                    var response = JSON.parse(responseText);
                                    if (response.status === "sucesso") {
                                        var dados = response.dados;

                                        for (var i = 0; i < dados.length; i++) {
                                            var valor = dados[i];

                                            var dropdownitem = document.createElement('div');
                                            dropdownitem.className = "search-item row py-1 shadow-bottom";
                                            dropdownitem.innerHTML = '<form method = "post" class="row" action="./dadoscondutor.php">'+
                                            '<button class ="row button-search-dropdown" type="submit">'+
                                            '<input type="hidden" name="matcond" value="'+valor.matricula+'">'+
                                            '<div class="col-sm-1 w-15 align-self-sm-center overflow-hidden">' +
                                            '<div class="row text-center"><i class="text-custom fa fa-user"></i></div>' +
                                            '</div>' +
                                            '<div class="bl-drop col-sm-8 text-center overflow-hidden">' +
                                            '<div class="row h5">' +
                                            '<p>' + valor.nome + '</p>' +
                                            '</div>' +
                                            '<div class="row text-start ps-1">' +
                                            '<p>Matricula: ' + valor.matricula + '</p>' +
                                            '</div>' +
                                            '<div class="row text-start ps-1">' +
                                            '<p>CNH: ' + valor.cnh + '</p>' +
                                            '</div>' +
                                            '<div class="row text-start ps-1">' +
                                            '<p>CPF: ' + valor.cpf + '</p>' +
                                            '</div>' +
                                            '</div>' +
                                            '<div class="bl-drop col-sm-2 text-center overflow-hidden py-4">' +
                                            '<div class="row">' +
                                            '<p>' + valor.centrocusto + '</p>' +
                                            '</div>'+
                                            '</div></button></form>';

                                            formWidth = $("#search-input").outerWidth();
                                            dropdownitem.style.width = formWidth + 'px !important';
                                            dropdown.appendChild(dropdownitem);
                                        }
                                        if(dados.length > 1){
                                            $('#roll-down').removeClass('d-none')
                                        }else{
                                            $('#roll-down').addClass('d-none')
                                        }
                                    } else {
                                        console.log('Nenhum veículo encontrado.');
                                    }
                                } catch (error) {
                                    console.log('Erro ao decodificar a resposta JSON: ' + error);
                                }
                            } else {
                                console.log('Erro ao fazer a requisição: ' + xhr.status);
                            }
                        }
                    };

                    xhr.send();
                }
            }   
    var scrollInterval;
    var dropdownElement = document.getElementById('dropdown');

    $('#roll-down')[0].addEventListener('mouseenter', function() {
        scrollInterval = setInterval(function() {
            dropdownElement.scrollTop += 1; // Rolar o elemento "dropdown" em 10 pixels para baixo
        }, 8);
    });

    $('#roll-down')[0].addEventListener('mouseleave', function() {
    clearInterval(scrollInterval);
    });
    </script>
</body>