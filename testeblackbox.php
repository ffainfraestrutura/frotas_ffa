<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!--Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <!--Datatable CSS-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="container">
    <h1>Filtrar Colunas</h1>
    <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Name <button type="button" class="btn btn-secondary btn-sm">Filtrar</button></th>
                <th>Position <button type="button" class="btn btn-secondary btn-sm">Filtrar</button></th>
                <th>Office <button type="button" class="btn btn-secondary btn-sm">Filtrar</button></th>
                <th>Age <button type="button" class="btn btn-secondary btn-sm">Filtrar</button></th>
                <th>Start date <button type="button" class="btn btn-secondary btn-sm">Filtrar</button></th>
                <th>Salary <button type="button" class="btn btn-secondary btn-sm">Filtrar</button></th>
            </tr>
        </thead>
            <tbody>
                <tr>
                    <td>Tiger Nixon</td>
                    <td>System Architect</td>
                    <td>Edinburgh</td>
                    <td>61</td>
                    <td>2011/04/25</td>
                    <td>$320,800</td>
                </tr>
                <tr>
                    <td>Garrett Winters</td>
                    <td>Accountant</td>
                    <td>Tokyo</td>
                    <td>63</td>
                    <td>2011/07/25</td>
                    <td>$170,750</td>
                </tr>
                <!--Insira mais registros conforme necessário-->
            </tbody>
        </table>
    </div>

    <!--jQuery-->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <!--Datatable JS-->
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
    var table = $('#example').DataTable();

    // Adicionar o evento de clique aos botões "Filtrar"
    $('#example thead button').on('click', function () {
        var columnIndex = $(this).parent().index(); // Obtém o índice da coluna ao qual o botão pertence
        var columnValue = prompt('Digite o valor para filtrar a coluna:');

        // Verificar se o valor inserido é diferente de vazio
        if (columnValue !== '') {
            // Filtrar a coluna
            table.column(columnIndex).search(columnValue).draw();
        } else {
            // Limpar o filtro
            table.column(columnIndex).search('').draw();
        }
    });
});
    </script>
</body>
</html>